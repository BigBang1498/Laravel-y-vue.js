<?php

namespace Tests\Feature;

use App\Enums\DocumentDirection;
use App\Enums\DocumentStatus;
use App\Models\Assignment;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentHistory;
use App\Models\DocumentType;
use App\Models\User;
use Tests\TestCase;

class AssignmentAndTrackingTest extends TestCase
{
    private function createTestDocument(Department $dept, User $creator, string $folioSuffix): Document
    {
        return Document::create([
            'direction' => DocumentDirection::RECEIVED->value,
            'folio' => 'TEST-DOC-' . $folioSuffix . '-' . uniqid(),
            'external_number' => 'EXT-' . uniqid(),
            'document_type_id' => DocumentType::first()->id,
            'sender_name' => 'Remitente Prueba',
            'recipient_name' => 'Destinatario Prueba',
            'subject' => 'Asunto de Prueba para Asignaciones',
            'document_date' => today(),
            'received_or_sent_at' => now(),
            'department_id' => $dept->id,
            'created_by' => $creator->id,
            'priority' => 'high',
            'status' => DocumentStatus::RECEIVED->value,
            'due_date' => today()->addDays(7),
        ]);
    }

    public function test_authorized_user_can_assign_document(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $sistemas = Department::where('code', 'SIS')->first();
        $empleadoSistemas = User::where('email', 'empleado_sistemas@universidad.edu')->first();
        $doc = $this->createTestDocument($sistemas, $admin, 'ASG');

        $response = $this->actingAs($admin)->post(route('documents.assignments.store', $doc), [
            'assigned_to_department_id' => $sistemas->id,
            'assigned_to_user_id' => $empleadoSistemas->id,
            'due_date' => date('Y-m-d', strtotime('+7 days')),
            'instructions' => 'Atender de manera prioritaria el presente oficio.',
        ]);

        $response->assertRedirect(route('documents.show', $doc));

        $this->assertDatabaseHas('assignments', [
            'document_id' => $doc->id,
            'assigned_to_department_id' => $sistemas->id,
            'assigned_to_user_id' => $empleadoSistemas->id,
            'is_current' => true,
        ]);

        $this->assertDatabaseHas('documents', [
            'id' => $doc->id,
            'department_id' => $sistemas->id,
            'current_assigned_user_id' => $empleadoSistemas->id,
        ]);

        $this->assertDatabaseHas('document_history', [
            'document_id' => $doc->id,
            'action' => 'ASIGNACION',
        ]);
    }

    public function test_reassigning_document_marks_previous_assignment_inactive(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $sistemas = Department::where('code', 'SIS')->first();
        $finanzas = Department::where('code', 'FIN')->first();
        $empleadoSistemas = User::where('email', 'empleado_sistemas@universidad.edu')->first();
        $empleadoFinanzas = User::where('email', 'empleado_finanzas@universidad.edu')->first();

        $doc = $this->createTestDocument($sistemas, $admin, 'REASG');

        $firstAssignment = Assignment::create([
            'document_id' => $doc->id,
            'assigned_by' => $admin->id,
            'assigned_to_department_id' => $sistemas->id,
            'assigned_to_user_id' => $empleadoSistemas->id,
            'assigned_at' => now()->subDay(),
            'due_date' => today()->addDays(5),
            'instructions' => 'Asignación inicial.',
            'is_current' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('documents.assignments.store', $doc), [
            'assigned_to_department_id' => $finanzas->id,
            'assigned_to_user_id' => $empleadoFinanzas->id,
            'due_date' => date('Y-m-d', strtotime('+5 days')),
            'instructions' => 'Turnado a Finanzas para dictamen de presupuesto.',
        ]);

        $response->assertRedirect(route('documents.show', $doc));

        $this->assertFalse((bool) $firstAssignment->fresh()->is_current);

        $newAssignment = $doc->fresh()->currentAssignment;
        $this->assertTrue((bool) $newAssignment->is_current);
        $this->assertEquals($finanzas->id, $newAssignment->assigned_to_department_id);
        $this->assertEquals($empleadoFinanzas->id, $newAssignment->assigned_to_user_id);

        $this->assertDatabaseHas('document_history', [
            'document_id' => $doc->id,
            'action' => 'REASIGNACION',
        ]);
    }

    public function test_unauthorized_user_cannot_assign_document(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $consultaUser = User::where('email', 'consulta@universidad.edu')->first();
        $sistemas = Department::where('code', 'SIS')->first();
        $doc = $this->createTestDocument($sistemas, $admin, 'UNAUTH');

        $response = $this->actingAs($consultaUser)->post(route('documents.assignments.store', $doc), [
            'assigned_to_department_id' => $sistemas->id,
            'instructions' => 'Intento no autorizado.',
        ]);

        $response->assertStatus(403);
    }

    public function test_my_documents_view_renders_for_assigned_user(): void
    {
        $empleadoSistemas = User::where('email', 'empleado_sistemas@universidad.edu')->first();

        $response = $this->actingAs($empleadoSistemas)->get(route('tracking.my-documents'));
        $response->assertStatus(200);
        $response->assertSee('Mis Documentos Asignados');
    }

    public function test_pending_tracking_view_renders_correctly(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();

        $response = $this->actingAs($admin)->get(route('tracking.pending'));
        $response->assertStatus(200);
        $response->assertSee('Correspondencia en Trámite y Pendientes');
    }

    public function test_expired_tracking_view_renders_correctly(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();

        $response = $this->actingAs($admin)->get(route('tracking.expired'));
        $response->assertStatus(200);
        $response->assertSee('Documentos Vencidos');
        $response->assertSee('UNI-2026-000003');
    }

    public function test_check_expired_documents_artisan_command(): void
    {
        $this->artisan('documents:check-expired')
            ->assertExitCode(0);
    }

    public function test_get_users_by_department_json_endpoint(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $sistemas = Department::where('code', 'SIS')->first();

        $response = $this->actingAs($admin)->getJson(route('departments.users', $sistemas));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'email' => 'jefe_sistemas@universidad.edu',
        ]);
        $response->assertJsonFragment([
            'email' => 'empleado_sistemas@universidad.edu',
        ]);
    }

    public function test_document_history_records_audit_trail_on_status_change(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $sistemas = Department::where('code', 'SIS')->first();
        $doc = $this->createTestDocument($sistemas, $admin, 'STATUS');

        $this->actingAs($admin)->patch(route('documents.change-status', $doc), [
            'status' => DocumentStatus::IN_PROCESS->value,
            'observations' => 'Iniciando trámite y dictamen formal.',
        ]);

        $this->assertDatabaseHas('document_history', [
            'document_id' => $doc->id,
            'action' => 'CAMBIO_ESTADO',
            'new_status' => DocumentStatus::IN_PROCESS->value,
        ]);
    }
}

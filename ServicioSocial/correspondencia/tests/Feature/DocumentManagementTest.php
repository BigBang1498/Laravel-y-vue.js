<?php

namespace Tests\Feature;

use App\Enums\DocumentDirection;
use App\Enums\DocumentStatus;
use App\Enums\Priority;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use App\Services\FolioGeneratorService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DocumentManagementTest extends TestCase
{
    public function test_folio_generator_service_increments_consecutively_without_duplicates(): void
    {
        $service = app(FolioGeneratorService::class);
        $year = (int) date('Y');

        $currentRec = DB::table('folio_sequences')->where('prefix', 'UNI')->where('year', $year)->value('current_number') ?? 0;
        $folio1 = $service->generateNextFolio(DocumentDirection::RECEIVED);
        $folio2 = $service->generateNextFolio(DocumentDirection::RECEIVED);

        $expectedNum1 = str_pad((string)($currentRec + 1), 6, '0', STR_PAD_LEFT);
        $expectedNum2 = str_pad((string)($currentRec + 2), 6, '0', STR_PAD_LEFT);

        $this->assertEquals("UNI-{$year}-{$expectedNum1}", $folio1);
        $this->assertEquals("UNI-{$year}-{$expectedNum2}", $folio2);

        $currentSent = DB::table('folio_sequences')->where('prefix', 'UNI-SAL')->where('year', $year)->value('current_number') ?? 0;
        $sentFolio = $service->generateNextFolio(DocumentDirection::SENT);
        $expectedSentNum = str_pad((string)($currentSent + 1), 6, '0', STR_PAD_LEFT);

        $this->assertEquals("UNI-SAL-{$year}-{$expectedSentNum}", $sentFolio);
    }

    public function test_reception_can_view_received_documents_index(): void
    {
        $recepcion = User::where('email', 'recepcion@universidad.edu')->first();

        $response = $this->actingAs($recepcion)->get(route('received-documents.index'));
        $response->assertStatus(200);
        $response->assertSee('Correspondencia Recibida');
    }

    public function test_reception_can_register_received_document(): void
    {
        $recepcion = User::where('email', 'recepcion@universidad.edu')->first();
        $dept = Department::where('code', 'SIS')->first();
        $type = DocumentType::where('code', 'OF')->first();
        $extNum = 'TEST/EXT/' . uniqid();

        $response = $this->actingAs($recepcion)->post(route('received-documents.store'), [
            'external_number' => $extNum,
            'document_type_id' => $type->id,
            'sender_name' => 'Lic. Pedro Ramírez',
            'sender_institution' => 'Gobierno Municipal',
            'recipient_name' => 'Rectoría Universitaria',
            'recipient_institution' => 'Universidad',
            'subject' => 'Convenio de Colaboración para Servicio Social',
            'description' => 'Propuesta de firma de convenio con la dirección de tecnologías.',
            'document_date' => date('Y-m-d'),
            'received_or_sent_at' => date('Y-m-d\TH:i'),
            'department_id' => $dept->id,
            'priority' => 'high',
            'due_date' => date('Y-m-d', strtotime('+10 days')),
            'security_level' => 'public',
            'observations' => 'Entregado en mano en ventanilla.',
        ]);

        $this->assertDatabaseHas('documents', [
            'external_number' => $extNum,
            'subject' => 'Convenio de Colaboración para Servicio Social',
            'priority' => 'high',
            'direction' => DocumentDirection::RECEIVED->value,
        ]);

        $doc = Document::where('external_number', $extNum)->first();
        $response->assertRedirect(route('documents.show', $doc));
    }

    public function test_user_can_register_sent_document(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $dept = Department::where('code', 'REC')->first();
        $type = DocumentType::where('code', 'OF')->first();
        $extNum = 'REC/SAL/TEST/' . uniqid();

        $response = $this->actingAs($admin)->post(route('sent-documents.store'), [
            'external_number' => $extNum,
            'document_type_id' => $type->id,
            'sender_name' => $admin->name,
            'sender_institution' => 'Universidad',
            'recipient_name' => 'Secretario de Educación',
            'recipient_institution' => 'SEP',
            'subject' => 'Envío de Matrícula Consolidada',
            'description' => 'Despacho de informe final de estudiantes inscritos.',
            'document_date' => date('Y-m-d'),
            'received_or_sent_at' => date('Y-m-d\TH:i'),
            'department_id' => $dept->id,
            'priority' => 'urgent',
            'security_level' => 'public',
        ]);

        $this->assertDatabaseHas('documents', [
            'external_number' => $extNum,
            'direction' => DocumentDirection::SENT->value,
            'priority' => 'urgent',
        ]);
    }

    public function test_valid_status_transition_is_permitted(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $doc = Document::where('folio', 'UNI-2026-000001')->first();

        $response = $this->actingAs($admin)->patch(route('documents.change-status', $doc), [
            'status' => DocumentStatus::UNDER_REVIEW->value,
            'observations' => 'Documento turnado a revisión preliminar.',
        ]);

        $response->assertRedirect(route('documents.show', $doc));
        $this->assertEquals(DocumentStatus::UNDER_REVIEW, $doc->fresh()->status);
    }

    public function test_invalid_status_transition_is_rejected_for_non_admin(): void
    {
        $jefeSistemas = User::where('email', 'jefe_sistemas@universidad.edu')->first();
        $doc = Document::where('folio', 'UNI-2026-000002')->first();

        $this->assertEquals(DocumentStatus::IN_PROCESS, $doc->status);

        $response = $this->actingAs($jefeSistemas)->patch(route('documents.change-status', $doc), [
            'status' => DocumentStatus::RECEIVED->value,
        ]);

        $response->assertSessionHasErrors('status');
    }

    public function test_traffic_light_calculation(): void
    {
        $docGreen = Document::where('folio', 'UNI-2026-000001')->first();
        $this->assertEquals('green', $docGreen->traffic_light);

        $docYellow = Document::where('folio', 'UNI-2026-000002')->first();
        $this->assertEquals('yellow', $docYellow->traffic_light);

        $docRed = Document::where('folio', 'UNI-2026-000003')->first();
        $this->assertEquals('red', $docRed->traffic_light);
    }
}

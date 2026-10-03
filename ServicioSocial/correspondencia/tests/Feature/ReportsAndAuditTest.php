<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Document;
use App\Models\User;
use App\Services\AuditService;
use Tests\TestCase;

class ReportsAndAuditTest extends TestCase
{
    public function test_authorized_user_can_access_reports_index(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();

        $response = $this->actingAs($admin)->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Centro de Reportes y Estadísticas');
        $response->assertSee('Exportar a Excel / CSV');
        $response->assertSee('Descargar Reporte PDF');
    }

    public function test_unauthorized_user_cannot_access_reports(): void
    {
        $empleado = User::where('email', 'empleado_sistemas@universidad.edu')->first();

        $response = $this->actingAs($empleado)->get(route('reports.index'));

        $response->assertStatus(403);
    }

    public function test_authorized_user_can_export_csv(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();

        $response = $this->actingAs($admin)->get(route('reports.export-csv'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $this->assertStringContainsString('attachment', $response->headers->get('content-disposition'));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'event' => 'exported_report',
        ]);
    }

    public function test_authorized_user_can_export_pdf_summary(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();

        $response = $this->actingAs($admin)->get(route('reports.export-pdf'));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }

    public function test_user_can_view_document_slip_pdf(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $doc = Document::first();
        $this->assertNotNull($doc);

        $response = $this->actingAs($admin)->get(route('documents.slip', $doc));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'event' => 'exported_report',
            'auditable_id' => $doc->id,
        ]);
    }

    public function test_user_can_view_printable_document_slip(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $doc = Document::first();
        $this->assertNotNull($doc);

        $response = $this->actingAs($admin)->get(route('documents.print-slip', $doc));

        $response->assertStatus(200);
        $response->assertSee($doc->folio);
        $response->assertSee('Boleta Oficial de Radicación');
    }

    public function test_admin_can_view_audit_logs_index(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();

        $response = $this->actingAs($admin)->get(route('audit-logs.index'));

        $response->assertStatus(200);
        $response->assertSee('Bitácora de Auditoría Forense');
    }

    public function test_non_admin_cannot_access_audit_logs(): void
    {
        $secretaria = User::where('email', 'secretaria@universidad.edu')->first();

        $response = $this->actingAs($secretaria)->get(route('audit-logs.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_view_single_audit_log_inspection(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $sistemas = Department::where('code', 'SIS')->first();

        $log = AuditService::log(
            'updated',
            'Prueba de modificacion forense para test',
            $sistemas,
            ['name' => 'Nombre Previo'],
            ['name' => 'Nombre Nuevo'],
            $admin
        );

        $response = $this->actingAs($admin)->get(route('audit-logs.show', $log));

        $response->assertStatus(200);
        $response->assertSee('Inspección Forense');
        $response->assertSee('Nombre Previo');
        $response->assertSee('Nombre Nuevo');
    }

    public function test_login_and_logout_creates_audit_log(): void
    {
        $loginResponse = $this->post(route('login.post'), [
            'email' => 'admin@universidad.edu',
            'password' => 'password123',
        ]);

        $loginResponse->assertRedirect(route('dashboard'));

        $admin = User::where('email', 'admin@universidad.edu')->first();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'event' => 'login',
        ]);

        $logoutResponse = $this->actingAs($admin)->post(route('logout'));
        $logoutResponse->assertRedirect(route('login'));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'event' => 'logout',
        ]);
    }
}

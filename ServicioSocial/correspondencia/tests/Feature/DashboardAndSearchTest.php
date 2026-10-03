<?php

namespace Tests\Feature;

use App\Enums\DocumentDirection;
use App\Enums\DocumentStatus;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use App\Notifications\DocumentAssignedNotification;
use App\Notifications\DocumentDueSoonNotification;
use App\Notifications\DocumentStatusChangedNotification;
use Tests\TestCase;

class DashboardAndSearchTest extends TestCase
{
    private function createTestDocument(Department $dept, User $creator, string $suffix, array $attributes = []): Document
    {
        return Document::create(array_merge([
            'direction' => DocumentDirection::RECEIVED->value,
            'folio' => 'TEST-P6-' . $suffix . '-' . uniqid(),
            'external_number' => 'EXT-P6-' . uniqid(),
            'document_type_id' => DocumentType::first()->id,
            'sender_name' => 'Remitente P6',
            'recipient_name' => 'Destinatario P6',
            'subject' => 'Asunto de Prueba Fase 6',
            'document_date' => today(),
            'received_or_sent_at' => now(),
            'department_id' => $dept->id,
            'created_by' => $creator->id,
            'priority' => 'normal',
            'status' => DocumentStatus::RECEIVED->value,
        ], $attributes));
    }

    public function test_user_can_view_notifications_index(): void
    {
        $user = User::where('email', 'empleado_sistemas@universidad.edu')->first();
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $sistemas = Department::where('code', 'SIS')->first();
        $doc = $this->createTestDocument($sistemas, $admin, 'NOTIF');

        $user->notify(new DocumentAssignedNotification($doc, $admin));

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Notificaciones del Sistema');
        $response->assertSee($doc->folio);
    }

    public function test_user_can_mark_single_notification_as_read(): void
    {
        $user = User::where('email', 'empleado_sistemas@universidad.edu')->first();
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $sistemas = Department::where('code', 'SIS')->first();
        $doc = $this->createTestDocument($sistemas, $admin, 'READ1');

        $user->notify(new DocumentStatusChangedNotification($doc, DocumentStatus::IN_PROCESS));

        $notification = $user->unreadNotifications()->first();
        $this->assertNotNull($notification);

        $response = $this->actingAs($user)->post(route('notifications.read', $notification->id));

        $response->assertRedirect();
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::where('email', 'empleado_sistemas@universidad.edu')->first();
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $sistemas = Department::where('code', 'SIS')->first();
        $doc = $this->createTestDocument($sistemas, $admin, 'READALL');

        $user->notify(new DocumentDueSoonNotification($doc));
        $user->notify(new DocumentAssignedNotification($doc, $admin));

        $this->assertGreaterThanOrEqual(2, $user->unreadNotifications()->count());

        $response = $this->actingAs($user)->post(route('notifications.mark-all-read'));

        $response->assertRedirect();
        $this->assertEquals(0, $user->unreadNotifications()->count());
    }

    public function test_unread_count_json_endpoint(): void
    {
        $user = User::where('email', 'secretaria@universidad.edu')->first();

        $response = $this->actingAs($user)->get(route('notifications.unread-count'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['unread_count']);
    }

    public function test_admin_dashboard_loads_successfully(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Panel de Control');
        $response->assertSee('Recibidos');
        $response->assertSee('Vencidos');
    }

    public function test_department_head_dashboard_loads_successfully(): void
    {
        $jefe = User::where('email', 'jefe_sistemas@universidad.edu')->first();

        $response = $this->actingAs($jefe)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Panel de Control');
    }

    public function test_employee_dashboard_loads_successfully(): void
    {
        $empleado = User::where('email', 'empleado_sistemas@universidad.edu')->first();

        $response = $this->actingAs($empleado)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Panel de Control');
    }

    public function test_advanced_search_can_filter_by_keyword(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $sistemas = Department::where('code', 'SIS')->first();

        $uniqueSubject = 'ClaveBusquedaUnica' . uniqid();
        $doc = $this->createTestDocument($sistemas, $admin, 'SEARCH', [
            'subject' => $uniqueSubject,
        ]);

        $response = $this->actingAs($admin)->get(route('search.advanced', [
            'q' => $uniqueSubject,
        ]));

        $response->assertStatus(200);
        $response->assertSee($uniqueSubject);
        $response->assertSee($doc->folio);
    }

    public function test_advanced_search_can_filter_by_department_and_status(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $sistemas = Department::where('code', 'SIS')->first();

        $doc = $this->createTestDocument($sistemas, $admin, 'DEPSTAT', [
            'status' => DocumentStatus::IN_PROCESS->value,
        ]);

        $response = $this->actingAs($admin)->get(route('search.advanced', [
            'department_id' => $sistemas->id,
            'status' => DocumentStatus::IN_PROCESS->value,
        ]));

        $response->assertStatus(200);
        $response->assertSee($doc->folio);
    }
}

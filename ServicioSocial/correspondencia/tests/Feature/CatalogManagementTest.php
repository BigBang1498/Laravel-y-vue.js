<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\DocumentType;
use App\Models\Recipient;
use App\Models\Sender;
use App\Models\User;
use Tests\TestCase;

class CatalogManagementTest extends TestCase
{
    public function test_admin_can_view_departments_list(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $firstDept = Department::orderBy('name')->first();

        $response = $this->actingAs($admin)->get(route('departments.index'));
        $response->assertStatus(200);
        $response->assertSee('Departamentos y Dependencias');
        if ($firstDept) {
            $response->assertSee($firstDept->name);
        }
    }

    public function test_admin_can_create_a_department(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $code = 'F' . rand(100, 999);
        $name = 'Facultad de Ingeniería ' . rand(10, 99);

        $response = $this->actingAs($admin)->post(route('departments.store'), [
            'name' => $name,
            'code' => $code,
            'description' => 'Área de ciencias exactas y desarrollo tecnológico.',
            'phone' => 'Ext. 6000',
            'email' => strtolower($code) . '@universidad.edu',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('departments.index'));
        $this->assertDatabaseHas('departments', ['code' => $code, 'name' => $name]);
    }

    public function test_admin_cannot_delete_department_with_assigned_users(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $rectoria = Department::where('code', 'REC')->first();

        $this->assertTrue($rectoria->users()->count() > 0);

        $response = $this->actingAs($admin)->delete(route('departments.destroy', $rectoria));
        $response->assertSessionHas('error');
        $this->assertNotSoftDeleted('departments', ['id' => $rectoria->id]);
    }

    public function test_admin_can_view_document_types_list(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();

        $response = $this->actingAs($admin)->get(route('document-types.index'));
        $response->assertStatus(200);
        $response->assertSee('Oficio');
        $response->assertSee('Memorándum');
        $response->assertSee('Circular');
    }

    public function test_admin_can_create_document_type(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();
        $code = 'D' . rand(10, 99);
        $name = 'Dictamen Técnico ' . rand(100, 999);

        $response = $this->actingAs($admin)->post(route('document-types.store'), [
            'name' => $name,
            'code' => $code,
            'description' => 'Resolución técnica de comités evaluadores.',
            'default_days_to_expire' => 12,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('document-types.index'));
        $this->assertDatabaseHas('document_types', ['code' => $code, 'name' => $name]);
    }

    public function test_reception_can_view_and_create_senders(): void
    {
        $recepcion = User::where('email', 'recepcion@universidad.edu')->first();
        $senderName = 'Ing. Roberto Silva ' . rand(100, 999);

        $response = $this->actingAs($recepcion)->get(route('senders.index'));
        $response->assertStatus(200);

        $storeResponse = $this->actingAs($recepcion)->post(route('senders.store'), [
            'name' => $senderName,
            'institution' => 'Cámara de Comercio',
            'position' => 'Presidente',
            'email' => 'rsilva' . rand(10, 99) . '@camaracomercio.org',
            'is_active' => true,
        ]);

        $storeResponse->assertRedirect(route('senders.index'));
        $this->assertDatabaseHas('senders', ['name' => $senderName]);
    }

    public function test_reception_can_view_and_create_recipients(): void
    {
        $recepcion = User::where('email', 'recepcion@universidad.edu')->first();
        $recipientName = 'Lic. Patricia Alarcón ' . rand(100, 999);

        $response = $this->actingAs($recepcion)->get(route('recipients.index'));
        $response->assertStatus(200);

        $storeResponse = $this->actingAs($recepcion)->post(route('recipients.store'), [
            'name' => $recipientName,
            'institution' => 'Tribunal Universitario',
            'position' => 'Magistrada Presidenta',
            'email' => 'palarcon' . rand(10, 99) . '@tribunal.gob.mx',
            'is_active' => true,
        ]);

        $storeResponse->assertRedirect(route('recipients.index'));
        $this->assertDatabaseHas('recipients', ['name' => $recipientName]);
    }

    public function test_users_are_associated_with_departments(): void
    {
        $jefe = User::where('email', 'jefe_sistemas@universidad.edu')->first();
        $this->assertNotNull($jefe->department);
        $this->assertEquals('SIS', $jefe->department->code);
    }

    public function test_unauthorized_user_cannot_access_department_management(): void
    {
        $empleado = User::where('email', 'empleado_sistemas@universidad.edu')->first();

        $response = $this->actingAs($empleado)->get(route('departments.index'));
        $response->assertStatus(403);
    }
}

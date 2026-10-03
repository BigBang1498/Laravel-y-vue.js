<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class SecurityAndPerformanceTest extends TestCase
{
    public function test_security_headers_are_present_in_responses(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertStringContainsString('camera=()', $response->headers->get('Permissions-Policy'));
    }

    public function test_deactivated_user_is_forcefully_logged_out(): void
    {
        $user = User::where('email', 'empleado_finanzas@universidad.edu')->first();
        $this->assertNotNull($user);

        $user->update(['is_active' => false]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();

        $user->update(['is_active' => true]);
    }

    public function test_system_health_artisan_command(): void
    {
        $this->artisan('app:system-health')
            ->expectsOutputToContain('DIAGNÓSTICO DE SALUD DEL SISTEMA INSTITUCIONAL')
            ->assertExitCode(0);
    }

    public function test_verify_attachments_integrity_artisan_command(): void
    {
        $this->artisan('app:verify-attachments-integrity')
            ->assertExitCode(0);
    }

    public function test_custom_403_view_renders_for_unauthorized_access(): void
    {
        $empleado = User::where('email', 'empleado_sistemas@universidad.edu')->first();

        $response = $this->actingAs($empleado)->get(route('users.index'));

        $response->assertStatus(403);
        $response->assertSee('Acceso No Autorizado');
        $response->assertSee('403');
    }

    public function test_custom_404_view_renders_for_missing_model(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();

        $response = $this->actingAs($admin)->get('/documents/999999');

        $response->assertStatus(404);
        $response->assertSee('Recurso No Encontrado');
        $response->assertSee('404');
    }
}

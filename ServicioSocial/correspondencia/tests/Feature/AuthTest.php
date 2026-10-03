<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_root_redirects_to_login_for_guests(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('dashboard'));

        $dashboardResponse = $this->get(route('dashboard'));
        $dashboardResponse->assertRedirect(route('login'));
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('Correspondencia Universitaria');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'admin@universidad.edu',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'admin@universidad.edu',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::where('email', 'admin@universidad.edu')->first();

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Panel de Control');
    }

    public function test_admin_can_view_users_list(): void
    {
        $admin = User::where('email', 'admin@universidad.edu')->first();

        $response = $this->actingAs($admin)->get(route('users.index'));
        $response->assertStatus(200);
        $response->assertSee('Usuarios del Sistema');
    }

    public function test_non_admin_cannot_access_users_list(): void
    {
        $empleado = User::where('email', 'empleado_sistemas@universidad.edu')->first();

        $response = $this->actingAs($empleado)->get(route('users.index'));
        $response->assertStatus(403);
    }
}

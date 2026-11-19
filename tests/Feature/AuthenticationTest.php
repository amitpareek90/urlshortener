<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_seeded_super_admin_can_log_in_and_log_out(): void
    {
        $this->seed(SuperAdminSeeder::class);

        $this->post('/login', [
            'email' => 'superadmin@example.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'superadmin@example.com',
            'role' => User::ROLE_SUPER_ADMIN,
            'company_id' => null,
        ]);

        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_login_rejects_unknown_credentials(): void
    {
        $this->post('/login', [
            'email' => 'missing@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}

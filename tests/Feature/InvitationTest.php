<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_invites_an_admin_into_a_new_company(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->post('/invitations', [
            'company_name' => 'Northwind',
            'name' => 'Ada Admin',
            'email' => 'ada@northwind.test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('invitations.create'));

        $company = Company::query()->where('name', 'Northwind')->first();
        $this->assertNotNull($company);

        $this->assertDatabaseHas('users', [
            'email' => 'ada@northwind.test',
            'role' => User::ROLE_ADMIN,
            'company_id' => $company->id,
        ]);

        $this->post('/logout');
        $this->post('/login', [
            'email' => 'ada@northwind.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_admin_invites_a_member_into_their_own_company(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'company_id' => $company->id,
        ]);

        $this->actingAs($admin)->post('/invitations', [
            'name' => 'Mo Member',
            'email' => 'mo@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::ROLE_MEMBER,
        ])->assertRedirect(route('invitations.create'));

        $this->assertDatabaseHas('users', [
            'email' => 'mo@example.test',
            'role' => User::ROLE_MEMBER,
            'company_id' => $company->id,
        ]);
    }

    public function test_admin_cannot_invite_a_super_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/invitations', [
            'name' => 'Nope',
            'email' => 'nope@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::ROLE_SUPER_ADMIN,
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'nope@example.test']);
    }

    public function test_member_cannot_invite_users(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)->get('/invitations/create')->assertForbidden();
        $this->actingAs($member)->post('/invitations', [
            'name' => 'Nope',
            'email' => 'nope@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::ROLE_MEMBER,
        ])->assertForbidden();
    }
}

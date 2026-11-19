<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_member_can_create_short_urls(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'company_id' => $company->id,
        ]);
        $member = User::factory()->create([
            'role' => User::ROLE_MEMBER,
            'company_id' => $company->id,
        ]);

        $this->actingAs($admin)->post('/short-urls', [
            'original_url' => 'https://admin.example/docs',
        ])->assertRedirect(route('dashboard'));

        $this->actingAs($member)->post('/short-urls', [
            'original_url' => 'https://member.example/docs',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('short_urls', [
            'original_url' => 'https://admin.example/docs',
            'user_id' => $admin->id,
            'company_id' => $company->id,
        ]);
        $this->assertDatabaseHas('short_urls', [
            'original_url' => 'https://member.example/docs',
            'user_id' => $member->id,
            'company_id' => $company->id,
        ]);
    }

    public function test_super_admin_cannot_create_short_urls(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->get('/short-urls/create')->assertForbidden();
        $this->actingAs($superAdmin)->post('/short-urls', [
            'original_url' => 'https://example.com',
        ])->assertForbidden();

        $this->assertDatabaseCount('short_urls', 0);
    }

    public function test_invalid_original_url_is_rejected(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)->post('/short-urls', [
            'original_url' => 'not-a-url',
        ])->assertSessionHasErrors('original_url');
    }

    public function test_admin_sees_only_short_urls_from_their_company(): void
    {
        $company = Company::factory()->create();
        $otherCompany = Company::factory()->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'company_id' => $company->id,
        ]);
        $member = User::factory()->create([
            'role' => User::ROLE_MEMBER,
            'company_id' => $company->id,
        ]);

        ShortUrl::factory()->create([
            'original_url' => 'https://own-company.example/page',
            'user_id' => $member->id,
            'company_id' => $company->id,
        ]);
        ShortUrl::factory()->create([
            'original_url' => 'https://other-company.example/page',
            'company_id' => $otherCompany->id,
            'user_id' => User::factory()->create([
                'role' => User::ROLE_MEMBER,
                'company_id' => $otherCompany->id,
            ])->id,
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('https://own-company.example/page')
            ->assertDontSee('https://other-company.example/page');
    }

    public function test_member_sees_only_short_urls_they_created(): void
    {
        $company = Company::factory()->create();
        $member = User::factory()->create([
            'role' => User::ROLE_MEMBER,
            'company_id' => $company->id,
        ]);
        $colleague = User::factory()->create([
            'role' => User::ROLE_MEMBER,
            'company_id' => $company->id,
        ]);

        ShortUrl::factory()->create([
            'original_url' => 'https://mine.example/page',
            'user_id' => $member->id,
            'company_id' => $company->id,
        ]);
        ShortUrl::factory()->create([
            'original_url' => 'https://colleague.example/page',
            'user_id' => $colleague->id,
            'company_id' => $company->id,
        ]);

        $this->actingAs($member)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('https://mine.example/page')
            ->assertDontSee('https://colleague.example/page');
    }

    public function test_super_admin_sees_short_urls_from_every_company(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        ShortUrl::factory()->create(['original_url' => 'https://one.example/page']);
        ShortUrl::factory()->create(['original_url' => 'https://two.example/page']);

        $this->actingAs($superAdmin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('https://one.example/page')
            ->assertSee('https://two.example/page')
            ->assertDontSee('Create short URL');
    }

    public function test_short_urls_are_publicly_resolvable(): void
    {
        $shortUrl = ShortUrl::factory()->create([
            'code' => 'abc123',
            'original_url' => 'https://destination.example/hello',
        ]);

        $this->get('/'.$shortUrl->code)
            ->assertRedirect('https://destination.example/hello');
    }

    public function test_unknown_short_code_returns_not_found(): void
    {
        $this->get('/zzzzzz')->assertNotFound();
    }
}

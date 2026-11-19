<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'role' => User::ROLE_MEMBER,
            'company_id' => null,
            'remember_token' => null,
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn () => [
            'role' => User::ROLE_SUPER_ADMIN,
            'company_id' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => User::ROLE_ADMIN,
            'company_id' => Company::factory(),
        ]);
    }

    public function member(): static
    {
        return $this->state(fn () => [
            'role' => User::ROLE_MEMBER,
            'company_id' => Company::factory(),
        ]);
    }
}

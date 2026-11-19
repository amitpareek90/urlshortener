<?php

namespace Database\Factories;

use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ShortUrl>
 */
class ShortUrlFactory extends Factory
{
    protected $model = ShortUrl::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => Str::lower(Str::random(ShortUrl::CODE_LENGTH)),
            'original_url' => fake()->url(),
            'user_id' => User::factory()->member(),
            'company_id' => function (array $attributes) {
                return User::query()->findOrFail($attributes['user_id'])->company_id;
            },
        ];
    }
}

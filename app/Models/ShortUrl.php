<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use RuntimeException;

class ShortUrl extends Model
{
    use HasFactory;

    public const CODE_LENGTH = 6;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'original_url',
        'user_id',
        'company_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        if ($user->isAdmin()) {
            return $query->where('company_id', $user->company_id);
        }

        return $query->where('user_id', $user->id);
    }

    public static function generateUniqueCode(): string
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $code = Str::lower(Str::random(self::CODE_LENGTH));

            if (! static::query()->where('code', $code)->exists()) {
                return $code;
            }
        }

        throw new RuntimeException('Could not generate a unique short code.');
    }
}

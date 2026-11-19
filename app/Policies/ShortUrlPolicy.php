<?php

namespace App\Policies;

use App\Models\ShortUrl;
use App\Models\User;

class ShortUrlPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->canCreateShortUrls();
    }

    public function view(User $user, ShortUrl $shortUrl): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isAdmin()) {
            return $shortUrl->company_id === $user->company_id;
        }

        return $shortUrl->user_id === $user->id;
    }
}

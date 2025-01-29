<?php

namespace App\Policies;

use App\Models\User;

class RecipeDietPolicy
{
    public function create(User $user): bool
    {
        return $user->isInternal();
    }
}

<?php

namespace App\Policies;

use App\Enum\RolesEnum;
use App\Models\RecipeDiet;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RecipeDietPolicy
{
    public function create(User $user): Response
    {
        return $user->role >= RolesEnum::INTERNAL
            ? Response::allow()
            : Response::deny('Denied');
    }
}

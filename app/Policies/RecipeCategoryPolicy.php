<?php

namespace App\Policies;

use App\Enum\RolesEnum;
use App\Models\RecipeCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RecipeCategoryPolicy
{
    public function isInternalUser(User $user): Response
    {
        return $user->role >= RolesEnum::INTERNAL
            ? Response::allow()
            : Response::deny('Denied');
    }
}

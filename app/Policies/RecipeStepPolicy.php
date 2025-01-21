<?php

namespace App\Policies;

use App\Models\Recipe;
use App\Models\RecipeStep;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RecipeStepPolicy
{
    public function isInternalUser(User $user): Response
    {
        return $user->role >= RolesEnum::INTERNAL
            ? Response::allow()
            : Response::deny('Denied');
    }

    public function modify(User $user, Recipe $recipe): Response
    {
        if (!$recipe->relationLoaded('user')) {
            $recipe->load('user');
        }

        return $user->role >= RolesEnum::INTERNAL || $user->id === $recipe->user->id
            ? Response::allow()
            : Response::deny('Denied');
    }
}

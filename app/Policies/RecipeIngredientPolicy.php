<?php

namespace App\Policies;

use App\Models\RecipeIngredient;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RecipeIngredientPolicy
{
    public function isInternalUser(User $user): Response
    {
        return $user->role >= RolesEnum::INTERNAL
            ? Response::allow()
            : Response::deny('Denied');
    }

    public function modify(User $user, RecipeIngredient $recipeIngredient): Response
    {
        if (!$recipeIngredient->relationLoaded('recipe')) {
            $recipeIngredient->load('recipe');
        }

        return $user->role >= RolesEnum::INTERNAL || $user->id === $recipeIngredient->recipe->user->id
            ? Response::allow()
            : Response::deny('Denied');
    }
}

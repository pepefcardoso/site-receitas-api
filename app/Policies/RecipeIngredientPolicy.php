<?php

namespace App\Policies;

use App\Models\RecipeIngredient;
use App\Models\User;

class RecipeIngredientPolicy
{
    public function update(User $user, RecipeIngredient $recipeIngredient): bool
    {
        return $user->isInternal() || $user->id === $recipeIngredient->recipe->user_id;
    }

    public function delete(User $user, RecipeIngredient $recipeIngredient): bool
    {
        return $user->isInternal() || $user->id === $recipeIngredient->recipe->user_id;
    }
}

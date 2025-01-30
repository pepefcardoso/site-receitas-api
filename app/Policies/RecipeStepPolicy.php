<?php

namespace App\Policies;

use App\Models\RecipeStep;
use App\Models\User;

class RecipeStepPolicy
{
    public function update(User $user, RecipeStep $recipeStep): bool
    {
        return $user->isInternal() || $user->id === $recipeStep->recipe->user_id;
    }

    public function delete(User $user, RecipeStep $recipeStep): bool
    {
        return $user->isInternal() || $user->id === $recipeStep->recipe->user_id;
    }
}

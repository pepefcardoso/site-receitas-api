<?php

namespace App\Policies;

use App\Models\RecipeCategory;
use App\Models\User;

class RecipeCategoryPolicy
{
    public function create(User $user): bool
    {
        return $user->isInternal();
    }

    public function update(User $user, RecipeCategory $recipeCategory): bool
    {
        return $user->isInternal();
    }

    public function delete(User $user, RecipeCategory $recipeCategory): bool
    {
        return $user->isInternal();
    }
}

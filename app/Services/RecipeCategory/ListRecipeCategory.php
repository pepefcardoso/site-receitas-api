<?php

namespace App\Services\RecipeCategory;

use App\Models\RecipeCategory;

class ListRecipeCategory
{
    public function list(array $filters = [], int $perPage = 10)
    {
        return RecipeCategory::with('image')->get();
    }
}

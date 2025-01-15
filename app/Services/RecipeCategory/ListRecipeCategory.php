<?php

namespace App\Services\RecipeCategory;

use App\Models\RecipeCategory;

class ListRecipeCategory
{
    public function list(array $filters = [])
    {
        return RecipeCategory::all();
    }
}

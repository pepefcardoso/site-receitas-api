<?php

namespace App\Services\RecipeCategory;

use App\Models\RecipeCategory;

class ShowRecipeCategory
{
    public function show($id)
    {
        return RecipeCategory::select('id', 'name')->findOrFail($id);
    }
}

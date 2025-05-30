<?php

namespace App\Services\RecipeCategory;

use App\Models\RecipeCategory;

class ListRecipeCategory
{
    public function list(int $perPage = 10)
    {
        $query = RecipeCategory::select('id', 'name');

        return $query->paginate($perPage);
    }
}

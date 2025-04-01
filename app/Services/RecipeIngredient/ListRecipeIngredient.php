<?php

namespace App\Services\RecipeIngredient;

use App\Models\RecipeIngredient;

class ListRecipeIngredient
{
    public function list(int $perPage = 10)
    {
        $query = RecipeIngredient::query();

        return $query->paginate($perPage);
    }
}

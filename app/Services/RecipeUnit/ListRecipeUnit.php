<?php

namespace App\Services\RecipeUnit;

use App\Models\RecipeUnit;

class ListRecipeUnit
{
    public function list(int $perPage = 10)
    {
        $query = RecipeUnit::query();

        return $query->paginate($perPage);
    }
}

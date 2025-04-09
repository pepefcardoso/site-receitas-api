<?php

namespace App\Services\RecipeUnit;

use App\Models\RecipeUnit;

class ListRecipeUnit
{
    public function list(int $perPage = 10)
    {
        $query = RecipeUnit::select('id', 'name');

        return $query->paginate($perPage);
    }
}

<?php

namespace App\Services\RecipeUnit;

use App\Models\RecipeUnit;

class ListRecipeUnit
{
    public function list(array $filters = [], int $perPage = 10)
    {
        return RecipeUnit::all();
    }
}

<?php

namespace App\Services\RecipeUnit;

use App\Models\RecipeUnit;

class ListRecipeUnit
{
    public function list(array $filters = [])
    {
        return RecipeUnit::all();
    }
}

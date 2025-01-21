<?php

namespace App\Services\RecipeStep;

use App\Models\RecipeStep;

class ListRecipeStep
{
    public function list(array $filters = [])
    {
        return RecipeStep::all();
    }
}

<?php

namespace App\Services\RecipeStep;

use App\Models\RecipeStep;

class ListRecipeStep
{
    public function list(int $perPage = 10)
    {
        $query = RecipeStep::query();

        return $query->paginate($perPage);
    }
}

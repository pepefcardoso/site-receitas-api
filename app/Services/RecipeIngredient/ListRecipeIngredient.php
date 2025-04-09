<?php

namespace App\Services\RecipeIngredient;

use App\Models\RecipeIngredient;

class ListRecipeIngredient
{
    public function list(int $perPage = 10)
    {
        $query = RecipeIngredient::select('id', 'name', 'quantity', 'unit_id', 'recipe_id')
            ->with([
                'unit' => fn($q) => $q->select('id', 'name')
            ]);

        return $query->paginate($perPage);
    }
}

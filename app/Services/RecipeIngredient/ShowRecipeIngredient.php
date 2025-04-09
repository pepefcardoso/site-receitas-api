<?php

namespace App\Services\RecipeIngredient;

use App\Models\RecipeIngredient;

class ShowRecipeIngredient
{
    public function show($id)
    {
        return RecipeIngredient::with([
            'unit' => fn($q) => $q->select('id', 'name')
        ])->findOrFail($id);
    }
}

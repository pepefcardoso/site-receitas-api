<?php

namespace App\Services\RecipeIngredient;

use App\Models\RecipeIngredient;

class ListRecipeIngredient
{
    public function list(array $filters = [])
    {
        return RecipeIngredient::with('unit')->get();
    }
}

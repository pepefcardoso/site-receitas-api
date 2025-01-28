<?php

namespace App\Services\RecipeIngredient;

use App\Models\RecipeIngredient;

class ShowRecipeIngredient
{
    public function show($id)
    {
        return RecipeIngredient::findOrFail($id);
    }
}

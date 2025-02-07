<?php

namespace App\Services\Recipe;

use App\Models\Recipe;

class ShowRecipe
{
    public function show($id)
    {
        return Recipe::with([
            'diets',
            'category',
            'steps',
            'ingredients.unit',
            'image',
            'user.image'
        ])->findOrFail($id);
    }
}

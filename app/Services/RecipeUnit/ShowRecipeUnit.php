<?php

namespace App\Services\RecipeUnit;

use App\Models\RecipeUnit;

class ShowRecipeUnit
{
    public function show($id)
    {
        return RecipeUnit::findOrFail($id);
    }
}

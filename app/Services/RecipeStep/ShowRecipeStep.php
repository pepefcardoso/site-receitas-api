<?php

namespace App\Services\RecipeStep;

use App\Models\RecipeStep;

class ShowRecipeStep
{
    public function show($id): RecipeStep
    {
        return RecipeStep::findOrFail($id);
    }
}

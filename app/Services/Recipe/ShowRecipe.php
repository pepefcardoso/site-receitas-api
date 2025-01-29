<?php

namespace App\Services\Recipe;

use App\Models\Recipe;

class ShowRecipe
{
    public function show($id)
    {
        //need to load the relations too
        return Recipe::findOrFail($id);
    }
}

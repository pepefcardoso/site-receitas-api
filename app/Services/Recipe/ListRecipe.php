<?php

namespace App\Services\Recipe;

use App\Models\Recipe;

class ListRecipe
{
    public function list(array $filters = [])
    {
        //need to load the relations too
        return Recipe::all();
    }
}

<?php

namespace App\Services\Recipe;

use App\Models\Recipe;

class ListRecipe
{
    public function list(array $filters = [])
    {
        return Recipe::all();
    }
}

<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use Illuminate\Support\Facades\DB;

class ShowRecipe
{
    public function show($id)
    {
        return Recipe::findOrFail($id);
    }
}

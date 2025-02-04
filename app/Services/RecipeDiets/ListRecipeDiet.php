<?php

namespace App\Services\RecipeDiets;

use App\Models\RecipeDiet;

class ListRecipeDiet
{
    public function list(array $filters = [], $perPage = 10)
    {
        return RecipeDiet::with('image')->get();
    }
}

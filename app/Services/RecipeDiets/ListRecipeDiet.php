<?php

namespace App\Services\RecipeDiets;

use App\Models\RecipeDiet;

class ListRecipeDiet
{
    public function list(int $perPage = 10)
    {
        $query = RecipeDiet::query();

        return $query->paginate($perPage);
    }
}

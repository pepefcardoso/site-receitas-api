<?php

namespace App\Services\RecipeDiets;

use App\Models\RecipeDiet;

class ListRecipeDiet
{
    public function list(int $perPage = 10)
    {
        $query = RecipeDiet::select('id', 'name');

        return $query->paginate($perPage);
    }
}

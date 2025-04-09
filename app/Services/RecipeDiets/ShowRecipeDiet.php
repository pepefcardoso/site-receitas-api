<?php

namespace App\Services\RecipeDiets;

use App\Models\RecipeDiet;

class ShowRecipeDiet
{
    public function show($id)
    {
        return RecipeDiet::select('id', 'name')->findOrFail($id);
    }
}

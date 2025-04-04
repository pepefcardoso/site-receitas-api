<?php

namespace App\Services\Rating;

use App\Models\Rating;

class ShowRating
{
    public function show(int $id)
    {
        return Rating::with(['user:id,name'])
        ->findOrFail($id);
    }
}

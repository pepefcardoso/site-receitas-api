<?php

namespace App\Services\Rating;

use App\Models\Rating;

class ShowRating
{
    public function show(int $id)
    {
        return Rating::findOrfail($id);
    }
}

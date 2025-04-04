<?php

namespace App\Services\Rating;

use App\Models\Rating;

class ListRatings
{
    public function list(int $perPage = 10)
    {
        return Rating::with(['user:id,name'])
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
    }
}

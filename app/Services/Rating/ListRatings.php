<?php

namespace App\Services\Rating;

use App\Models\Rating;

class ListRatings
{
    public function list(int $perPage = 10)
    {
        $query = Rating::query();

        return $query->paginate($perPage);
    }
}

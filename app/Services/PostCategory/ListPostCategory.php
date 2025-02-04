<?php

namespace App\Services\PostCategory;

use App\Models\PostCategory;

class ListPostCategory
{
    public function list(array $filters = [], $perPage = 10)
    {
        return PostCategory::with('image')->get();
    }
}

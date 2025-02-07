<?php

namespace App\Services\PostCategory;

use App\Models\PostCategory;

class ListPostCategory
{
    public function list(int $perPage = 10)
    {
        $query = PostCategory::query();

        return $query->paginate($perPage);
    }
}

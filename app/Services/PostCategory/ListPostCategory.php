<?php

namespace App\Services\PostCategory;

use App\Models\PostCategory;

class ListPostCategory
{
    public function list(int $perPage = 10)
    {
        return PostCategory::select('id', 'name')->paginate($perPage);
    }
}

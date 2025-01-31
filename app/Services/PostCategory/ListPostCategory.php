<?php

namespace App\Services\PostCategory;

use App\Models\PostCategory;

class ListPostCategory
{
    public function list(array $filters = [])
    {
        return PostCategory::all();
    }
}

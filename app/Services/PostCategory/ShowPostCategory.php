<?php

namespace App\Services\PostCategory;

use App\Models\PostCategory;

class ShowPostCategory
{
    public function show($id)
    {
        return PostCategory::select('id', 'name')->findOrFail($id);
    }
}

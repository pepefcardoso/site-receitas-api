<?php

namespace App\Services\PostCategory;

use App\Models\PostCategory;

class ShowPostCategory
{
    public function show($id)
    {
        return PostCategory::with('image')->findOrFail($id);
    }
}

<?php

namespace App\Services\Post;

use App\Models\Post;

class ShowPost
{
    public function show($id)
    {
        return Post::with([
            'category',
            'user' => function ($query) {
                $query->select('name', 'image');
            }
        ])->findOrFail($id);
    }
}

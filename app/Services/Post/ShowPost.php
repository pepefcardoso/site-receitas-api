<?php

namespace App\Services\Post;

use App\Models\Post;

class ShowPost
{
    public function show($id)
    {
        return Post::with([
            'category',
            'topics',
            'image',
            'user.image'
        ])->findOrFail($id);
    }
}

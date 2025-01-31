<?php

namespace App\Services\Post;

use App\Models\Post;

class ListPost
{
    public function list(array $filters = [])
    {
        $query = Post::with([
            'category',
            'user' => function ($query) {
                $query->select('name', 'image');
            }
        ]);

        return $query->get();
    }
}

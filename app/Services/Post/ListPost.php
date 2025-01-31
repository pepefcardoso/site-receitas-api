<?php

namespace App\Services\Post;

use App\Models\Post;

class ListPost
{
    public function list(array $filters = [])
    {
        $query = Post::with([
            'category',
            'topics',
            'user' => function ($query) {
                $query->select('id', 'name');
            }
        ]);

        return $query->get();
    }
}

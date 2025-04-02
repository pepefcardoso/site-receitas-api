<?php

namespace App\Services\Comment;

use App\Models\Comment;

class ListComments
{
    public function list(int $perPage = 10)
    {
        $query = Comment::query();

        return $query->paginate($perPage);
    }
}

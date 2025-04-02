<?php

namespace App\Services\Comment;

use App\Models\Comment;

class ListComments
{
    public function list(int $perPage = 10)
    {
        return Comment::with(['user', 'user.image'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}

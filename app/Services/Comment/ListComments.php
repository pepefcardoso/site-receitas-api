<?php

namespace App\Services\Comment;

use App\Models\Comment;

class ListComments
{
    public function list(int $perPage = 10)
    {
        return Comment::with([
            'user:id,name',
            'user.image' => fn($q) => $q->select('id', 'path', 'imageable_id', 'imageable_type'),
        ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}

<?php

namespace App\Services\Comment;

use App\Models\Comment;

class ShowComment
{
    public function show(int $id)
    {
        return Comment::with(['user', 'user.image'])
            ->where('id', $id)
            ->firstOrFail();
    }
}

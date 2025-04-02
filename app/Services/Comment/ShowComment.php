<?php

namespace App\Services\Comment;

use App\Models\Comment;

class ShowComment
{
    public function show(int $id)
    {
        return Comment::findOrfail($id);
    }
}

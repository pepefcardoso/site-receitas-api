<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
class CommentPolicy
{
    public function viewAny(?User $user)
    {
        return true;
    }

    public function view(?User $user, Comment $comment)
    {
        return true;
    }

    public function create(?User $user): bool
    {
        return true;
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->isInternal() || $user->id === $comment->user_id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->isInternal() || $user->id === $comment->user_id;
    }
}

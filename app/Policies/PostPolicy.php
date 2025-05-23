<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
class PostPolicy
{
    public function viewAny(?User $user)
    {
        return true;
    }

    public function view(?User $user, Post $Post)
    {
        return true;
    }

    public function create(?User $user): bool
    {
        return true;
    }

    public function update(User $user, Post $post): bool
    {
        return $user->isInternal() || $user->id === $post->user_id;
    }

    public function delete(User $user, Post $Post): bool
    {
        return $user->isInternal() || $user->id === $Post->user_id;
    }
}

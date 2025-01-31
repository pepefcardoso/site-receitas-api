<?php

namespace App\Policies;

use App\Models\PostCategory;
use App\Models\User;

class PostCategoryPolicy
{
    public function viewAny(?User $user)
    {
        return true;
    }

    public function view(?User $user, PostCategory $PostCategory)
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isInternal();
    }

    public function update(User $user, PostCategory $PostCategory): bool
    {
        return $user->isInternal();
    }

    public function delete(User $user, PostCategory $PostCategory): bool
    {
        return $user->isInternal();
    }
}

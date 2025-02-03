<?php

namespace App\Policies;

use App\Models\Image;
use App\Models\User;
class ImagePolicy
{
    public function viewAny(?User $user)
    {
        return true;
    }

    public function view(?User $user, Image $Image)
    {
        return true;
    }

    public function create(?User $user): bool
    {
        return true;
    }

    public function update(User $user, Image $Image): bool
    {
        return $user->isInternal() || $user->id === $Image->user_id;
    }

    public function delete(User $user, Image $Image): bool
    {
        return $user->isInternal() || $user->id === $Image->user_id;
    }
}

<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{

    public function viewAny(?User $user)
    {
        return $user->isInternal();
    }

    public function view(?User $authUser, User $updateUser): bool
    {
        return $authUser->isInternal() || $authUser->id === $updateUser->id;
    }

    public function create(User $user): bool
    {
        if (!$user) {
            return false;
        }

        return true;
    }

    public function update(?User $authUser, User $updateUser): bool
    {
        return $authUser->isInternal() || $authUser->id === $updateUser->id;
    }

    public function delete(?User $authUser, User $updateUser): bool
    {
        return $authUser->isInternal() || $authUser->id === $updateUser->id;
    }
}

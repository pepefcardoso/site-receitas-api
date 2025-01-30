<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function update(User $authUser, User $updateUser): bool
    {
        return $authUser->isInternal() || $authUser->id === $updateUser->id;
    }

    public function showAndModify(User $authUser, User $updateUser): bool
    {
        return $authUser->isInternal() || $authUser->id === $updateUser->id;
    }
}

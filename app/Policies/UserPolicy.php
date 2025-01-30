<?php

namespace App\Policies;

use App\Enum\RolesEnum;
use App\Models\User;
use Illuminate\Auth\Access\Response;

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

<?php

namespace App\Policies;

use App\Enum\RolesEnum;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function isInternalUser(User $user): Response
    {
        return $user->role >= RolesEnum::INTERNAL
            ? Response::allow()
            : Response::deny('Denied');
    }

    public function showAndModify(User $user, User $modelUser): Response
    {
        return $user->role >= RolesEnum::INTERNAL || $user->id === $modelUser->id
            ? Response::allow()
            : Response::deny('Denied');
    }
}

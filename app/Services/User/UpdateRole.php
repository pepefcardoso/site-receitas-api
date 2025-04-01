<?php

namespace App\Services\User;

use App\Enum\RolesEnum;
use App\Models\User;
use Exception;

class UpdateRole
{
    public function update(array $data): User|string
    {
        try {
            $userId = data_get($data, 'user_id');
            $user = User::findOrFail($userId);

            $newRoleId = data_get($data, 'role');

            $user->role = $newRoleId;
            $user->save();

            return $user;
        } catch (Exception $e) {
            throw $e;
        }
    }
}

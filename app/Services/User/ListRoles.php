<?php

namespace App\Services\User;

use App\Enum\RolesEnum;

class ListRoles
{
    public function list()
    {
        return array_map(function (RolesEnum $role) {
            return [
                'id' => $role->value,
                'name' => $role->name,
            ];
        }, RolesEnum::cases());
    }
}

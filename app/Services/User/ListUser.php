<?php

namespace App\Services\User;

use App\Models\User;

class ListUser
{
    public function list(array $filters = [])
    {
        return User::with('image')->get();
    }
}

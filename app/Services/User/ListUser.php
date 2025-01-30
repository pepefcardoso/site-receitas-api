<?php

namespace App\Services\User;

use App\Models\User;

class ListUser
{
    public function list(array $filters = [])
    {
        //need to load the relations too
        return User::all();
    }
}

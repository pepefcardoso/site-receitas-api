<?php

namespace App\Services\User;

use App\Models\User;

class ShowUser
{
    public function show($id)
    {
        //need to load the relations too
        return User::findOrFail($id);
    }
}

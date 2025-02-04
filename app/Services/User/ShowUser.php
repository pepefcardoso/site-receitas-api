<?php

namespace App\Services\User;

use App\Models\User;

class ShowUser
{
    public function show($id)
    {
        return User::with('image')->findOrFail($id);
    }
}

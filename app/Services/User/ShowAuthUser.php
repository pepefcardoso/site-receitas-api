<?php

namespace App\Services\User;

use App\Models\User;
use Exception;

class ShowAuthUser
{
    public function show()
    {
        $userId = auth()->user()->id;
        if (!$userId) {
            throw new Exception('User not authenticated');
        }

        return User::with('image')->findOrFail($userId);
    }
}

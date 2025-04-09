<?php

namespace App\Services\User;

use App\Models\User;
use Exception;

class ShowAuthUser
{
    public function show()
    {
        $userId = auth()->id();

        if (!$userId) {
            throw new Exception('User not authenticated');
        }

        return User::select('id', 'name', 'email', 'phone', 'birthday', 'created_at')
            ->with([
                'image' => fn($q) => $q->select('id', 'path', 'imageable_id', 'imageable_type'),
            ])
            ->findOrFail($userId);
    }
}

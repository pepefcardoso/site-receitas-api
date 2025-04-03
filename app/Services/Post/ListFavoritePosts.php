<?php

namespace App\Services\Post;

use App\Models\User;
use Exception;

class ListFavoritePosts
{
    public function list(int $perPage = 10)
    {
        $userId = auth()->id();
        if (!$userId) {
            throw new Exception('User not authenticated');
        }

        return User::find($userId)
            ->favoritePosts()
            ->with(['image'])
            ->paginate($perPage);
    }
}

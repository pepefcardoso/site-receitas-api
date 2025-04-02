<?php

namespace App\Services\Post;

use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\Auth;

class ListUserPosts
{
    public function list($perPage = 10)
    {
        $userId = Auth::id();
        if (!$userId) {
            throw new Exception('User not authenticated');
        }

        return Post::with(['image'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->where('user_id', $userId)
            ->paginate($perPage);
    }
}

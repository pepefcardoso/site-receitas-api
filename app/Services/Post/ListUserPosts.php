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

        return Post::select('id', 'title')
            ->with([
                'image' => fn($q) => $q
                    ->select('id', 'path', 'imageable_id', 'imageable_type', 'created_at', 'updated_at')
                    ->makeHidden('path'),
            ])
            ->withExists([
                'favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', $userId)
            ])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->where('user_id', $userId)
            ->paginate($perPage);
    }
}

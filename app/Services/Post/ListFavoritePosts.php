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

        return User::findOrFail($userId)
            ->favoritePosts()
            ->select('id', 'title')
            ->with([
                'image' => fn($q) => $q
                    ->select('id', 'path', 'imageable_id', 'imageable_type', 'created_at', 'updated_at')
                    ->makeHidden('path'),
            ])
            ->withExists([
                'favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', $userId),
            ])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->paginate($perPage);
    }
}

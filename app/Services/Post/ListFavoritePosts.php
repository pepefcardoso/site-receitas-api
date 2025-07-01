<?php

namespace App\Services\Post;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class ListFavoritePosts
{
    public function list(int $perPage = 10)
    {
        $userId = Auth::id();
        if (!$userId) {
            throw new Exception('User not authenticated');
        }

        return User::findOrFail($userId)
            ->favoritePosts()
            ->select('id', 'title')
            ->with([
                'image' => fn($q) => $q
                    ->select('id', 'imageable_id', 'imageable_type', 'created_at', 'updated_at'),
            ])
            ->withExists([
                'favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', $userId),
            ])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->paginate($perPage);
    }
}

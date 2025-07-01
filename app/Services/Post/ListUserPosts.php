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

        return Post::where('user_id', $userId)
            ->with([
                'image' => fn($q) => $q
                    ->select('id', 'model_type', 'model_id', 'name', 'file_name'),
                'topics:id,name',
                'category:id,name',
                'user:id,name'
            ])
            ->withExists([
                'favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', $userId)
            ])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->paginate($perPage);
    }
}

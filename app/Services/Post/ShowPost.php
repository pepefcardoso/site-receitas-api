<?php

namespace App\Services\Post;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class ShowPost
{
    public function show(int $id)
    {
        $post = Cache::remember("post_model.{$id}", now()->addHour(), function () use ($id) {
            return Post::with(['category', 'topics', 'image', 'user.image'])
                ->withAvg('ratings', 'rating')
                ->withCount('ratings')
                ->findOrFail($id);
        });

        if ($userId = auth('sanctum')->id()) {
            $post->loadExists(['favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', $userId)]);
        }
        return $post;
    }
}

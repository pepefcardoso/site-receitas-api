<?php

namespace App\Services\Post;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class ShowPost
{
    public function show(int $id)
    {
        $post = Cache::remember("post.{$id}", now()->addHour(), function () use ($id) {
            return Post::with([
                'category' => fn($q) => $q->select('id', 'name'),
                'topics' => fn($q) => $q->select('post_topics.id', 'post_topics.name'),
                'image' => fn($q) => $q->select('id', 'path', 'imageable_id', 'imageable_type'),
                'user' => fn($q) => $q->select('id', 'name'),
                'user.image' => fn($q) => $q->select('id', 'path', 'imageable_id', 'imageable_type'),
            ])
                ->withAvg('ratings', 'rating')
                ->withCount('ratings')
                ->findOrFail($id);
        });

        $userId = auth('sanctum')->id();
        if ($userId) {
            $post->loadExists([
                'favoritedByUsers as is_favorited' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ]);
        } else {
            $post->is_favorited = false;
        }

        return $post;
    }
}

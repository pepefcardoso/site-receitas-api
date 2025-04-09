<?php

namespace App\Services\Post;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class ShowPost
{
    public function show($id)
    {
        return Cache::remember("post.{$id}", now()->addHour(), function () use ($id) {
            return Post::with([
                    'category' => fn($q) => $q->select('id', 'name'),
                    'topics' => fn($q) => $q->select('id', 'name'),
                    'image' => fn($q) => $q->select('id', 'path', 'imageable_id', 'imageable_type')->makeHidden('path'),
                    'user' => fn($q) => $q->select('id', 'name'),
                    'user.image' => fn($q) => $q->select('id', 'path', 'imageable_id', 'imageable_type')->makeHidden('path'),
                ])
                ->when(auth()->check(), fn($q) => $q->withExists([
                    'favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', auth()->id())
                ]))
                ->withAvg('ratings', 'rating')
                ->withCount('ratings')
                ->findOrFail($id);
        });
    }
}

<?php

namespace App\Services\Post;

use App\Models\Post;

class ShowPost
{
    public function show($id)
    {
        $query = Post::with([
            'category',
            'topics',
            'image',
            'user.image'
        ])
        ->withAvg('ratings', 'rating')
        ->withCount('ratings');

        if (auth()->check()) {
            $query->withExists([
                'favoritedByUsers as is_favorited' => function ($query) {
                    $query->where('user_id', auth()->id());
                }
            ]);
        }

        return $query->findOrFail($id);
    }
}

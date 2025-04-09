<?php

namespace App\Services\Post;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class ListPost
{
    public function list(array $filters = [], $perPage = 10)
    {
        $query = Post::select('id', 'title', 'summary', 'user_id', 'category_id')
            ->with([
                'category:id,name',
                'topics:id,name',
                'image:id,path,imageable_id,imageable_type'
            ])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings');

        if (Auth::check()) {
            $query->withExists([
                'favoritedByUsers as is_favorited' => function ($query) {
                    $query->where('user_id', Auth::id());
                }
            ]);
        }

        $query = $query->filter($filters);
        $query = $this->applySorting($query, $filters);

        return $query->paginate($perPage);
    }

    protected function applySorting($query, array $filters)
    {
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';

        if (!in_array($orderBy, Post::VALID_SORT_COLUMNS)) {
            $orderBy = 'created_at';
        }

        if (!in_array(strtolower($orderDirection), ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        return $query->orderBy($orderBy, $orderDirection);
    }
}

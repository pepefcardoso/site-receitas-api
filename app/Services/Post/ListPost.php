<?php

namespace App\Services\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Log;

class ListPost
{
    public function list(array $filters = [], int $perPage = 10)
    {
        $query = Post::with(['user', 'category', 'topics', 'image'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings');

        if ($userId = auth('sanctum')->id()) {
            $query->withExists(['favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', $userId)]);
        }

        $query->filter($filters);
        $this->applySorting($query, $filters);
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

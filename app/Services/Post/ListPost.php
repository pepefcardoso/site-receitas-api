<?php

namespace App\Services\Post;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class ListPost
{
    public function list(array $filters = [], int $perPage = 10)
    {
        $searchTerm = $filters['title'] ?? $filters['search'] ?? '';

        if (empty($searchTerm)) {
            return $this->traditionalQuery($filters, $perPage);
        }

        return $this->searchQuery($searchTerm, $filters, $perPage);
    }

    private function searchQuery(string $searchTerm, array $filters, int $perPage)
    {
        $query = Post::search($searchTerm, function ($meilisearch, $query, $options) use ($filters) {
            $filterExpressions = [];

            if (!empty($filters['category_id'])) {
                $filterExpressions[] = 'category_id = ' . (int) $filters['category_id'];
            }

            if (!empty($filters['user_id'])) {
                $filterExpressions[] = 'user_id = ' . (int) $filters['user_id'];
            }

            if (!empty($filterExpressions)) {
                $options['filter'] = implode(' AND ', $filterExpressions);
            }

            $orderBy = $filters['order_by'] ?? 'created_at';
            $orderDirection = $filters['order_direction'] ?? 'desc';

            if (in_array($orderBy, Post::VALID_SORT_COLUMNS)) {
                $options['sort'] = [$orderBy . ':' . $orderDirection];
            }

            return $meilisearch->search($query, $options);
        });

        return $this->addRelationsAndPaginate($query, $perPage);
    }

    private function traditionalQuery(array $filters, int $perPage)
    {
        $query = Post::query();

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';

        if (in_array($orderBy, Post::VALID_SORT_COLUMNS)) {
            $query->orderBy($orderBy, $orderDirection);
        }

        return $this->addRelationsAndPaginate($query, $perPage);
    }

    private function addRelationsAndPaginate($query, int $perPage)
    {
        $userId = Auth::id();

        $query->with(['user', 'category', 'topics', 'image'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings');

        if ($userId) {
            $query->withExists([
                'favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', $userId)
            ]);
        }

        return $query->paginate($perPage);
    }
}

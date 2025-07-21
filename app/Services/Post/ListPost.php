<?php

namespace App\Services\Post;

use App\Models\Post;

class ListPost
{
    public function list(array $filters = [], int $perPage = 10)
    {
        $searchTerm = $filters['search'] ?? '*';

        $query = Post::search($searchTerm, function ($meilisearch, $query, $options) use ($filters) {

            $filterExpressions = [];

            if (!empty($filters['category_id'])) {
                $filterExpressions[] = 'category_id = ' . $filters['category_id'];
            }

            if (!empty($filters['user_id'])) {
                $filterExpressions[] = 'user_id = ' . $filters['user_id'];
            }

            if (!empty($filterExpressions)) {
                $options['filter'] = implode(' AND ', $filterExpressions);
            }

            $orderBy = $filters['order_by'] ?? 'created_at';
            $orderDirection = $filters['order_direction'] ?? 'desc';

            if (in_array($orderBy, Post::VALID_SORT_COLUMNS) && in_array($orderDirection, ['asc', 'desc'])) {
                 $options['sort'] = [$orderBy . ':' . $orderDirection];
            }

            return $meilisearch->search($query, $options);
        });

        if ($userId = auth('sanctum')->id()) {
            $query->query(fn($builder) => $builder->withExists(['favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', $userId)]));
        }

        $query->query(fn($builder) => $builder->with(['user', 'category', 'topics', 'image'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings'));

        return $query->paginate($perPage);
    }
}

# Exemplo de como configurar os atributos via terminal com cURL
// curl -X POST 'http://localhost:7700/indexes/posts/settings/filterable-attributes' \
// -H "Content-Type: application/json" \
// --data-binary '["category_id", "user_id"]'

// curl -X POST 'http://localhost:7700/indexes/posts/settings/sortable-attributes' \
// -H "Content-Type: application/json" \
// --data-binary '["title", "created_at"]'

<?php

namespace App\Services\Recipe;

use App\Models\Recipe;

class ListRecipe
{
    public function list(array $filters = [], int $perPage = 10)
    {
        $searchTerm = $filters['title'] ?? '';

        if (empty($searchTerm)) {
            return $this->traditionalQuery($filters, $perPage);
        }

        return $this->searchQuery($searchTerm, $filters, $perPage);
    }

    private function searchQuery(string $searchTerm, array $filters, int $perPage)
    {
        $query = Recipe::search($searchTerm, function ($meilisearch, $query, $options) use ($filters) {
            $filterExpressions = [];

            if (!empty($filters['category_id'])) {
                $filterExpressions[] = 'category_id = ' . (int) $filters['category_id'];
            }

            if (!empty($filters['user_id'])) {
                $filterExpressions[] = 'user_id = ' . (int) $filters['user_id'];
            }

            if (!empty($filters['diets']) && is_array($filters['diets'])) {
                $diets = implode(', ', array_map('intval', $filters['diets']));
                $filterExpressions[] = 'diets IN [' . $diets . ']';
            }

            if (!empty($filterExpressions)) {
                $options['filter'] = implode(' AND ', $filterExpressions);
            }

            $orderBy = $filters['order_by'] ?? 'created_at';
            $orderDirection = $filters['order_direction'] ?? 'desc';

            if (in_array($orderBy, Recipe::VALID_SORT_COLUMNS)) {
                $options['sort'] = [$orderBy . ':' . $orderDirection];
            }

            return $meilisearch->search($query, $options);
        });

        return $this->addRelationsAndPaginate($query, $perPage);
    }

    private function traditionalQuery(array $filters, int $perPage)
    {
        $query = Recipe::query();

        if (!empty($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['diets'])) {
            $query->whereHas('diets', function ($query) use ($filters) {
                $query->whereIn('recipe_diets.id', $filters['diets']);
            });
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';

        if (in_array($orderBy, Recipe::VALID_SORT_COLUMNS)) {
            $query->orderBy($orderBy, $orderDirection);
        }

        return $this->addRelationsAndPaginate($query, $perPage);
    }

    private function addRelationsAndPaginate($query, int $perPage)
    {
        $userId = auth('sanctum')->id();

        if (method_exists($query, 'query')) {
            $query->query(function ($builder) use ($userId) {
                $builder->with(['diets', 'category', 'image'])
                    ->withAvg('ratings', 'rating')
                    ->withCount('ratings');

                if ($userId) {
                    $builder->withExists([
                        'favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', $userId)
                    ]);
                }
            });
        } else {
            $query->with(['diets', 'category', 'image'])
                ->withAvg('ratings', 'rating')
                ->withCount('ratings');

            if ($userId) {
                $query->withExists([
                    'favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', $userId)
                ]);
            }
        }

        return $query->paginate($perPage);
    }
}

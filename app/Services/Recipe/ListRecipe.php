<?php

namespace App\Services\Recipe;

use App\Models\Recipe;

class ListRecipe
{
    /**
     * Lista e filtra as receitas usando Meilisearch (via Laravel Scout).
     *
     * @param array $filters Filtros de busca (title, category_id, diets, user_id, order_by, order_direction)
     * @param int $perPage Resultados por página
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 10)
    {
        $searchTerm = $filters['title'] ?? '*';

        $query = Recipe::search($searchTerm, function ($meilisearch, $query, $options) use ($filters) {
            
            $filterExpressions = [];

            if (!empty($filters['category_id'])) {
                $filterExpressions[] = 'category_id = ' . (int)$filters['category_id'];
            }

            if (!empty($filters['user_id'])) {
                $filterExpressions[] = 'user_id = ' . (int)$filters['user_id'];
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

            if (in_array($orderBy, Recipe::VALID_SORT_COLUMNS) && in_array($orderDirection, ['asc', 'desc'])) {
                $options['sort'] = [$orderBy . ':' . $orderDirection];
            }

            return $meilisearch->search($query, $options);
        });

        $query->query(function ($builder) {
            $builder->with([
                'diets',
                'category',
                'image',
            ])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings');
        });
        
        if ($userId = auth('sanctum')->id()) {
            $query->query(function ($builder) use ($userId) {
                $builder->withExists([
                    'favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', $userId),
                ]);
            });
        }

        return $query->paginate($perPage);
    }
}

// # Configura os atributos que podem ser usados em filtros
// docker-compose exec app php artisan scout:sync-filterable-attributes "App\Models\Recipe"

// # Configura os atributos que podem ser usados para ordenação
// docker-compose exec app php artisan scout:sync-sortable-attributes "App\Models\Recipe"

// # Configura os atributos filtráveis para as receitas
// curl -X POST 'http://localhost:7700/indexes/recipes/settings/filterable-attributes' \
// -H "Content-Type: application/json" \
// --data-binary '["category_id", "user_id", "diets"]'

// # Configura os atributos ordenáveis para as receitas
// curl -X POST 'http://localhost:7700/indexes/recipes/settings/sortable-attributes' \
// -H "Content-Type: application/json" \
// --data-binary '["created_at", "title", "time", "difficulty"]'
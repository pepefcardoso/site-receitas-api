<?php

namespace App\Services\Concerns;

trait BaseListService
{
    abstract protected function getModelClass(): string;

    abstract protected function getValidSortColumns(): array;

    abstract protected function applySearchFilters($query, string $searchTerm, array $filters);

    abstract protected function getDefaultRelations(): array;

    public function list(array $filters = [], int $perPage = 10)
    {
        $searchTerm = $filters['search'] ?? '';

        if (empty($searchTerm)) {
            return $this->traditionalQuery($filters, $perPage);
        }

        return $this->searchQuery($searchTerm, $filters, $perPage);
    }

    protected function searchQuery(string $searchTerm, array $filters, int $perPage)
    {
        $modelClass = $this->getModelClass();

        $query = $modelClass::search($searchTerm, function ($meilisearch, $query, $options) use ($filters) {
            $filterExpressions = [];

            if (!empty($filters['category_id'])) {
                $filterExpressions[] = 'category_id = ' . (int) $filters['category_id'];
            }

            if (!empty($filters['user_id'])) {
                $filterExpressions[] = 'user_id = ' . (int) $filters['user_id'];
            }

            $filterExpressions = array_merge($filterExpressions, $this->getCustomSearchFilters($filters));

            if (!empty($filterExpressions)) {
                $options['filter'] = implode(' AND ', $filterExpressions);
            }

            $orderBy = $filters['order_by'] ?? 'created_at';
            $orderDirection = $filters['order_direction'] ?? 'desc';

            if (in_array($orderBy, $this->getValidSortColumns())) {
                $options['sort'] = [$orderBy . ':' . $orderDirection];
            }

            return $meilisearch->search($query, $options);
        });

        return $this->addRelationsAndPaginate($query, $perPage);
    }

    protected function traditionalQuery(array $filters, int $perPage)
    {
        $modelClass = $this->getModelClass();
        $query = $modelClass::query();

        $query->filter($filters);

        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';

        if (in_array($orderBy, $this->getValidSortColumns())) {
            $query->orderBy($orderBy, $orderDirection);
        }

        return $this->addRelationsAndPaginate($query, $perPage);
    }

    protected function addRelationsAndPaginate($query, int $perPage)
    {
        $userId = auth('sanctum')->id();

        if (method_exists($query, 'query')) {
            $query->query(function ($builder) use ($userId) {
                $builder->with($this->getDefaultRelations())
                    ->withAvg('ratings', 'rating')
                    ->withCount('ratings');

                if ($userId) {
                    $builder->withExists([
                        'favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', $userId)
                    ]);
                }
            });
        } else {
            $query->with($this->getDefaultRelations())
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

    protected function getCustomSearchFilters(array $filters): array
    {
        return [];
    }
}

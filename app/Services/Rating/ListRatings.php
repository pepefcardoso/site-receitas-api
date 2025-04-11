<?php

namespace App\Services\Rating;

use App\Models\Rating;

class ListRatings
{
    public function list(array $filters = [], int $perPage = 10)
    {
        $query = Rating::select('id', 'rating', '_id', '_type', 'user_id', 'created_at', 'updated_at')
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'name')
                        ->with([
                            'image' => function ($q) {
                                $q->select('id', 'path', 'imageable_id', 'imageable_type');
                            }
                        ]);
                }
            ]);

        if (isset($filters['rateable_id'])) {
            $query->where('rateable_id', $filters['rateable_id']);
        }

        if (isset($filters['rateable_type'])) {
            $modelClass = 'App\\Models\\' . $filters['rateable_type'];
            $query->where('rateable_type', $modelClass);
        }

        return $query->paginate($perPage);
    }
}

<?php

namespace App\Services\Comment;

use App\Models\Comment;

class ListComments
{
    public function list(array $filters = [], int $perPage = 10)
    {
        $query = Comment::select('id', 'content', 'commentable_id', 'commentable_type', 'user_id', 'created_at', 'updated_at')
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

        if (isset($filters['commentable_id'])) {
            $query->where('commentable_id', $filters['commentable_id']);
        }

        if (isset($filters['commentable_type'])) {
            $modelClass = 'App\\Models\\' . $filters['commentable_type'];
            $query->where('commentable_type', $modelClass);
        }

        return $query->paginate($perPage);
    }
}

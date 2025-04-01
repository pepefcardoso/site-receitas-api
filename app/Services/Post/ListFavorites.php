<?php

namespace App\Services\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class ListFavorites
{
    public function list(array $filters = [], int $perPage = 10)
    {
        $userId = data_get($filters, 'user_id');
        $user = User::findOrFail($userId);

        $query = $user->favoritePosts()
            ->with(['category', 'topics', 'image', 'user'])
            ->withExists([
                'favoritedByUsers as is_favorited' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            ]);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhereHas('topics', function ($q) use ($filters) {
                        $q->where('name', 'like', '%' . $filters['search'] . '%');
                    });
            });
        }

        $orderBy = in_array($filters['order_by'] ?? 'created_at', Post::VALID_SORT_COLUMNS)
            ? $filters['order_by']
            : 'created_at';

        $orderDirection = in_array(strtolower($filters['order_direction'] ?? 'desc'), ['asc', 'desc'])
            ? $filters['order_direction']
            : 'desc';

        return $query->orderBy($orderBy, $orderDirection)
            ->paginate($perPage);
    }
}

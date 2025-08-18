<?php

namespace App\Services\Concerns;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

trait HasUserContentListing
{
    abstract protected function getModelClass(): string;

    abstract protected function getFavoriteRelationshipName(): string;

    abstract protected function getUserContentRelations(): array;

    public function listUserContent(int $perPage = 10)
    {
        $userId = Auth::id();
        if (!$userId) {
            throw new Exception('User not authenticated');
        }

        $modelClass = $this->getModelClass();

        return $modelClass::where('user_id', $userId)
            ->with($this->getUserContentRelations())
            ->withExists([
                'favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', $userId)
            ])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->paginate($perPage);
    }

    public function listUserFavorites(int $perPage = 10)
    {
        $userId = Auth::id();
        if (!$userId) {
            throw new Exception('User not authenticated');
        }

        $relationshipName = $this->getFavoriteRelationshipName();

        return User::findOrFail($userId)
            ->{$relationshipName}()
                ->with($this->getFavoriteContentRelations())
                ->withExists([
                    'favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', $userId),
                ])
                ->withAvg('ratings', 'rating')
                ->withCount('ratings')
                ->paginate($perPage);
    }

    protected function getFavoriteContentRelations(): array
    {
        return [
            'image' => fn($q) => $q->select('id', 'imageable_id', 'imageable_type', 'created_at', 'updated_at'),
        ];
    }
}

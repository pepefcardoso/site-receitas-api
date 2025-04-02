<?php

namespace App\Services\Rating;

use App\Models\Rating;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateRating
{
    public function create(Model $model, int $rating): Rating
    {
        try {
            DB::beginTransaction();

            $userId = Auth::id();
            if (!$userId) {
                throw new Exception('User not authenticated');
            }

            $rating = $model->ratings()->create([
                'rating' => $rating,
                'user_id' => $userId,
            ]);

            DB::commit();

            return $rating;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

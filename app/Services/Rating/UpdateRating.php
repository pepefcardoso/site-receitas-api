<?php

namespace App\Services\Rating;

use App\Models\Rating;
use Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class UpdateRating
{
    public function update(int $ratingId, int $newRating): Rating
    {
        try {
            DB::beginTransaction();

            $rating = Rating::findOrFail($ratingId);

            $user_id = Auth::id();
            if (!$user_id) {
                throw new Exception('User not authenticated');
            }

            $rating->fill([
                'rating' => $newRating,
                'user_id' => $user_id,
            ]);
            $rating->save();

            DB::commit();
            return $rating;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

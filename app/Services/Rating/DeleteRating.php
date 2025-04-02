<?php

namespace App\Services\Rating;

use App\Models\Rating;
use Illuminate\Support\Facades\DB;
use Exception;

class DeleteRating
{
    public function delete(int $ratingId): Rating
    {
        try {
            DB::beginTransaction();

            $rating = Rating::findOrFail($ratingId);
            $rating->delete();

            DB::commit();
            return $rating;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

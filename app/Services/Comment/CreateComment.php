<?php

namespace App\Services\Comment;

use App\Models\Comment;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateComment
{
    public function create(Model $model, string $content): Comment
    {
        try {
            DB::beginTransaction();

            $userId = Auth::id();
            if (!$userId) {
                throw new Exception('User not authenticated');
            }

            $comment = $model->comments()->create([
                'content' => $content,
                'user_id' => $userId,
            ]);

            DB::commit();

            return $comment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

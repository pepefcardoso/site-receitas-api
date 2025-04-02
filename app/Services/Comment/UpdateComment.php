<?php

namespace App\Services\Comment;

use App\Models\Comment;
use Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class UpdateComment
{
    public function update(int $commentId, string $newContent): Comment
    {
        try {
            DB::beginTransaction();

            $comment = Comment::findOrFail($commentId);

            $user_id = Auth::id();
            if (!$user_id) {
                throw new Exception('User not authenticated');
            }

            $comment->fill([
                'content' => $newContent,
                'user_id' => $user_id,
            ]);
            $comment->save();

            DB::commit();
            return $comment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

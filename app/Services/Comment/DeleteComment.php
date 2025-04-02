<?php

namespace App\Services\Comment;

use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use Exception;

class DeleteComment
{
    public function delete(string $commentId): Comment
    {
        try {
            DB::beginTransaction();

            $comment = Comment::findOrFail($commentId);
            $comment->delete();

            DB::commit();
            return $comment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

<?php

namespace App\Services\Comment;

use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use Exception;

class DeleteComment
{
    public function delete(string $commentId)
    {
        try {
            DB::beginTransaction();

            $comment = Comment::findOrFail($commentId);
            $comment->delete();

            DB::commit();
        return "Deleted comment with ID: $commentId";
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

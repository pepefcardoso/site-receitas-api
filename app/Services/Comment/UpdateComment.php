<?php

namespace App\Services\Comment;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class UpdateComment
{
    public function update(int $commentId, string $newContent): Comment
    {
        try {
            DB::beginTransaction();

            $comment = Comment::findOrFail($commentId);

            $comment->fill([
                'content' => $newContent,
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

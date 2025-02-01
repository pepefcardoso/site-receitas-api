<?php

namespace App\Services\Post;

use App\Models\Post;
use Illuminate\Support\Facades\DB;

class DeletePost
{
    public function delete(Post $post): Post|string
    {
        try {
            DB::beginTransaction();

            $post->topics()->detach();

            $post->delete();

            DB::commit();

            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
}

<?php

namespace App\Services\Post;

use App\Models\Post;
use Illuminate\Support\Facades\DB;

class UpdatePost
{
    public function update(int $id, array $data)
    {
        try {
            DB::beginTransaction();

            $post = Post::findOrFail($id);

            $post->update($data);

            DB::commit();

            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
}

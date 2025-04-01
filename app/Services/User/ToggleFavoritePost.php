<?php

namespace App\Services\User;

use App\Models\Post;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class ToggleFavoritePost
{
    public function toggle(array $data): User|string
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail(auth()->user()->id);

            $postId = data_get($data, 'post_id');
            $post = Post::findOrFail($postId);

            if ($user->favoritePosts()->where('post_id', $postId)->exists()) {
                $user->favoritePosts()->detach($post->id);
                $message = "Post removido dos favoritos";
            } else {
                $user->favoritePosts()->attach($post->id);
                $message = "Post favoritado com sucesso";
            }

            DB::commit();

            return $message;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

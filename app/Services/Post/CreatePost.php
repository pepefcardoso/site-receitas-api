<?php

namespace App\Services\Post;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreatePost
{
    public function create(array $data): Post|string
    {
        try {
            DB::beginTransaction();

            $user_id = Auth::id();
            $data['user_id'] = $user_id;

            $post = Post::create($data);

            DB::commit();

            return $post;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}

<?php

namespace App\Services\PostTopics;

use App\Models\PostTopic;
use Illuminate\Support\Facades\DB;

class DeletePostTopic
{
    public function delete(PostTopic $postTopic): PostTopic|string
    {
        try {
            DB::beginTransaction();

            if ($postTopic->posts()->exists()) {
                throw new \Exception('This topic cannot be deleted because it is associated with one or more posts');
            }

            $postTopic->delete();

            DB::commit();

            return $postTopic;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}

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

            $postTopic->delete();

            DB::commit();

            return $postTopic;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}

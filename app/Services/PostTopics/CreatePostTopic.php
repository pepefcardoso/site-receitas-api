<?php

namespace App\Services\PostTopics;

use App\Models\PostTopic;
use Illuminate\Support\Facades\DB;

class CreatePostTopic
{
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $PostTopic = PostTopic::create($data);

            DB::commit();

            return $PostTopic;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}

<?php

namespace App\Services\PostTopics;

use App\Models\PostTopic;

class ListPostTopic
{
    public function list(int $perPage = 10)
    {
        $query = PostTopic::query();

        return $query->paginate($perPage);
    }
}

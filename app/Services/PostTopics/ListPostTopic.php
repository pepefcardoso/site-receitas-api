<?php

namespace App\Services\PostTopics;

use App\Models\PostTopic;

class ListPostTopic
{
    public function list(array $filters = [], $perPage = 10)
    {
        return PostTopic::with('image')->get();
    }
}

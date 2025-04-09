<?php

namespace App\Services\PostTopics;

use App\Models\PostTopic;

class ListPostTopic
{
    public function list(int $perPage = 10)
    {
        return PostTopic::select('id', 'name')->paginate($perPage);
    }
}

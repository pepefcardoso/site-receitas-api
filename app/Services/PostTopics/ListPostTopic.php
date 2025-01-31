<?php

namespace App\Services\PostTopics;

use App\Models\PostTopic;

class ListPostTopic
{
    public function list(array $filters = [])
    {
        return PostTopic::all();
    }
}

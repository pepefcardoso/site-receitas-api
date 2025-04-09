<?php

namespace App\Services\PostTopics;

use App\Models\PostTopic;

class ShowPostTopic
{
    public function show($id)
    {
        return PostTopic::select('id', 'name')->findOrFail($id);
    }
}

<?php

namespace App\Services\PostTopics;

use App\Models\PostTopic;
class ShowPostTopic
{
    public function show($id)
    {
        return PostTopic::with('image')->findOrFail($id);
    }
}

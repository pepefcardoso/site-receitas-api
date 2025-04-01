<?php

namespace App\Services\PostTopics;

use App\Models\PostTopic;
use Illuminate\Support\Facades\DB;

class UpdatePostTopic
{

    public function update(PostTopic $PostTopic, array $data)
    {
        try {
            DB::beginTransaction();

            $PostTopic->fill($data);
            $PostTopic->save();

            DB::commit();

            return $PostTopic;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}

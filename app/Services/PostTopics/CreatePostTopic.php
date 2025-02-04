<?php

namespace App\Services\PostTopics;

use App\Models\PostTopic;
use App\Services\Image\CreateImage;
use Illuminate\Support\Facades\DB;

class CreatePostTopic
{
    protected CreateImage $createImageService;

    public function __construct(
        CreateImage $createImageService,
    ) {
        $this->createImageService = $createImageService;
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $PostTopic = PostTopic::create($data);

            $image = data_get($data, 'image');
            $this->createImageService->create($PostTopic, $image);

            DB::commit();

            return $PostTopic;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}

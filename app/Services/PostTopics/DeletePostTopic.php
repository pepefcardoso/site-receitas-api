<?php

namespace App\Services\PostTopics;

use App\Models\PostTopic;
use App\Services\Image\DeleteImage;
use Illuminate\Support\Facades\DB;

class DeletePostTopic
{
    protected DeleteImage $deleteImageService;

    public function __construct(
        DeleteImage $deleteImageService,
    ) {
        $this->deleteImageService = $deleteImageService;
    }

    public function delete(PostTopic $postTopic): PostTopic|string
    {
        try {
            DB::beginTransaction();

            if ($postTopic->posts()->exists()) {
                throw new \Exception('This topic cannot be deleted because it is associated with one or more posts');
            }

            if ($postTopic->image) {
                $imageId = $postTopic->image->id;
                $this->deleteImageService->delete($imageId);
            }

            $postTopic->delete();

            DB::commit();

            return $postTopic;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}

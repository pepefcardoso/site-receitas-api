<?php

namespace App\Services\Post;

use App\Models\Post;
use App\Services\Image\DeleteImage;
use Illuminate\Support\Facades\DB;

class DeletePost
{
    protected DeleteImage $deleteImageService;

    public function __construct(
        DeleteImage $deleteImageService,
    ) {
        $this->deleteImageService = $deleteImageService;
    }

    public function delete(Post $post): Post|string
    {
        try {
            DB::beginTransaction();

            $post->topics()->detach();

            if ($post->image) {
                $imageId = $post->image->id;
                $this->deleteImageService->delete($imageId);
            }

            $post->delete();

            DB::commit();

            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

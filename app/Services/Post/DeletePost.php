<?php

namespace App\Services\Post;

use App\Models\Post;
use App\Services\Image\DeleteImage;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DeletePost
{
    protected DeleteImage $deleteImageService;

    public function __construct(
        DeleteImage $deleteImageService,
    ) {
        $this->deleteImageService = $deleteImageService;
    }

    public function delete(Post $post): int
    {
        try {
            DB::beginTransaction();

            $postId = $post->id;
            $post->topics()->detach();

            if ($post->image) {
                $this->deleteImageService->delete($post->image);
            }

            $post->delete();

            Cache::forget("post_model.{$postId}");

            DB::commit();

            return $postId;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

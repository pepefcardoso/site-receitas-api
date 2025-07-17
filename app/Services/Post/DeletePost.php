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

    public function delete(int $postId): Post|string
    {
        try {
            DB::beginTransaction();

            $post = Post::findOrFail($postId);

            $post->topics()->detach();

            if ($post->image) {
                $imageId = $post->image->id;
                $this->deleteImageService->delete($imageId);
            }

            $post->delete();

            // Invalida o cache para este post específico
            Cache::forget("post_model.{$postId}");

            DB::commit();

            return $postId;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

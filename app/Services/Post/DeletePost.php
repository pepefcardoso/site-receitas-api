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

    /**
     * Deleta um post e seus dados associados de forma transacionalmente segura.
     *
     * @param Post $post
     * @return void
     * @throws Exception
     */
    public function delete(Post $post): void
    {
        $imagePathToDelete = $post->image?->path;
        $postId = $post->id;

        DB::beginTransaction();

        try {
            $post->topics()->detach();

            if ($post->image) {
                $this->deleteImageService->deleteDbRecord($post->image);
            }

            $post->delete();

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }

        if ($imagePathToDelete) {
            $this->deleteImageService->deleteFile($imagePathToDelete);
        }

        Cache::forget("post_model.{$postId}");
    }
}

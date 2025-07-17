<?php

namespace App\Services\Post;

use App\Models\Post;
use App\Services\Image\CreateImage;
use App\Services\Image\UpdateImage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UpdatePost
{
    protected UpdateImage $updateImageService;
    protected CreateImage $createImageService;

    public function __construct(
        UpdateImage $updateImageService,
        CreateImage $createImageService
    ) {
        $this->updateImageService = $updateImageService;
        $this->createImageService = $createImageService;
    }

    public function update(int $id, array $data)
    {
        try {
            DB::beginTransaction();

            $post = Post::findOrFail($id);

            $post->update($data);

            $topics = data_get($data, 'topics');
            $post->topics()->sync($topics);

            $newImageFile = data_get($data, 'image');
            if ($newImageFile) {
                $currentImage = $post->image;
                if ($currentImage) {
                    $this->updateImageService->update($currentImage->id, $newImageFile);
                } else {
                    $this->createImageService->create($post, $newImageFile);
                }
            }

            Cache::forget("post_model.{$id}");

            DB::commit();

            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

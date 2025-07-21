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

    public function update(Post $post, array $data)
    {
        try {
            DB::beginTransaction();

            $post->update($data);

            if (array_key_exists('topics', $data)) {
                $post->topics()->sync($data['topics']);
            }

            $newImageFile = data_get($data, 'image');
            if ($newImageFile) {
                if ($post->image) {
                    $this->updateImageService->update($post->image, $newImageFile);
                } else {
                    $this->createImageService->create($post, $newImageFile);
                }
            }

            Cache::forget("post_model.{$post->id}");

            DB::commit();

            $post->load(['user.image', 'category', 'topics', 'image']);

            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

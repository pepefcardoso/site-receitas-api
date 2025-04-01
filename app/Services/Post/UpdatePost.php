<?php

namespace App\Services\Post;

use App\Models\Post;
use App\Services\Image\UpdateImage;
use Illuminate\Support\Facades\DB;

class UpdatePost
{
    protected UpdateImage $updateImageService;

    public function __construct(
        UpdateImage $updateImageService,
    ) {
        $this->updateImageService = $updateImageService;
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
                $this->updateImageService->update($currentImage->id, $newImageFile);
            }

            DB::commit();

            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

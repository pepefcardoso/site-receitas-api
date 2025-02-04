<?php

namespace App\Services\PostCategory;

use App\Models\PostCategory;
use App\Services\Image\UpdateImage;
use Illuminate\Support\Facades\DB;

class UpdatePostCategory
{
    protected UpdateImage $updateImageService;

    public function __construct(
        UpdateImage $updateImageService,
    ) {
        $this->updateImageService = $updateImageService;
    }

    public function update(PostCategory $postCategory, array $data)
    {
        try {
            DB::beginTransaction();

            $postCategory->fill($data);
            $postCategory->save();

            $newImageFile = data_get($data, 'image');
            if ($newImageFile) {
                $currentImage = $postCategory->image;
                $this->updateImageService->update($currentImage->id, $newImageFile);
            }

            DB::commit();

            return $postCategory;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

}

<?php

namespace App\Services\PostCategory;

use App\Models\PostCategory;
use App\Services\Image\DeleteImage;
use Exception;
use Illuminate\Support\Facades\DB;

class DeletePostCategory
{
    protected DeleteImage $deleteImageService;

    public function __construct(
        DeleteImage $deleteImageService,
    ) {
        $this->deleteImageService = $deleteImageService;
    }

    public function delete(int $id)
    {
        try {
            DB::beginTransaction();

            $postCategory = PostCategory::findOrFail($id);

            if ($postCategory->posts()->exists()) {
                throw new Exception('This category cannot be deleted because it is associated with one or more posts.');
            }

            if ($postCategory->image) {
                $imageId = $postCategory->image->id;
                $this->deleteImageService->delete($imageId);
            }

            $postCategory->delete();

            DB::commit();

            return $postCategory;
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

}

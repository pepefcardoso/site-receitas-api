<?php

namespace App\Services\RecipeCategory;

use App\Models\RecipeCategory;
use App\Services\Image\DeleteImage;
use Illuminate\Support\Facades\DB;

class DeleteRecipeCategory
{
    protected DeleteImage $deleteImageService;

    public function __construct(
        DeleteImage $deleteImageService,
    ) {
        $this->deleteImageService = $deleteImageService;
    }

    public function delete(RecipeCategory $recipeCategory)
    {
        try {
            DB::beginTransaction();

            if ($recipeCategory->recipes()->exists()) {
                throw new \Exception('This category cannot be deleted because it is associated with one or more recipes');
            }

            if ($recipeCategory->image) {
                $imageId = $recipeCategory->image->id;
                $this->deleteImageService->delete($imageId);
            }

            $recipeCategory->delete();

            DB::commit();

            return $recipeCategory;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}

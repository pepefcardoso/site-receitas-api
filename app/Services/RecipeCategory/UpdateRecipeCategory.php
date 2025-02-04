<?php

namespace App\Services\RecipeCategory;

use App\Models\RecipeCategory;
use App\Services\Image\UpdateImage;
use Illuminate\Support\Facades\DB;

class UpdateRecipeCategory
{
    protected UpdateImage $updateImageService;

    public function __construct(
        UpdateImage $updateImageService,
    ) {
        $this->updateImageService = $updateImageService;
    }

    public function update(RecipeCategory $recipeCategory, array $data)
    {
        try {
            DB::beginTransaction();

            $recipeCategory->fill($data);
            $recipeCategory->save();

            $newImageFile = data_get($data, 'image');
            if ($newImageFile) {
                $currentImage = $recipeCategory->image;
                $this->updateImageService->update($currentImage->id, $newImageFile);
            }

            DB::commit();

            return $recipeCategory;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}

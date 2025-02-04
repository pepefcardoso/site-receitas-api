<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use App\Services\Image\DeleteImage;
use Exception;
use Illuminate\Support\Facades\DB;

class DeleteRecipe
{
    protected DeleteImage $deleteImageService;

    public function __construct(
        DeleteImage $deleteImageService,
    ) {
        $this->deleteImageService = $deleteImageService;
    }

    public function delete(Recipe $recipe): Recipe|string
    {
        try {
            DB::beginTransaction();

            $recipe->steps()->delete();
            $recipe->ingredients()->delete();
            $recipe->diets()->detach();

            if ($recipe->image) {
                $imageId = $recipe->image->id;
                $this->deleteImageService->delete($imageId);
            }

            $recipe->delete();

            DB::commit();

            return $recipe;
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}

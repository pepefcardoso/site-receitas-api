<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use App\Services\Image\DeleteImage;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DeleteRecipe
{
    public function __construct(
        protected DeleteImage $deleteImageService,
    ) {
    }

    /**
     * Deleta uma receita e todos os seus dados associados de forma transacional.
     *
     * @param Recipe $recipe
     * @return void
     * @throws Exception
     */
    public function delete(Recipe $recipe): void
    {
        $imagePathToDelete = $recipe->image?->path;

        DB::beginTransaction();

        try {
            $recipe->steps()->delete();
            $recipe->ingredients()->delete();
            $recipe->diets()->detach();

            if ($recipe->image) {
                $this->deleteImageService->deleteDbRecord($recipe->image);
            }

            $recipe->delete();

            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }

        if ($imagePathToDelete) {
            $this->deleteImageService->deleteFile($imagePathToDelete);
        }

        Cache::forget("recipe_model.{$recipe->id}");
    }
}

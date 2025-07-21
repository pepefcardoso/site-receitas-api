<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use App\Services\Image\DeleteImage;
use Exception;
use Illuminate\Support\Facades\Cache;
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
                $this->deleteImageService->delete($recipe->image);
            }

            $recipe->delete();

            Cache::forget("recipe_model.{$recipe->id}");

            DB::commit();

            return $recipe;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}

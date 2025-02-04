<?php

namespace App\Services\RecipeCategory;

use App\Models\RecipeCategory;
use App\Services\Image\CreateImage;
use Illuminate\Support\Facades\DB;

class CreateRecipeCategory
{
    protected CreateImage $createImageService;

    public function __construct(
        CreateImage $createImageService,
    ) {
        $this->createImageService = $createImageService;

    }
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $recipeCategory = RecipeCategory::create($data);

            $image = data_get($data, 'image');
            $this->createImageService->create($recipeCategory, $image);

            DB::commit();

            return $recipeCategory;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}

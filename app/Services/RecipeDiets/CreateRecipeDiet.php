<?php

namespace App\Services\RecipeDiets;

use App\Models\RecipeDiet;
use App\Services\Image\CreateImage;
use Illuminate\Support\Facades\DB;

class CreateRecipeDiet
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

            $recipeDiet = RecipeDiet::create($data);

            $image = data_get($data, 'image');
            $this->createImageService->create($recipeDiet, $image);

            DB::commit();

            return $recipeDiet;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}

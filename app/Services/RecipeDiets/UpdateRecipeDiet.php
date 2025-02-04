<?php

namespace App\Services\RecipeDiets;

use App\Models\RecipeDiet;
use App\Services\Image\UpdateImage;
use Illuminate\Support\Facades\DB;

class UpdateRecipeDiet
{
    protected UpdateImage $updateImageService;

    public function __construct(
        UpdateImage $updateImageService,
    ) {
        $this->updateImageService = $updateImageService;
    }

    public function update(RecipeDiet $recipeDiet, array $data)
    {
        try {
            DB::beginTransaction();

            $recipeDiet->fill($data);
            $recipeDiet->save();

            $newImageFile = data_get($data, 'image');
            if ($newImageFile) {
                $currentImage = $recipeDiet->image;
                $this->updateImageService->update($currentImage->id, $newImageFile);
            }

            DB::commit();

            return $recipeDiet;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}

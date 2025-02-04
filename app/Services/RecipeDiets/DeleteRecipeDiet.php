<?php

namespace App\Services\RecipeDiets;

use App\Models\RecipeDiet;
use App\Services\Image\DeleteImage;
use Illuminate\Support\Facades\DB;

class DeleteRecipeDiet
{
    protected DeleteImage $deleteImageService;

    public function __construct(
        DeleteImage $deleteImageService,
    ) {
        $this->deleteImageService = $deleteImageService;
    }

    public function delete(RecipeDiet $recipeDiet): RecipeDiet|string
    {
        try {
            DB::beginTransaction();

            if ($recipeDiet->recipes()->exists()) {
                throw new \Exception('This diet cannot be deleted because it is associated with one or more recipes');
            }

            if ($recipeDiet->image) {
                $imageId = $recipeDiet->image->id;
                $this->deleteImageService->delete($imageId);
            }

            $recipeDiet->delete();

            DB::commit();

            return $recipeDiet;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}

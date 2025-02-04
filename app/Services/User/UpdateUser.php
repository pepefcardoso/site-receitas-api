<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\Image\CreateImage;
use App\Services\Image\UpdateImage;
use Exception;
use Illuminate\Support\Facades\DB;

class UpdateUser
{
    protected CreateImage $createImageService;
    protected UpdateImage $updateImageService;

    public function __construct(
        CreateImage $createImageService,
        UpdateImage $updateImageService,
    ) {
        $this->createImageService = $createImageService;
        $this->updateImageService = $updateImageService;
    }

    public function update(int $id, array $data): User|string
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);

            $user->update($data);

            $newImageFile = data_get($data, 'image');
            if ($newImageFile) {
                if ($user->image) {
                    $currentImage = $user->image;
                    $this->updateImageService->update($currentImage->id, $newImageFile);
                } else {
                    $this->createImageService->create($newImageFile, $newImageFile);
                }
            }

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}

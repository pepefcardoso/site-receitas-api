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

    public function update(User $user, array $data): User
    {
        try {
            DB::beginTransaction();

            $user->update($data);

            $newImageFile = data_get($data, 'image');
            if ($newImageFile) {
                if ($user->image) {
                    $this->updateImageService->update($user->image, $newImageFile);
                } else {
                    $this->createImageService->create($user, $newImageFile);
                }
            }

            DB::commit();

            return $user->load('image');

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}

<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\Image\DeleteImage;
use Exception;
use Illuminate\Support\Facades\DB;

class DeleteUser
{
    protected DeleteImage $deleteImageService;

    public function __construct(
        DeleteImage $deleteImageService,
    ) {
        $this->deleteImageService = $deleteImageService;
    }

    public function delete(User $user): User|string
    {
        try {
            DB::beginTransaction();

            if ($user->image) {
                $imageId = $user->image->id;
                $this->deleteImageService->delete($imageId);
            }

            $user->delete();
            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}

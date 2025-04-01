<?php

namespace App\Services\User;

use App\Models\User;
use App\Notifications\DeletedUser;
use App\Services\Image\DeleteImage;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

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

            Notification::route('mail', $user->email)
                ->notify(new DeletedUser($user));

            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}

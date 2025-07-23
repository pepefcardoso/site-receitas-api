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
    public function __construct(
        protected DeleteImage $deleteImageService
    ) {
    }

    /**
     * Deleta um usuário e seus dados associados de forma transacionalmente segura.
     *
     * @param User $user
     * @return void
     * @throws Exception
     */
    public function delete(User $user): void
    {
        $imagePathToDelete = $user->image?->path;
        $userEmail = $user->email;
        $userName = $user->name;

        DB::beginTransaction();

        try {
            if ($user->image) {
                $this->deleteImageService->deleteDbRecord($user->image);
            }

            $user->delete();

            DB::commit();

        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }

        if ($imagePathToDelete) {
            $this->deleteImageService->deleteFile($imagePathToDelete);
        }

        $deletedUserInfo = (new User)->forceFill(['name' => $userName, 'email' => $userEmail]);
        Notification::route('mail', $userEmail)
            ->notify(new DeletedUser($deletedUserInfo));
    }
}

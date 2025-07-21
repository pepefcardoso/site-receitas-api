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
            $userEmail = $user->email;
            $userName = $user->name;

            DB::transaction(function () use ($user) {
                if ($user->image) {
                    $this->deleteImageService->delete($user->image);
                }
                $user->delete();
            });

            $deletedUserInfo = (new User)->forceFill(['name' => $userName, 'email' => $userEmail]);
            Notification::route('mail', $userEmail)
                ->notify(new DeletedUser($deletedUserInfo));

            return $user;
        } catch (Exception $e) {
            throw $e;
        }
    }
}

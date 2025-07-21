<?php

namespace App\Services\Image;

use App\Models\Image;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteImage
{
    public function delete(Image $image): Image
    {
        try {
            return DB::transaction(function () use ($image) {
                $this->deleteFromStorage($image);
                return tap($image)->delete();
            });
        } catch (Exception $e) {
            Log::error("Image deletion failed: {$image->id} - " . $e->getMessage());
            throw $e;
        }
    }

    private function deleteFromStorage(Image $image): void
    {
        $filePath = Image::$S3Directory . '/' . $image->name;

        if (Storage::disk('s3')->exists($filePath)) {
            Storage::disk('s3')->delete($filePath);
        }
    }
}

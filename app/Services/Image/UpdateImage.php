<?php

namespace App\Services\Image;

use App\Models\Image;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UpdateImage
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];

    public function update(int $imageId, UploadedFile $newFile): Image
    {
        try {
            $this->validateFile($newFile);

            return DB::transaction(function () use ($imageId, $newFile) {
                $image = Image::findOrFail($imageId);

                $newName = $newFile->hashName();
                $path = $this->storeNewFile($newFile, $newName);

                $this->deleteOldFile($image);

                $image->update([
                    'path' => $path,
                    'name' => $newName,
                ]);

                return $image->refresh();
            });
        } catch (Exception $e) {
            Log::error("Image update failed (ID: {$imageId}): {$e->getMessage()}", [
                'exception' => $e,
                'file' => $newFile->getClientOriginalName()
            ]);
            throw $e;
        }
    }

    private function validateFile(UploadedFile $file): void
    {
        if (!$file->isValid()) {
            throw new Exception('Invalid file upload');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            throw new Exception("Unsupported file extension: {$extension}");
        }
    }

    private function storeNewFile(UploadedFile $file, string $filename): string
    {
        $path = Storage::disk('s3')->putFile(Image::$S3Directory, $file);

        if (!$path) {
            throw new Exception('Failed to store new image file');
        }

        return $path;
    }

    private function deleteOldFile(Image $image): void
    {
        try {
            if (Storage::disk('s3')->exists($image->path)) {
                Storage::disk('s3')->delete($image->path);
            }
        } catch (Exception $e) {
            Log::warning("Failed to delete old image file (ID: {$image->id}): " . $e->getMessage());
        }
    }
}

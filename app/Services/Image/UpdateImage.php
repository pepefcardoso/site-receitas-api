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
    private const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public function update(int $imageId, UploadedFile $newFile): Image
    {
        try {
            $this->validateFile($newFile);

            return DB::transaction(function () use ($imageId, $newFile) {
                $image = Image::findOrFail($imageId);
                $newName = $this->generateFilename($newFile);
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

        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            throw new Exception('Unsupported file type');
        }

        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new Exception('File too large');
        }
    }

    private function generateFilename(UploadedFile $file): string
    {
        $extension = strtolower($file->extension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            $extension = 'dat';
        }
        return hash_file('sha256', $file->path()) . '.' . $extension;
    }

    private function storeNewFile(UploadedFile $file, string $filename): string
    {
        $fullPath = Image::$S3Directory . '/' . $filename;

        if (Storage::disk('s3')->exists($fullPath)) {
            throw new Exception('File already exists on storage');
        }

        $path = Storage::disk('s3')->putFileAs(
            Image::$S3Directory,
            $file,
            $filename
        );

        if (!$path) {
            throw new Exception('Failed to store new image file');
        }

        return $path;
    }

    private function deleteOldFile(Image $image): void
    {
        try {
            $oldPath = Image::$S3Directory . '/' . $image->name;
            if (Storage::disk('s3')->exists($oldPath)) {
                Storage::disk('s3')->delete($oldPath);
            }
        } catch (Exception $e) {
            Log::warning("Failed to delete old image file (ID: {$image->id}): " . $e->getMessage());
        }
    }
}

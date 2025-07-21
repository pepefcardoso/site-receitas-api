<?php

namespace App\Services\Image;

use App\Models\Image;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UpdateImage
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];

    /**
     * Atualiza uma imagem, substituindo o arquivo antigo por um novo.
     *
     * @param Image $image O modelo da imagem a ser atualizada.
     * @param UploadedFile $newFile O novo arquivo de imagem.
     * @return Image
     * @throws Exception
     */
    public function update(Image $image, UploadedFile $newFile): Image
    {
        $this->validateFile($newFile);

        $oldPath = $image->path;
        $newPath = null;

        try {
            $newPath = $this->storeNewFile($newFile);

            $image->update([
                'path' => $newPath,
                'name' => $newFile->hashName(),
            ]);

            $this->deleteOldFile($oldPath);

            return $image;
        } catch (Exception $e) {
            if ($newPath) {
                Storage::disk('s3')->delete($newPath);
            }

            Log::error("Image update failed (ID: {$image->id}): {$e->getMessage()}", [
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

    private function storeNewFile(UploadedFile $file): string
    {
        $path = Storage::disk('s3')->putFile(Image::$S3Directory, $file);

        if (!$path) {
            throw new Exception('Failed to store new image file');
        }

        return $path;
    }

    private function deleteOldFile(?string $path): void
    {
        if (!$path) {
            return;
        }

        try {
            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
        } catch (Exception $e) {
            Log::warning("Failed to delete old image file (Path: {$path}): " . $e->getMessage());
        }
    }
}

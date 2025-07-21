<?php

namespace App\Services\Image;

use App\Models\Image;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteImage
{
    /**
     * Remove o registro de uma imagem do banco de dados e o arquivo associado do S3.
     *
     * @param Image $image O modelo da imagem a ser deletada.
     * @return Image O modelo da imagem que foi deletado.
     * @throws Exception
     */
    public function delete(Image $image): Image
    {
        try {
            $filePath = $image->path;

            $deletedImage = tap($image)->delete();

            $this->deleteFromStorage($filePath);

            return $deletedImage;
        } catch (Exception $e) {
            Log::error("Image deletion failed: {$image->id} - " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove um arquivo do S3 se ele existir.
     *
     * @param string|null $filePath O caminho do arquivo a ser deletado.
     */
    private function deleteFromStorage(?string $filePath): void
    {
        if (!$filePath) {
            return;
        }

        $disk = Storage::disk('s3');

        if ($disk->exists($filePath)) {
            $disk->delete($filePath);
        }
    }
}

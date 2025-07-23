<?php

namespace App\Services\Image;

use App\Models\Image;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteImage
{
    /**
     * Remove o registro da imagem do banco de dados.
     *
     * @param Image $image O modelo da imagem a ser deletado.
     * @return string Retorna o path do arquivo que deverá ser deletado do storage.
     */
    public function deleteDbRecord(Image $image): string
    {
        $filePath = $image->path;

        $image->delete();

        return $filePath;
    }

    /**
     * Remove um arquivo do disco de armazenamento (S3).
     *
     * @param string|null $filePath O caminho do arquivo a ser deletado.
     */
    public function deleteFile(?string $filePath): void
    {
        if (!$filePath) {
            return;
        }

        try {
            $disk = Storage::disk(config('filesystems.default'));
            if ($disk->exists($filePath)) {
                $disk->delete($filePath);
            }
        } catch (Exception $e) {
            Log::warning("Falha ao deletar o arquivo de imagem (Path: {$filePath}): " . $e->getMessage());
        }
    }
}

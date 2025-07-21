<?php

namespace App\Services\Image;

use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CreateImage
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];

    /**
     * Valida, armazena um arquivo de imagem e cria o registro no banco de dados.
     *
     * @param Model $model O modelo ao qual a imagem pertence (ex: User, Post).
     * @param UploadedFile $file O arquivo de imagem enviado.
     * @return Image
     * @throws Exception
     */
    public function create(Model $model, UploadedFile $file): Image
    {
        if (!$file->isValid()) {
            throw new Exception('Invalid file upload');
        }

        $this->validateFileExtension($file);

        $disk = Storage::disk(config('filesystems.default'));
        $path = null;

        try {
            $path = $disk->putFile(Image::$S3Directory, $file);

            if (!$path) {
                throw new Exception('Failed to store file');
            }

            return $model->image()->create([
                'path' => $path,
                'name' => $file->hashName(),
                'user_id' => Auth::id(),
            ]);
        } catch (Exception $e) {
            if ($path) {
                $disk->delete($path);
            }

            throw new Exception('Falha ao processar a imagem: ' . $e->getMessage());
        }
    }

    /**
     * Valida se a extensão do arquivo está na lista de permitidas.
     */
    private function validateFileExtension(UploadedFile $file): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            throw new Exception("Unsupported file extension: {$extension}");
        }
    }
}

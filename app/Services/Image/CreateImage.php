<?php

namespace App\Services\Image;

use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreateImage
{
    public function create(Model $model, UploadedFile $file): Image
    {
        if (!$file->isValid()) {
            throw new Exception('Invalid file upload');
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new Exception('Unsupported file type');
        }

        $name = hash_file('sha256', $file->path()) . '.' . $file->extension();
        $path = null;

        try {
            $path = Storage::disk('s3')->putFileAs(
                Image::$S3Directory,
                $file,
                $name
            );

            if (!$path) {
                throw new Exception('Failed to store file');
            }

            return DB::transaction(function () use ($model, $path, $name) {
                return $model->image()->create([
                    'path' => $path,
                    'name' => $name,
                    'user_id' => Auth::id(),
                ]);
            });

        } catch (Exception $e) {
            if ($path) {
                Storage::disk('s3')->delete($path);
            }
            throw $e;
        }
    }
}

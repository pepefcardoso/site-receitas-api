<?php

namespace App\Services\Image;

use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class UpdateImage
{
    public function update(int $id, array $data): Image|string
    {
        try {
            DB::beginTransaction();

            $image = Image::findOrFail($id);

            $newFile = data_get($data, 'file');
            throw_if(!$newFile, new Exception('File not found'));

            $newName = uniqid() . '.' . $newFile->extension();
            $path = Storage::disk('s3')->putFileAs(Image::$S3Directory, $newFile, $newName);

            if ($path && $image->name !== $newName) {
                Storage::disk('s3')->delete(Image::$S3Directory . '/' . $image->name);
            } else {
                throw new Exception('Error uploading image');
            }

            $image->fill([
                'path' => $path,
                'name' => $newName,
            ]);
            $image->save();

            DB::commit();

            return $image;
        } catch (Exception $e) {
            DB::rollBack();

            return $e->getMessage();
        }
    }
}

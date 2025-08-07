<?php

namespace App\Services\Company;

use App\Models\Company;
use App\Services\Image\CreateImage;
use App\Services\Image\DeleteImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateCompany
{
    public function __construct(
        protected CreateImage $createImageService,
        protected DeleteImage $deleteImageService
    ) {
    }

    public function create(array $data): Company
    {
        $imageData = null;

        try {
            if ($imageFile = data_get($data, 'image')) {
                $imageData = $this->createImageService->uploadOnly($imageFile);
            }

            $company = DB::transaction(function () use ($data, $imageData) {
                $data['user_id'] = Auth::id();
                $company = Company::create($data);

                if ($imageData) {
                    $this->createImageService->createDbRecord($company, $imageData);
                }
            });

            return $company;
        } catch (Throwable $e) {
            if ($imageData) {
                Log::info('Rolling back file upload due to DB transaction failure.', [
                    'path' => $imageData['path'],
                    'error' => $e->getMessage(),
                ]);
                $this->deleteImageService->deleteFile($imageData['path']);
            }
            throw $e;
        }
    }
}

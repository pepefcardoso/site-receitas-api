<?php

namespace App\Services\Company;

use App\Models\Company;
use App\Services\Image\CreateImage;
use App\Services\Image\UpdateImage;
use Illuminate\Support\Facades\DB;
use Throwable;

class UpdateCompany
{
    public function __construct(
        protected UpdateImage $updateImageService,
        protected CreateImage $createImageService
    ) {
    }

    public function update(Company $company, array $data): Company
    {
        $newImageData = null;
        $oldImagePath = null;

        if ($newImageFile = data_get($data, 'image')) {
            $newImageData = $this->createImageService->uploadOnly($newImageFile);
        }

        try {
            DB::transaction(function () use ($company, $data, $newImageData, &$oldImagePath) {
                $company->update($data);

                if ($newImageData) {
                    if ($company->image) {
                        $oldImagePath = $this->updateImageService->updateDbRecord($company->image, $newImageData);
                    } else {
                        $this->createImageService->createDbRecord($company, $newImageData);
                    }
                }
            });
        } catch (Throwable $e) {
            if ($newImageData) {
                $this->updateImageService->deleteFile($newImageData['path']);
            }
            throw $e;
        }

        if ($oldImagePath) {
            $this->updateImageService->deleteFile($oldImagePath);
        }

        return $company->refresh();
    }
}

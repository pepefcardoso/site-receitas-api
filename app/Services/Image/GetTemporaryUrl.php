<?php

namespace App\Services\Image;

use App\Models\Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class GetTemporaryUrl
{
    private const DEFAULT_CACHE_TTL = 5;

    public function temporaryUrl(Image $image): string
    {
        $this->validateImage($image);

        $cacheKey = $this->getCacheKey($image);
        $cacheTtl = config('image.url_cache_ttl', self::DEFAULT_CACHE_TTL);

        return Cache::remember($cacheKey, $cacheTtl * 60, function () use ($image, $cacheTtl) {
            try {
                return Storage::disk('s3')->temporaryUrl(
                    $image->path,
                    Carbon::now()->addMinutes($cacheTtl)
                );
            } catch (\Exception $e) {
                throw $e;
            }
        });
    }

    private function getCacheKey(Image $image): string
    {
        return "image_url_{$image->id}";
    }

    private function validateImage(Image $image): void
    {
        if (empty($image->path)) {
            throw new \InvalidArgumentException("Image path is empty");
        }
    }
}

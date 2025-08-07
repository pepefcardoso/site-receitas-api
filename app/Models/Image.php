<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;

class Image extends Model
{
    use HasFactory;

    static public string $S3Directory = 'images';

    protected $appends = [
        'url',
    ];

    protected $fillable = ['name', 'path', 'imageable_id', 'imageable_type', 'user_id'];

    public static function createRules(): array
    {
        return [
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'imageable_id' => 'required|integer',
            'imageable_type' => 'required|string',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: function () {
                $diskName = config('filesystems.default');

                /** @var FilesystemAdapter $disk */
                $disk = Storage::disk($diskName);

                if ($diskName !== 's3') {
                    return $disk->url($this->path);
                }

                if (config('filesystems.disks.s3.bucket')) {
                    return $disk->temporaryUrl($this->path, now()->addMinutes(15));
                }

                return null;
            }
        );
    }
}

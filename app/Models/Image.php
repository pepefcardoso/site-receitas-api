<?php

namespace App\Models;

use App\Services\Image\GetTemporaryUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Image extends Model
{
    use HasFactory;

    static public string $S3Directory = 'app_images';

    protected $appends = [
        'url',
    ];

    protected $fillable = ['name', 'path', 'imageable_id', 'imageable_type', 'user_id'];

    public static function modelRules(): array
    {
        return [
            'id' => 'nullable|integer|exists:images,id',
            'file' => 'nullable',
            'user_id' => 'nullable|integer|exists:users,id',
        ];
    }

    public static function createRules(): array
    {
        return [
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'imageable_id' => 'required|integer',
            'imageable_type' => 'required|string',
            'user_id' => 'required|integer|exists:users,id',
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

    public function url(): Attribute
    {
        return Attribute::make(
            fn() => (new GetTemporaryUrl())->temporaryUrl($this)
        );
    }
}

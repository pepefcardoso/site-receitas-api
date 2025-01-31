<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    public mixed $user;

    protected $fillable = [
        'title',
        'summary',
        'content',
        'image_url',
        'category_id',
        'user_id',
    ];

    public static function createRules(): array
    {
        return [
            'title' => 'required|string|max:100',
            'summary' => 'required|string|max:255',
            'content' => 'required|string',
            'image_url' => 'required|url',
            'category_id' => 'required|exists:post_categories,id',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class);
    }
}

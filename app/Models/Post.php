<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;

    public mixed $user;
    protected $fillable = [
        'title',
        'summary',
        'content',
        'image_url',
    ];

    public static array $rules = [
        'title' => 'required|string|max:100',
        'summary' => 'required|string|max:255',
        'content' => 'required|string',
        'image_url' => 'required|url',
        'categories' => 'required|array',
        'categories.*' => 'required|integer|exists:post_categories,id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(PostCategory::class, 'rl_post_categories', 'post_id', 'post_category_id');
    }
}

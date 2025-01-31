<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
            'topics' => 'array|required',
            'topics.*' => 'exists:post_topics,id',
        ];
    }

    public static function updateRules(): array
    {
        return [
            'title' => 'required|string|max:100',
            'summary' => 'required|string|max:255',
            'content' => 'required|string',
            'image_url' => 'required|url',
            'category_id' => 'required|exists:post_categories,id',
            'topics' => 'array|required',
            'topics.*' => 'exists:post_topics,id',
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

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(PostTopic::class, 'rl_post_topics', 'post_id', 'post_topic_id');
    }
}

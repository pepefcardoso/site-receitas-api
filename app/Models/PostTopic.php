<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PostTopic extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'normalized_name', 'image_url'];

    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:post_topics',
            'normalized_name' => 'required|string|max:50|unique:post_topics',
            'image_url' => 'required|url',
        ];
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'rl_post_topics', 'post_topic_id', 'post_id');
    }
}

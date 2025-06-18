<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PostTopic extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'normalized_name'];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'rl_post_topics', 'post_topic_id', 'post_id');
    }
}

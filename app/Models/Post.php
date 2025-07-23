<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use HasFactory, Searchable;

    public const VALID_SORT_COLUMNS = ['title', 'created_at'];

    protected $fillable = [
        'title',
        'summary',
        'content',
        'category_id',
        'user_id',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        $this->load('category', 'topics', 'user');

        return [
            'id'      => (int) $this->id,
            'title'   => $this->title,
            'summary' => $this->summary,
            'content' => $this->content,
            'author'  => $this->user->name ?? null,
            'topics'  => $this->topics->pluck('name')->all(),
            'category_id' => $this->category_id,
            'user_id'     => $this->user_id,
            'created_at'  => $this->created_at->timestamp,
        ];
    }


    public function filterableAttributes(): array
{
    return ['category_id', 'user_id'];
}

public function sortableAttributes(): array
{
    return ['created_at', 'title'];
}

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($query) use ($searchTerm) {
                $query->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('topics', function ($query) use ($searchTerm) {
                        $query->where('name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query;
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

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'rl_user_favorite_posts', 'post_id', 'user_id');
    }
}

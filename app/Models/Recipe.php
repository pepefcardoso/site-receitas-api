<?php

namespace App\Models;

use App\Enum\RecipeDifficultyEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Validation\Rule;
use Laravel\Scout\Searchable;

class Recipe extends Model
{
    use HasFactory, Searchable;

    public const VALID_SORT_COLUMNS = ['title', 'created_at', 'time', 'difficulty'];

    protected $fillable = [
        'title',
        'description',
        'time',
        'portion',
        'difficulty',
        'category_id',
        'user_id',
    ];

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['diets'])) {
            $query->whereHas('diets', function ($query) use ($filters) {
                $query->whereIn('recipe_diets.id', $filters['diets']);
            });
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

    public function diets(): BelongsToMany
    {
        return $this->belongsToMany(RecipeDiet::class, 'rl_recipe_diets', 'recipe_id', 'recipe_diet_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(RecipeCategory::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(RecipeStep::class)->orderBy('order');
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
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
        return $this->belongsToMany(User::class, 'rl_user_favorite_recipes', 'recipe_id', 'user_id');
    }
}

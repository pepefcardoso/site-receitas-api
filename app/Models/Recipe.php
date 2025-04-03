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

class Recipe extends Model
{
    use HasFactory;

    public const VALID_SORT_COLUMNS = ['title', 'created_at', 'time', 'difficulty'];

    public mixed $user;

    protected $fillable = [
        'title',
        'description',
        'time',
        'portion',
        'difficulty',
        'category_id',
        'user_id',
    ];

    protected $casts = [
        'ingredients' => 'array',
        'steps' => 'array',
        'is_favorited' => 'boolean',
    ];

    protected $appends = ['is_favorited'];

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

    public static function createRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'time' => 'required|integer',
            'portion' => 'required|integer',
            'difficulty' => ['required', 'integer', Rule::enum(RecipeDifficultyEnum::class)],
            'category_id' => 'required|exists:recipe_categories,id',
            'diets' => 'array|required',
            'diets.*' => 'exists:recipe_diets,id',
            'steps' => 'required|array',
            ...RecipeStep::recipeRules(),
            'ingredients' => 'required|array',
            ...RecipeIngredient::recipeRules(),
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public static function updateRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'time' => 'required|integer',
            'portion' => 'required|integer',
            'difficulty' => ['required', 'integer', Rule::enum(RecipeDifficultyEnum::class)],
            'category_id' => 'required|exists:recipe_categories,id',
            'diets' => 'array|required',
            'diets.*' => 'exists:recipe_diets,id',
            'steps' => 'required|array',
            ...RecipeStep::recipeRules(),
            'ingredients' => 'required|array',
            ...RecipeIngredient::recipeRules(),
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public static function filtersRules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|exists:recipe_categories,id',
            'diets' => 'nullable|array',
            'diets.*' => 'integer|exists:recipe_diets,id',
            'order_by' => 'nullable|string|in:title,created_at,time,difficulty',
            'order_direction' => 'nullable|string|in:asc,desc',
            'user_id' => 'nullable|integer|exists:users,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function getIsFavoritedAttribute(): bool
    {
        return (bool) ($this->attributes['is_favorited'] ?? false);
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
        return $this->hasMany(RecipeStep::class);
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

    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 0;
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

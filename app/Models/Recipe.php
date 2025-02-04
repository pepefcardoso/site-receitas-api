<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Recipe extends Model
{
    use HasFactory;

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
    ];

    public static function createRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'time' => 'required|integer',
            'portion' => 'required|integer',
            'difficulty' => 'required|integer',
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
            'difficulty' => 'required|integer',
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
}

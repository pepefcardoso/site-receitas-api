<?php

namespace App\Models;

use App\Enum\RecipeDifficultyEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;

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
        'image',
        'category_id',
    ];

    protected $casts = [
        'ingredients' => 'array',
        'steps' => 'array',
    ];

    public static function createRules(): array
    {
        return array_merge([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'time' => 'required|integer',
            'portion' => 'required|integer',
            'difficulty' => 'required|string',
            'image' => 'required|url',
            'category_id' => 'required|exists:recipe_categories,id',
            'diets' => 'array|required',
            'diets.*' => 'exists:recipe_diets,id',
        ], self::ingredientRules('create'), self::stepRules('create'));
    }

    public static function updateRules(): array
    {
        return array_merge([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'time' => 'required|integer',
            'portion' => 'required|integer',
            'difficulty' => 'required|string',
            'image' => 'required|url',
            'category_id' => 'required|exists:recipe_categories,id',
            'diets' => 'array|required',
            'diets.*' => 'exists:recipe_diets,id',
        ], self::ingredientRules('update'), self::stepRules('update'));
    }

    protected static function ingredientRules(string $type): array
    {
        $rules = RecipeIngredient::{$type . 'Rules'}();
        return [
            'ingredients' => 'required|array',
            'ingredients.*' => $rules,
        ];
    }

    protected static function stepRules(string $type): array
    {
        $rules = RecipeStep::{$type . 'Rules'}();
        return [
            'steps' => 'required|array',
            'steps.*' => $rules,
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
}

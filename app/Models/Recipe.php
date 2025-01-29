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

    public static function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'time' => 'required|integer',
            'portion' => 'required|integer',
            'difficulty' => 'required|string',
            'image' => 'required|url',
            'category_id' => 'required|exists:recipe_categories,id',
            'ingredients' => 'required|array',
            'ingredients.*.quantity' => 'required|integer',
            'ingredients.*.name' => 'required|string',
            'ingredients.*.unit_id' => 'required|exists:recipe_units,id',
            'steps' => 'required|array',
            'steps.*.order' => 'required|integer',
            'steps.*.description' => 'required|string',
            'diets' => 'array|required',
            'diets.*' => 'exists:recipe_diets,id',
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

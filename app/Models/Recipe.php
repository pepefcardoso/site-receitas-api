<?php

namespace App\Models;

use App\Enum\RecipeDifficultyEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Recipe extends Model
{
    use HasFactory, SoftDeletes;

    public mixed $user;
    protected $fillable = [
        'name',
        'description',
        'time',
        'portion',
        'difficulty'
    ];

    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'time' => 'required|integer',
            'portion' => 'required|integer',
            'difficulty' => ['required', Rule::in(RecipeDifficultyEnum::cases())],
            'category_id' => 'required|exists:recipe_categories,id',
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

    public function category()
    {
        return $this->belongsTo(RecipeCategory::class);
    }
}

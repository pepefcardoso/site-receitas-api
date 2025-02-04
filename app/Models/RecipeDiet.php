<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class RecipeDiet extends Model
{
    /** @use HasFactory<RecipeDietFactory> */
    use HasFactory;

    protected $fillable = ['name', 'normalized_name'];

    public static function createRules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:post_categories',
            'normalized_name' => 'required|string|max:50|unique:post_categories',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public static function updateRules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:post_categories',
            'normalized_name' => 'required|string|max:50|unique:post_categories',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'rl_recipe_diets', 'recipe_diet_id', 'recipe_id');
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}

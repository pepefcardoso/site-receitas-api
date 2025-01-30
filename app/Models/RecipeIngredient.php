<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeIngredient extends Model
{
    /** @use HasFactory<\Database\Factories\RecipeIngredientFactory> */
    use HasFactory;

    protected $fillable = [
        'quantity',
        'name',
        'unit_id',
        'recipe_id'
    ];

    public static function createRules(): array
    {
        return [
            'quantity' => 'required|integer|min:1',
            'name' => 'required|string',
            'unit_id' => 'required|exists:recipe_units,id',
            'recipe_id' => 'required|exists:recipes,id',
        ];
    }

    public static function updateRules(): array
    {
        return [
            'quantity' => 'required|integer|min:1',
            'name' => 'required|string',
            'unit_id' => 'required|exists:recipe_units,id',
        ];
    }

    public static function recipeRules(): array
    {
        return [
            'ingredients.*.id' => 'nullable|exists:recipe_ingredients,id',
            'ingredients.*.name' => self::createRules()["name"],
            'ingredients.*.quantity' => self::createRules()["quantity"],
            'ingredients.*.unit_id' => self::createRules()["unit_id"],
        ];
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(RecipeUnit::class);
    }
}

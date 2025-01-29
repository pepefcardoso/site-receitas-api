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
        'recipe_id'
    ];

    public static function createRules(): array
    {
        return [
            'quantity' => 'required|integer',
            'name' => 'required|string',
            'unit_id' => 'required|exists:recipe_units,id',
        ];
    }

    public static function updateRules(): array
    {
        return [
            'id' => 'required|exists:recipe_ingredients',
            'quantity' => 'required|integer',
            'name' => 'required|string',
            'unit_id' => 'required|exists:recipe_units,id',
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

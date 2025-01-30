<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeStep extends Model
{
    /** @use HasFactory<\Database\Factories\RecipeStepFactory> */
    use HasFactory;

    protected $fillable = [
        'order',
        'description',
        'recipe_id'
    ];

    public static function createRules(): array
    {
        return [
            'order' => 'required|integer',
            'description' => 'required|string',
            'recipe_id' => 'required|exists:recipes,id',
        ];
    }

    public static function updateRules(): array
    {
        return [
            'order' => 'required|integer',
            'description' => 'required|string',
        ];
    }

    public static function recipeRules(): array
    {
        return [
            'steps.*.id' => 'nullable|exists:recipe_steps,id',
            'steps.*.order' => self::createRules()["order"],
            'steps.*.description' => self::createRules()["description"],
        ];
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}

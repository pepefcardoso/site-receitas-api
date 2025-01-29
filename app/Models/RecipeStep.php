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
        ];
    }

    public static function updateRules(): array
    {
        return [
            'order' => 'required|integer',
            'description' => 'required|string',
        ];
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}

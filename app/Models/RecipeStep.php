<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecipeStep extends Model
{
    /** @use HasFactory<\Database\Factories\RecipeStepFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order',
        'description',
        'recipe_id'
    ];

    public static function rules(): array
    {
        return [
            'order'=> 'required|integer',
            'description'=> 'required|string',
        ];
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeUnit extends Model
{
    /** @use HasFactory<\Database\Factories\RecipeUnitFactory> */
    use HasFactory;

    protected $fillable = ['name', 'normalized_name'];

    public static array $rules = [
        'name' => 'required|string|max:50|unique:recipe_diets',
        'normalized_name' => 'required|string|max:50|unique:recipe_diets',
    ];

    public function ingredients()
    {
        return $this->hasMany(RecipeIngredient::class);
    }
}

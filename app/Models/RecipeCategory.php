<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecipeCategory extends Model
{
    /** @use HasFactory<\Database\Factories\RecipeCategoryFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'normalized_name'];

    public static array $rules = [
        'name' => 'required|string|max:50|unique:recipe_diets',
        'normalized_name' => 'required|string|max:50|unique:recipe_diets',
    ];

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }
}

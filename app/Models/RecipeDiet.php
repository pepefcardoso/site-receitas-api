<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecipeDiet extends Model
{
    /** @use HasFactory<\Database\Factories\RecipeDietFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'normalized_name'];

    public static array $rules = [
        'name' => 'required|string|max:50|unique:recipe_diets',
        'normalized_name' => 'required|string|max:50|unique:recipe_diets',
    ];

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'rl_recipe_diets', 'recipe_diet_id', 'recipe_id');
    }
}

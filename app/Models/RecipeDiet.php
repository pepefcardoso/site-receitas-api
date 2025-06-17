<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RecipeDiet extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'normalized_name'];

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'rl_recipe_diets', 'recipe_diet_id', 'recipe_id');
    }
}

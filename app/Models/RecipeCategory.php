<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecipeCategory extends Model
{
    /** @use HasFactory<RecipeCategoryFactory> */
    use HasFactory;

    protected $fillable = ['name', 'normalized_name'];

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class, 'category_id');
    }
}

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

    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:recipe_categories',
            'normalized_name' => 'required|string|max:50|unique:recipe_categories',
        ];
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }
}

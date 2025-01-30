<?php

namespace App\Models;

use Database\Factories\RecipeUnitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecipeUnit extends Model
{
    /** @use HasFactory<RecipeUnitFactory> */
    use HasFactory;

    protected $fillable = ['name', 'normalized_name'];

    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:recipe_units,name',
            'normalized_name' => 'required|string|max:50|unique:recipe_units,normalized_name',
        ];
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }
}

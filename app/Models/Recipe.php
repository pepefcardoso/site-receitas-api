<?php

namespace App\Models;

use App\Enum\RecipeDifficultyEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Recipe extends Model
{
    use HasFactory, SoftDeletes;

    public mixed $user;
    protected $fillable = [
        'name',
        'description',
        'time',
        'portion',
        'difficulty'
    ];

    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'time' => 'required|integer',
            'portion' => 'required|integer',
            'difficulty' => ['required', Rule::in(RecipeDifficultyEnum::cases())]
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

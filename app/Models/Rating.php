<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    /** @use HasFactory<\Database\Factories\RatingFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rateable_id',
        'rateable_type',
        'rating',
    ];

    public static function rules(): array
    {
        return [
            'rateable_id' => 'required|integer',
            'rateable_type' => 'required|string',
            'rating' => 'required|integer|min:0|max:5',
        ];
    }

    public function rateable()
    {
        return $this->morphTo();
    }
}

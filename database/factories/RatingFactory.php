<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'rating' => $this->faker->numberBetween(0, 5),
            'rateable_id' => 1,
            'rateable_type' => 'App\Models\Post',
            'user_id' => 1,
        ];
    }
}

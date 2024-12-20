<?php

namespace Database\Factories;

use App\Enum\RecipeDifficultyEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'time' => $this->faker->numberBetween(10, 240),
            'portion' => $this->faker->numberBetween(1, 20),
            'difficulty' => $this->faker->randomElement(RecipeDifficultyEnum::cases()),
            'user_id' => $this->faker->numberBetween(1, 10),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\PostCategory;
use App\Models\PostTopic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = PostCategory::inRandomOrder()->first();

        return [
            'title' => $this->faker->sentence(),
            'summary' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(),
            'image_url' => $this->faker->imageUrl(),
            'category_id' => $category ? $category->id : null,
            'user_id' => 1,
        ];
    }

    /**
     * Configure the factory.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function configure()
    {
        return $this->afterCreating(function ($post) {
            $topics = PostTopic::inRandomOrder()->take(rand(1, 3))->pluck('id');
            $post->topics()->sync($topics);
        });
    }
}

<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Recipe;
use App\Models\Rating;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        $users = range(1, 11);
        $posts = Post::pluck('id');
        $recipes = Recipe::pluck('id');

        foreach ($users as $userId) {
            foreach ($posts as $postId) {
                Rating::factory()->create([
                    'user_id' => $userId,
                    'rateable_id' => $postId,
                    'rateable_type' => 'App\Models\Post',
                ]);
            }

            foreach ($recipes as $recipeId) {
                Rating::factory()->create([
                    'user_id' => $userId,
                    'rateable_id' => $recipeId,
                    'rateable_type' => 'App\Models\Recipe',
                ]);
            }
        }
    }
}

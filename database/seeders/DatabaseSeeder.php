<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PostCategorySeeder::class,
            PostTopicSeeder::class,
            PostSeeder::class,
            RecipeDietSeeder::class,
            RecipeCategorySeeder::class,
            RecipeUnitSeeder::class,
            RecipeSeeder::class,
            RecipeIngredientSeeder::class,
            RecipeStepSeeder::class,
            ImageSeeder::class,
        ]);
    }
}

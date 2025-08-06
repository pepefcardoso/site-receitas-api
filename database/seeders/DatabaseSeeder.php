<?php

namespace Database\Seeders;

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
            PostSeeder::class,
            RecipeSeeder::class,
            CompanySeeder::class,
            PlanSeeder::class,
            DatabaseSeeder::class,
        ]);
    }
}

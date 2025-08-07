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
            PaymentMethodSeeder::class,
            PlanSeeder::class,
            PostCategorySeeder::class,
            PostTopicSeeder::class,
            PostSeeder::class,
            RecipeDietSeeder::class,
            RecipeCategorySeeder::class,
            RecipeSeeder::class,
            RecipeUnitSeeder::class,
            UserSeeder::class,
        ]);
    }
}

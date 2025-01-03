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
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'birthday' => '1990-01-01',
            'phone' => '(11) 99999-9999',
            'password' => '123456',
        ]);

        $this->call([
            PostCategorySeeder::class,
            PostSeeder::class,
            UserSeeder::class,
            RecipeDietSeeder::class,
            RecipeCategorySeeder::class,
            RecipeSeeder::class,
        ]);
    }
}

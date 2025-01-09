<?php

namespace Database\Seeders;

use App\Models\RecipeUnit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RecipeUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RecipeUnit::factory(10)->create();
    }
}

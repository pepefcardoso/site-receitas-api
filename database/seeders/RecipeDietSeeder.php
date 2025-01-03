<?php

namespace Database\Seeders;

use App\Models\RecipeDiet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RecipeDietSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RecipeDiet::factory(10)->create();
    }
}

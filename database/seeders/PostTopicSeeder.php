<?php

namespace Database\Seeders;

use App\Models\PostTopic;
use Illuminate\Database\Seeder;

class PostTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PostTopic::factory(10)->create();
    }
}

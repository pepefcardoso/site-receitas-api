<?php

namespace Database\Seeders;

use App\Models\NewsletterCustomer;
use Database\Factories\NewsletterCustomerFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsletterCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NewsletterCustomer::factory(10)->create();
    }
}

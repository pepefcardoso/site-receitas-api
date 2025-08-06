<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $subscriptions = Subscription::all();

        if ($subscriptions->isEmpty()) {
            $this->command->info('No subscriptions found, skipping PaymentSeeder.');
            return;
        }

        foreach ($subscriptions as $subscription) {
            Payment::factory()
                ->count(3)
                ->for($subscription)
                ->create();
        }
    }
}

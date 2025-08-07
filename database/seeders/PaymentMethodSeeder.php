<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $methods = [
            ['name' => 'Pix'],
            ['name' => 'Boleto Bancário'],
            ['name' => 'Cartão de Crédito'],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['name' => $method['name']],
                ['slug' => Str::slug($method['name'])]
            );
        }
    }
}

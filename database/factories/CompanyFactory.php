<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Temperinho',
            'cnpj' => '00.000.000/0001-01',
            'email' => 'contato@temperinho.com',
            'phone' => '(48) 99115-5026',
            'address' => 'Rua do Temperinho, 123',
            'website' => 'https://www.temperinho.com',
            'user_id' => 1,
        ];
    }
}

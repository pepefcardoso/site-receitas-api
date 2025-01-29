<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::create([
            'name' => 'Test',
            'email' => 'test@test.com',
            'birthday' => '1990-01-01',
            'phone' => '48998742031',
            'password' => Hash::make('test1234'),
            'role' => 2,
        ]);
    }
}

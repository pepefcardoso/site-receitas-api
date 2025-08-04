<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => env('ADMIN_NAME', 'TestUser'),
            'email' => env('ADMIN_EMAIL', 'Test@test.com'),
            'birthday' => env('ADMIN_BIRTHDAY', '1990-01-01'),
            'phone' => env('ADMIN_PHONE', '51912345678'),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'userTest123')),
            'role' => env('ADMIN_ROLE', 3),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
    }
}

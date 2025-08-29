<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@furbabiessafety.com'],
            [
                'name' => 'Pawtel Admin',
                'phone' => '+8801234567890',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'address' => 'Pawtel Head Office',
                'city' => 'Dhaka',
                'status' => 'active',
            ]
        );

        $admin->assignRole('admin');
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::firstOrCreate(
            ['email' => 'admin@houseprice.ai'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Admin@12345'),
                'role' => 'admin',
                'phone' => '+91 98765 43210',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Demo User
        User::firstOrCreate(
            ['email' => 'demo@houseprice.ai'],
            [
                'name' => 'Demo Real Estate Investor',
                'password' => Hash::make('Demo@12345'),
                'role' => 'user',
                'phone' => '+91 98765 43211',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
    }
}

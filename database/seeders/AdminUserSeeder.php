<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::updateOrCreate(
            ['email' => 'admin@speedyindex.com'], // Check if email exists
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'), // Default password
                'email_verified_at' => now(),
                // 'role' => 'admin', // Uncomment if you have a 'role' column
            ]
        );

        $this->command->info('Admin user created successfully.');
        $this->command->info('Email: admin@speedyindex.com');
        $this->command->info('Password: password');
    }
}

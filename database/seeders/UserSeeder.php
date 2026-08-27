<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing users (optional - comment out if you want to keep existing)
        // User::truncate();

        // Create ADMIN user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@somapos.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin@somapos.com'),
            'role' => 'admin', // 👈 Admin role
            'remember_token' => Str::random(10),
        ]);

        // Create EMPLOYEE users (cashiers)
        User::create([
            'name' => 'Cynthia Kamau',
            'email' => 'cykamau@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('cykamau@gmail.com'),
            'role' => 'employee', // 👈 Employee role
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'name' => 'Ian Kamnganga',
            'email' => 'iankamnganga@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('iankamnganga@gmail.com'),
            'role' => 'employee', // 👈 Employee role
            'remember_token' => Str::random(10),
        ]);

        // Optional: Create a test employee
        User::create([
            'name' => 'Test Cashier',
            'email' => 'cashier@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'employee',
            'remember_token' => Str::random(10),
        ]);

        $this->command->info('✅ Users seeded successfully!');
        $this->command->info('👑 ADMIN: admin@somapos.com (Password: admin@somapos.com)');
        $this->command->info('👤 EMPLOYEE: cykamau@gmail.com (Password: cykamau@gmail.com)');
        $this->command->info('👤 EMPLOYEE: iankamnganga@gmail.com (Password: iankamnganga@gmail.com)');
        $this->command->info('👤 EMPLOYEE: cashier@test.com (Password: password)');
    }
}
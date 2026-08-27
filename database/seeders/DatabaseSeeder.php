<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            OutletSeeder::class,    // First create outlets
            UserSeeder::class,     // Then create users with roles
            ProductSeeder::class,  // Then create products
        ]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Category;
use App\Models\ReturnModel;

class OutletSeeder extends Seeder
{
    public function run(): void
    {
        // Create outlets
        $mainOutlet = Outlet::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Main Outlet',
                'location' => 'Headquarters',
                'contact_info' => '0712345678',
                'is_active' => true
            ]
        );

        $branchOutlet = Outlet::firstOrCreate(
            ['id' => 2],
            [
                'name' => 'Branch Outlet',
                'location' => 'Branch Location',
                'contact_info' => '0798765432',
                'is_active' => true
            ]
        );

        // Update existing records to belong to Main Outlet
        $models = [User::class, Product::class, Sale::class, Customer::class, Category::class, ReturnModel::class];
        
        foreach ($models as $model) {
            $model::withoutGlobalScopes()
                ->whereNull('outlet_id')
                ->update(['outlet_id' => $mainOutlet->id]);
        }

        $this->command->info('Outlets seeded successfully!');
        $this->command->info('Main Outlet ID: ' . $mainOutlet->id);
        $this->command->info('Branch Outlet ID: ' . $branchOutlet->id);
    }
}
<?php
// c:\xampp\htdocs\soma-pos\seed_outlets.php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Outlet;
use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Category;
use App\Models\ReturnModel;

$outlet = Outlet::firstOrCreate(['id' => 1], [
    'name' => 'Main Outlet',
    'location' => 'HQ',
    'contact_info' => '0712345678',
    'is_active' => true
]);

User::withoutGlobalScopes()->whereNull('outlet_id')->update(['outlet_id' => 1]);
Product::withoutGlobalScopes()->whereNull('outlet_id')->update(['outlet_id' => 1]);
Sale::withoutGlobalScopes()->whereNull('outlet_id')->update(['outlet_id' => 1]);
Customer::withoutGlobalScopes()->whereNull('outlet_id')->update(['outlet_id' => 1]);
Category::withoutGlobalScopes()->whereNull('outlet_id')->update(['outlet_id' => 1]);
ReturnModel::withoutGlobalScopes()->whereNull('outlet_id')->update(['outlet_id' => 1]);

echo "Outlets seeded and relationships updated.";

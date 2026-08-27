<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Wireless Headphones', 'sku' => 'WH-001', 'price' => 8000, 'cost' => 4500, 'stock_quantity' => 25, 'category' => 'Electronics'],
            ['name' => 'Running Shoes', 'sku' => 'RS-002', 'price' => 4500, 'cost' => 2500, 'stock_quantity' => 15, 'category' => 'Footwear'],
            ['name' => 'Smart Watch', 'sku' => 'SW-003', 'price' => 12000, 'cost' => 7000, 'stock_quantity' => 10, 'category' => 'Electronics'],
            ['name' => 'T-Shirt (Black)', 'sku' => 'TS-004', 'price' => 1500, 'cost' => 800, 'stock_quantity' => 50, 'category' => 'Clothing'],
            ['name' => 'Jeans (Blue)', 'sku' => 'JN-005', 'price' => 3500, 'cost' => 1800, 'stock_quantity' => 30, 'category' => 'Clothing'],
            ['name' => 'Wireless Mouse', 'sku' => 'WM-006', 'price' => 1800, 'cost' => 900, 'stock_quantity' => 5, 'category' => 'Electronics'],
            ['name' => 'Bluetooth Speaker', 'sku' => 'BS-007', 'price' => 3000, 'cost' => 1600, 'stock_quantity' => 3, 'category' => 'Electronics'],
            ['name' => 'Phone Charger', 'sku' => 'PC-008', 'price' => 1000, 'cost' => 450, 'stock_quantity' => 2, 'category' => 'Accessories'],
            ['name' => 'Backpack', 'sku' => 'BP-009', 'price' => 2800, 'cost' => 1400, 'stock_quantity' => 8, 'category' => 'Bags'],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}
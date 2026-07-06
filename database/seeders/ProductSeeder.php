<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Wireless Headphones', 'sku' => 'WH-001', 'price' => 79.99, 'cost' => 45.00, 'stock_quantity' => 25, 'category' => 'Electronics'],
            ['name' => 'Running Shoes', 'sku' => 'RS-002', 'price' => 89.99, 'cost' => 52.00, 'stock_quantity' => 15, 'category' => 'Footwear'],
            ['name' => 'Smart Watch', 'sku' => 'SW-003', 'price' => 199.99, 'cost' => 120.00, 'stock_quantity' => 10, 'category' => 'Electronics'],
            ['name' => 'T-Shirt (Black)', 'sku' => 'TS-004', 'price' => 24.99, 'cost' => 12.00, 'stock_quantity' => 50, 'category' => 'Clothing'],
            ['name' => 'Jeans (Blue)', 'sku' => 'JN-005', 'price' => 59.99, 'cost' => 30.00, 'stock_quantity' => 30, 'category' => 'Clothing'],
            ['name' => 'Wireless Mouse', 'sku' => 'WM-006', 'price' => 29.99, 'cost' => 15.00, 'stock_quantity' => 5, 'category' => 'Electronics'],
            ['name' => 'Bluetooth Speaker', 'sku' => 'BS-007', 'price' => 49.99, 'cost' => 28.00, 'stock_quantity' => 3, 'category' => 'Electronics'],
            ['name' => 'Phone Charger', 'sku' => 'PC-008', 'price' => 19.99, 'cost' => 8.00, 'stock_quantity' => 2, 'category' => 'Accessories'],
            ['name' => 'Backpack', 'sku' => 'BP-009', 'price' => 45.99, 'cost' => 22.00, 'stock_quantity' => 8, 'category' => 'Bags'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
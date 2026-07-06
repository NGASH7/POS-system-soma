<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Product([
            'name' => $row['name'],
            'sku' => $row['sku'],
            'barcode' => $row['barcode'] ?? null,
            'price' => $row['price'],
            'cost' => $row['cost'],
            'stock_quantity' => $row['stock_quantity'],
            'low_stock_threshold' => $row['low_stock_threshold'] ?? 5,
            'category' => $row['category'] ?? null,
            'is_active' => true
        ]);
    }
    
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'sku' => 'required|unique:products',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ];
    }
}
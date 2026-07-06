<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Product::with('category')->orderBy('name')->get();
    }
    
    public function headings(): array
    {
        return [
            'ID',
            'Product Name',
            'SKU',
            'Barcode',
            'Selling Price',
            'Cost Price',
            'Stock Quantity',
            'Low Stock Threshold',
            'Category',
            'Status',
            'Created Date'
        ];
    }
    
    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->sku,
            $product->barcode ?? 'N/A',
            $product->price,
            $product->cost,
            $product->stock_quantity,
            $product->low_stock_threshold,
            $product->category->name ?? 'Uncategorized',
            $product->is_active ? 'Active' : 'Inactive',
            $product->created_at->format('Y-m-d H:i:s')
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
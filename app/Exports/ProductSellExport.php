<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ProductSellExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        // Similar logic to productSell method but returning collection for export
        return Product::with(['category'])
            ->whereHas('saleItems', function($query) {
                $query->whereHas('sale', function($q) {
                    $q->whereBetween('created_at', [$this->startDate, $this->endDate->endOfDay()])
                      ->where('status', 'completed');
                });
            })
            ->get()
            ->map(function($product) {
                $saleItems = $product->saleItems()
                    ->whereHas('sale', function($query) {
                        $query->whereBetween('created_at', [$this->startDate, $this->endDate->endOfDay()])
                              ->where('status', 'completed');
                    })
                    ->get();

                return (object) [
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'total_quantity' => $saleItems->sum('quantity'),
                    'total_revenue' => $saleItems->sum(function($item) {
                        return $item->price * $item->quantity;
                    }),
                    'total_cost' => $saleItems->sum(function($item) {
                        return ($item->product->cost ?? 0) * $item->quantity;
                    }),
                    'total_profit' => $saleItems->sum(function($item) {
                        return ($item->price * $item->quantity) - (($item->product->cost ?? 0) * $item->quantity);
                    }),
                ];
            })
            ->filter(function($product) {
                return $product->total_quantity > 0;
            });
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'SKU',
            'Category',
            'Units Sold',
            'Revenue (KES)',
            'Cost (KES)',
            'Profit (KES)',
            'Margin (%)'
        ];
    }

    public function map($product): array
    {
        $margin = $product->total_revenue > 0 ? ($product->total_profit / $product->total_revenue) * 100 : 0;
        
        return [
            $product->name,
            $product->sku,
            $product->category,
            $product->total_quantity,
            number_format($product->total_revenue, 2),
            number_format($product->total_cost, 2),
            number_format($product->total_profit, 2),
            number_format($margin, 1) . '%'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A' => ['width' => 30],
            'B' => ['width' => 15],
            'C' => ['width' => 20],
            'D' => ['width' => 15],
            'E' => ['width' => 20],
            'F' => ['width' => 20],
            'G' => ['width' => 20],
            'H' => ['width' => 15],
        ];
    }
}
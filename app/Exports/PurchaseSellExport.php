<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class PurchaseSaleExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $reportType;

    public function __construct($startDate, $endDate, $reportType)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reportType = $reportType;
    }

    public function collection()
    {
        $sales = Sale::with(['items.product', 'user'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate->endOfDay()])
            ->where('status', 'completed')
            ->get();

        $purchases = DB::table('purchase_items')
            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->whereBetween('purchases.created_at', [$this->startDate, $this->endDate->endOfDay()])
            ->where('purchases.status', 'completed')
            ->select(
                'purchases.created_at',
                'purchase_items.product_id',
                'purchase_items.quantity',
                'purchase_items.cost_price',
                'purchases.total_amount'
            )
            ->get();

        $data = [];
        
        if ($this->reportType == 'daily') {
            for ($date = clone $this->startDate; $date <= $this->endDate; $date->modify('+1 day')) {
                $daySales = $sales->where('created_at', '>=', $date->copy()->startOfDay())
                    ->where('created_at', '<=', $date->copy()->endOfDay());
                
                $dayPurchases = $purchases->where('created_at', '>=', $date->copy()->startOfDay())
                    ->where('created_at', '<=', $date->copy()->endOfDay());

                $data[] = (object) [
                    'period' => $date->copy()->format('Y-m-d'),
                    'sales_count' => $daySales->count(),
                    'sales_amount' => $daySales->sum('paid'),
                    'purchases_count' => $dayPurchases->count(),
                    'purchases_amount' => $dayPurchases->sum('total_amount'),
                    'profit' => $daySales->sum('paid') - $dayPurchases->sum('total_amount'),
                ];
            }
        } else {
            // Similar logic for weekly/monthly
            // For brevity, this is simplified
            $data[] = (object) [
                'period' => $this->reportType . ' Summary',
                'sales_count' => $sales->count(),
                'sales_amount' => $sales->sum('paid'),
                'purchases_count' => $purchases->count(),
                'purchases_amount' => $purchases->sum('total_amount'),
                'profit' => $sales->sum('paid') - $purchases->sum('total_amount'),
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Period',
            'Sales Count',
            'Sales Amount (KES)',
            'Purchases Count',
            'Purchases Amount (KES)',
            'Net (KES)',
            'Profit/Loss (KES)',
            'Margin (%)'
        ];
    }

    public function map($row): array
    {
        $margin = $row->sales_amount > 0 ? ($row->profit / $row->sales_amount) * 100 : 0;
        
        return [
            $row->period,
            $row->sales_count,
            number_format($row->sales_amount, 2),
            $row->purchases_count,
            number_format($row->purchases_amount, 2),
            number_format($row->sales_amount - $row->purchases_amount, 2),
            number_format($row->profit, 2),
            number_format($margin, 1) . '%'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A' => ['width' => 20],
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
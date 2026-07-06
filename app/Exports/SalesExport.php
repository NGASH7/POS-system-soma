<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class SalesExport implements FromCollection, WithHeadings, WithMapping
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
        return Sale::with('user', 'customer')
            ->whereBetween('created_at', [$this->startDate, $this->endDate->endOfDay()])
            ->where('status', 'completed')
            ->get();
    }
    
    public function headings(): array
    {
        return [
            'Invoice No',
            'Date',
            'Cashier',
            'Customer',
            'Subtotal',
            'Tax',
            'Total',
            'Payment Method',
            'Status'
        ];
    }
    
    public function map($sale): array
    {
        return [
            $sale->invoice_no,
            $sale->created_at->format('Y-m-d H:i:s'),
            $sale->user->name,
            $sale->customer->name ?? 'Walk-in',
            $sale->subtotal,
            $sale->tax,
            $sale->total,
            ucfirst($sale->payment_method),
            $sale->status
        ];
    }
}
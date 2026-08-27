<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class SellPaymentExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $paymentMethod;

    public function __construct($startDate, $endDate, $paymentMethod = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->paymentMethod = $paymentMethod;
    }

    public function collection()
    {
        $query = Sale::with(['user', 'customer'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate->endOfDay()])
            ->where('status', 'completed');

        if ($this->paymentMethod) {
            $query->where('payment_method', $this->paymentMethod);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Invoice #',
            'Customer',
            'Payment Method',
            'Amount (KES)',
            'Status',
            'Processed By'
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->created_at->format('Y-m-d H:i'),
            $sale->invoice_number,
            $sale->customer->name ?? 'Walk-in Customer',
            ucwords(str_replace('_', ' ', $sale->payment_method)),
            number_format($sale->paid, 2),
            $sale->status,
            $sale->user->name ?? 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A' => ['width' => 20],
            'B' => ['width' => 15],
            'C' => ['width' => 25],
            'D' => ['width' => 20],
            'E' => ['width' => 20],
            'F' => ['width' => 15],
            'G' => ['width' => 20],
        ];
    }
}
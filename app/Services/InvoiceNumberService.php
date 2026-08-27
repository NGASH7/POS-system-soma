<?php

namespace App\Services;

use App\Models\Outlet;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InvoiceNumberService
{
    /**
     * Generate unique invoice number (SOMA-BRANCH-YYYYMMDD-XXXX).
     */
    public function generate(): string
    {
        $branchCode = $this->branchCode();
        $date = date('Ymd');

        $invoices = Sale::withoutGlobalScopes()
            ->where('invoice_no', 'like', "SOMA-{$branchCode}-{$date}-%")
            ->pluck('invoice_no')
            ->toArray();

        $numbers = [];
        foreach ($invoices as $inv) {
            $parts = explode('-', $inv);
            $lastPart = end($parts);
            if (is_numeric($lastPart)) {
                $numbers[] = (int) $lastPart;
            }
        }

        $number = !empty($numbers) ? max($numbers) + 1 : 1;
        if ($number > 9999) {
            $number = 1;
        }

        $invoiceNo = 'SOMA-' . $branchCode . '-' . $date . '-' . str_pad((string) $number, 4, '0', STR_PAD_LEFT);

        $attempts = 0;
        while (Sale::withoutGlobalScopes()->where('invoice_no', $invoiceNo)->exists() && $attempts < 100) {
            $number++;
            if ($number > 9999) {
                $number = 1;
            }
            $invoiceNo = 'SOMA-' . $branchCode . '-' . $date . '-' . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
            $attempts++;
        }

        return $invoiceNo;
    }

    private function branchCode(): string
    {
        try {
            $user = Auth::user();
            $outletId = $user?->isAdmin()
                ? session('active_outlet_id', $user->outlet_id ?? 1)
                : ($user->outlet_id ?? null);

            $outlet = $outletId ? Outlet::find($outletId) : null;
            if ($outlet) {
                $cleanName = preg_replace('/[^a-zA-Z]/', '', $outlet->name);
                $code = strtoupper(substr($cleanName, 0, 3));
                if (strlen($code) >= 2) {
                    return $code;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Could not get outlet for branch code: ' . $e->getMessage());
        }

        return 'SOM';
    }
}

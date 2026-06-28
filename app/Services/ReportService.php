<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    public function salesReport(array $filters): array
    {
        $query = Invoice::query()->with('customer')->dateRange($filters['date_from'] ?? null, $filters['date_to'] ?? null);

        return [
            'total' => (float) $query->clone()->sum('total'),
            'count' => $query->clone()->count(),
            'rows' => $query->latest()->limit(100)->get(),
        ];
    }

    public function customerReport(array $filters): array
    {
        return ['rows' => Customer::query()->withCount('invoices')->withSum('invoices', 'total')->latest()->limit(100)->get()];
    }

    public function paymentReport(array $filters): array
    {
        $query = Payment::query()->with(['invoice', 'customer'])
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('payment_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('payment_date', '<=', $date));

        return ['total' => (float) $query->clone()->sum('amount'), 'rows' => $query->latest()->limit(100)->get()];
    }

    public function outstandingInvoicesReport(array $filters): array
    {
        return ['rows' => Invoice::query()->with('customer')->where('balance_amount', '>', 0)->latest()->limit(100)->get()];
    }

    public function exportSalesReportCsv(array $filters): string
    {
        $rows = $this->salesReport($filters)['rows'];
        $csv = "Invoice,Customer,Date,Total,Balance\n";
        foreach ($rows as $invoice) {
            $csv .= "{$invoice->invoice_number},{$invoice->customer->name},{$invoice->invoice_date->toDateString()},{$invoice->total},{$invoice->balance_amount}\n";
        }
        $path = 'exports/sales-report.csv';
        Storage::put($path, $csv);

        return Storage::path($path);
    }

    public function exportPaymentReportCsv(array $filters): string
    {
        $rows = $this->paymentReport($filters)['rows'];
        $csv = "Invoice,Customer,Date,Method,Amount\n";
        foreach ($rows as $payment) {
            $csv .= "{$payment->invoice->invoice_number},{$payment->customer->name},{$payment->payment_date->toDateString()},{$payment->payment_method->value},{$payment->amount}\n";
        }
        $path = 'exports/payment-report.csv';
        Storage::put($path, $csv);

        return Storage::path($path);
    }
}

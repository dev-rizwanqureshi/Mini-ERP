<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Support\ReportPeriod;
use App\Support\TableQuery;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    public function salesReport(array $filters): array
    {
        $range = ReportPeriod::fromFilters($filters);
        $query = Invoice::query()->with('customer')->dateRange($range['date_from'], $range['date_to']);

        TableQuery::applySort($query, $filters, [
            'invoice_date' => 'invoice_date',
            'invoice_number' => 'invoice_number',
            'status' => 'status',
            'total' => 'total',
            'balance_amount' => 'balance_amount',
            'customer' => fn ($query, string $direction) => $query->orderBy(Customer::query()->select('name')->whereColumn('customers.id', 'invoices.customer_id'), $direction),
        ], 'invoice_date', 'desc');

        return [
            'total' => (float) $query->clone()->sum('total'),
            'count' => $query->clone()->count(),
            'balance' => (float) $query->clone()->sum('balance_amount'),
            'average' => round((float) $query->clone()->avg('total'), 2),
            'range' => $this->publicRange($range),
            'series' => $this->invoiceSeries($range),
            'statusBreakdown' => $this->invoiceStatusBreakdown($range),
            'rows' => $query->limit(200)->get(),
        ];
    }

    public function customerReport(array $filters): array
    {
        $range = ReportPeriod::fromFilters($filters);
        $query = Customer::query()
            ->when($filters['search'] ?? null, fn ($query, string $term) => $query->where(function ($query) use ($term): void {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('company_name', 'like', "%{$term}%");
            }))
            ->withCount(['invoices' => fn ($query) => $query->dateRange($range['date_from'], $range['date_to'])])
            ->withSum(['invoices as invoices_sum_total' => fn ($query) => $query->dateRange($range['date_from'], $range['date_to'])], 'total')
            ->withSum(['payments as payments_sum_amount' => fn ($query) => $query->whereDate('payment_date', '>=', $range['date_from'])->whereDate('payment_date', '<=', $range['date_to'])], 'amount');

        TableQuery::applySort($query, $filters, [
            'name' => 'name',
            'company_name' => 'company_name',
            'invoices_count' => 'invoices_count',
            'invoices_sum_total' => 'invoices_sum_total',
            'payments_sum_amount' => 'payments_sum_amount',
            'created_at' => 'created_at',
        ], 'invoices_sum_total', 'desc');

        $rows = $query->limit(200)->get();

        return [
            'range' => $this->publicRange($range),
            'count' => $rows->count(),
            'total' => (float) $rows->sum('invoices_sum_total'),
            'paid' => (float) $rows->sum('payments_sum_amount'),
            'series' => $rows->take(10)->map(fn (Customer $customer) => [
                'label' => $customer->name,
                'value' => (float) ($customer->invoices_sum_total ?? 0),
            ])->values(),
            'rows' => $rows,
        ];
    }

    public function paymentReport(array $filters): array
    {
        $range = ReportPeriod::fromFilters($filters);
        $query = Payment::query()->with(['invoice', 'customer'])
            ->whereDate('payment_date', '>=', $range['date_from'])
            ->whereDate('payment_date', '<=', $range['date_to'])
            ->when($filters['search'] ?? null, fn ($query, string $term) => $query->where(function ($query) use ($term): void {
                $query->where('reference_number', 'like', "%{$term}%")
                    ->orWhere('payment_method', 'like', "%{$term}%")
                    ->orWhereHas('invoice', fn ($query) => $query->where('invoice_number', 'like', "%{$term}%"))
                    ->orWhereHas('customer', fn ($query) => $query->where('name', 'like', "%{$term}%"));
            }));

        TableQuery::applySort($query, $filters, [
            'payment_date' => 'payment_date',
            'amount' => 'amount',
            'payment_method' => 'payment_method',
            'customer' => fn ($query, string $direction) => $query->orderBy(Customer::query()->select('name')->whereColumn('customers.id', 'payments.customer_id'), $direction),
            'invoice' => fn ($query, string $direction) => $query->orderBy(Invoice::query()->select('invoice_number')->whereColumn('invoices.id', 'payments.invoice_id'), $direction),
        ], 'payment_date', 'desc');

        return [
            'total' => (float) $query->clone()->sum('amount'),
            'count' => $query->clone()->count(),
            'range' => $this->publicRange($range),
            'series' => $this->paymentSeries($range),
            'rows' => $query->limit(200)->get(),
        ];
    }

    public function outstandingInvoicesReport(array $filters): array
    {
        $range = ReportPeriod::fromFilters($filters);
        $query = Invoice::query()
            ->with('customer')
            ->where('balance_amount', '>', 0)
            ->dateRange($range['date_from'], $range['date_to'])
            ->when($filters['search'] ?? null, fn ($query, string $term) => $query->where(function ($query) use ($term): void {
                $query->where('invoice_number', 'like', "%{$term}%")
                    ->orWhereHas('customer', fn ($query) => $query->where('name', 'like', "%{$term}%"));
            }));

        TableQuery::applySort($query, $filters, [
            'invoice_date' => 'invoice_date',
            'due_date' => 'due_date',
            'invoice_number' => 'invoice_number',
            'customer' => fn ($query, string $direction) => $query->orderBy(Customer::query()->select('name')->whereColumn('customers.id', 'invoices.customer_id'), $direction),
            'total' => 'total',
            'balance_amount' => 'balance_amount',
        ], 'due_date', 'asc');

        return [
            'total' => (float) $query->clone()->sum('balance_amount'),
            'count' => $query->clone()->count(),
            'range' => $this->publicRange($range),
            'series' => $this->invoiceBalanceSeries($range),
            'rows' => $query->limit(200)->get(),
        ];
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

    private function publicRange(array $range): array
    {
        return collect($range)->except(['start', 'end'])->all();
    }

    private function invoiceSeries(array $range): array
    {
        return $this->timeSeries($range, fn (CarbonImmutable $start, CarbonImmutable $end): array => [
            'value' => (float) Invoice::query()->whereBetween('invoice_date', [$start->toDateString(), $end->toDateString()])->sum('total'),
            'count' => Invoice::query()->whereBetween('invoice_date', [$start->toDateString(), $end->toDateString()])->count(),
        ]);
    }

    private function paymentSeries(array $range): array
    {
        return $this->timeSeries($range, fn (CarbonImmutable $start, CarbonImmutable $end): array => [
            'value' => (float) Payment::query()->whereBetween('payment_date', [$start->toDateString(), $end->toDateString()])->sum('amount'),
        ]);
    }

    private function invoiceBalanceSeries(array $range): array
    {
        return $this->timeSeries($range, fn (CarbonImmutable $start, CarbonImmutable $end): array => [
            'value' => (float) Invoice::query()->where('balance_amount', '>', 0)->whereBetween('invoice_date', [$start->toDateString(), $end->toDateString()])->sum('balance_amount'),
        ]);
    }

    private function invoiceStatusBreakdown(array $range): array
    {
        return collect(InvoiceStatus::cases())->map(fn (InvoiceStatus $status) => [
            'label' => $status->label(),
            'value' => Invoice::query()
                ->where('status', $status)
                ->dateRange($range['date_from'], $range['date_to'])
                ->count(),
        ])->values()->all();
    }

    private function timeSeries(array $range, callable $resolver): array
    {
        return collect($this->buckets($range))->map(function (array $bucket) use ($resolver): array {
            [$label, $start, $end] = $bucket;

            return [
                'label' => $label,
                ...$resolver($start, $end),
            ];
        })->all();
    }

    private function buckets(array $range): array
    {
        $start = $range['start'];
        $end = $range['end'];

        if ($range['period'] === 'year' || $start->diffInDays($end) > 45) {
            return $this->monthBuckets($start, $end);
        }

        return $this->dayBuckets($start, $end);
    }

    private function dayBuckets(CarbonImmutable $start, CarbonImmutable $end): array
    {
        $buckets = [];
        for ($date = $start->startOfDay(); $date->lte($end); $date = $date->addDay()) {
            $buckets[] = [$date->format('M j'), $date, $date->endOfDay()];
        }

        return $buckets;
    }

    private function monthBuckets(CarbonImmutable $start, CarbonImmutable $end): array
    {
        $buckets = [];
        for ($date = $start->startOfMonth(); $date->lte($end); $date = $date->addMonth()) {
            $buckets[] = [$date->format('M Y'), $date->startOfMonth(), $date->endOfMonth()];
        }

        return $buckets;
    }
}

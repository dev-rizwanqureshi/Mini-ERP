<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function getStats(): array
    {
        return Cache::remember('dashboard:stats', 300, fn () => [
            'customers' => Customer::query()->count(),
            'products' => Product::query()->count(),
            'invoices' => Invoice::query()->count(),
            'revenue' => (float) Payment::query()->sum('amount'),
            'pending' => (float) Invoice::query()->sum('balance_amount'),
            'overdue' => Invoice::query()->where('status', InvoiceStatus::Overdue)->count(),
        ]);
    }

    public function getMonthlySalesData(int $months = 12): array
    {
        return Cache::remember('dashboard:monthly_sales', 300, fn () => collect(range($months - 1, 0))->map(function (int $offset): array {
            $date = now()->subMonths($offset);

            return [
                'label' => $date->format('M Y'),
                'value' => (float) Invoice::query()->whereYear('invoice_date', $date->year)->whereMonth('invoice_date', $date->month)->sum('total'),
            ];
        })->values()->all());
    }

    public function getInvoiceStatusBreakdown(): array
    {
        return Cache::remember('dashboard:invoice_breakdown', 300, fn () => collect(InvoiceStatus::cases())->map(fn (InvoiceStatus $status) => [
            'label' => $status->label(),
            'value' => Invoice::query()->where('status', $status)->count(),
        ])->all());
    }

    public function getRecentInvoices(int $limit = 5)
    {
        return Invoice::query()->with('customer')->latest()->limit($limit)->get();
    }

    public function getRecentPayments(int $limit = 5)
    {
        return Payment::query()->with(['invoice', 'customer'])->latest()->limit($limit)->get();
    }

    public function getTopCustomers(int $limit = 5)
    {
        return Customer::query()->withSum('invoices', 'total')->orderByDesc('invoices_sum_total')->limit($limit)->get();
    }

    public function getLowStockProducts(int $limit = 5)
    {
        return Product::query()->lowStock()->limit($limit)->get();
    }
}

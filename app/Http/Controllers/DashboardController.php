<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request, DashboardService $dashboard): Response
    {
        if (! $request->user()?->isApproved()) {
            return Inertia::render('Account/PendingApproval');
        }

        return Inertia::render('Dashboard/Index', [
            'stats' => $dashboard->getStats(),
            'monthlySales' => $dashboard->getMonthlySalesData(),
            'statusBreakdown' => $dashboard->getInvoiceStatusBreakdown(),
            'recentInvoices' => $dashboard->getRecentInvoices(),
            'recentPayments' => $dashboard->getRecentPayments(),
            'topCustomers' => $dashboard->getTopCustomers(),
            'lowStockProducts' => $dashboard->getLowStockProducts(),
        ]);
    }
}

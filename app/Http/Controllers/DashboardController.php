<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(DashboardService $dashboard): Response
    {
        return Inertia::render('Dashboard/Index', [
            'stats' => $dashboard->getStats(),
            'monthlySales' => $dashboard->getMonthlySalesData(),
            'statusBreakdown' => $dashboard->getInvoiceStatusBreakdown(),
            'recentInvoices' => $dashboard->getRecentInvoices(),
            'recentPayments' => $dashboard->getRecentPayments(),
            'lowStockProducts' => $dashboard->getLowStockProducts(),
        ]);
    }
}

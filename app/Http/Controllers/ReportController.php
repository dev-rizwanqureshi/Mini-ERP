<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Support\TableQuery;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function sales(Request $request, ReportService $reports): Response
    {
        $this->authorizeReportView($request);

        return Inertia::render('Reports/Sales', [
            'report' => $reports->salesReport($request->all()),
            'filters' => TableQuery::filters($request, ['period', 'period_value', 'date_from', 'date_to', 'search', 'sort', 'direction']),
        ]);
    }

    public function customers(Request $request, ReportService $reports): Response
    {
        $this->authorizeReportView($request);

        return Inertia::render('Reports/Customers', [
            'report' => $reports->customerReport($request->all()),
            'filters' => TableQuery::filters($request, ['period', 'period_value', 'date_from', 'date_to', 'search', 'sort', 'direction']),
        ]);
    }

    public function payments(Request $request, ReportService $reports): Response
    {
        $this->authorizeReportView($request);

        return Inertia::render('Reports/Payments', [
            'report' => $reports->paymentReport($request->all()),
            'filters' => TableQuery::filters($request, ['period', 'period_value', 'date_from', 'date_to', 'search', 'sort', 'direction']),
        ]);
    }

    public function outstanding(Request $request, ReportService $reports): Response
    {
        $this->authorizeReportView($request);

        return Inertia::render('Reports/Outstanding', [
            'report' => $reports->outstandingInvoicesReport($request->all()),
            'filters' => TableQuery::filters($request, ['period', 'period_value', 'date_from', 'date_to', 'search', 'sort', 'direction']),
        ]);
    }

    public function export(string $type, Request $request, ReportService $reports)
    {
        abort_unless($request->user()?->canDo('reports.export'), 403);

        $path = $type === 'payments' ? $reports->exportPaymentReportCsv($request->all()) : $reports->exportSalesReportCsv($request->all());

        return response()->download($path);
    }

    private function authorizeReportView(Request $request): void
    {
        abort_unless($request->user()?->canDo('reports.viewAny') || $request->user()?->canDo('reports.view'), 403);
    }
}

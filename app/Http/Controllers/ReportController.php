<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function sales(Request $request, ReportService $reports): Response
    {
        $this->authorize('viewAny', \App\Models\Invoice::class);

        return Inertia::render('Reports/Sales', ['report' => $reports->salesReport($request->all()), 'filters' => $request->all()]);
    }

    public function customers(Request $request, ReportService $reports): Response
    {
        return Inertia::render('Reports/Customers', ['report' => $reports->customerReport($request->all())]);
    }

    public function payments(Request $request, ReportService $reports): Response
    {
        return Inertia::render('Reports/Payments', ['report' => $reports->paymentReport($request->all())]);
    }

    public function outstanding(Request $request, ReportService $reports): Response
    {
        return Inertia::render('Reports/Outstanding', ['report' => $reports->outstandingInvoicesReport($request->all())]);
    }

    public function export(string $type, Request $request, ReportService $reports)
    {
        $path = $type === 'payments' ? $reports->exportPaymentReportCsv($request->all()) : $reports->exportSalesReportCsv($request->all());

        return response()->download($path);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\PdfService;
use Illuminate\Http\Response;

class InvoicePdfController extends Controller
{
    public function __invoke(Invoice $invoice, PdfService $pdfService): Response
    {
        $this->authorize('download', $invoice);

        return $pdfService->streamInvoicePdf($invoice);
    }
}

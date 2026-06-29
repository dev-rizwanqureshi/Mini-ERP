<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    public function generateInvoicePdf(Invoice $invoice): string
    {
        $invoice->loadMissing(['customer', 'items.product', 'payments']);
        $path = "invoices/{$invoice->invoice_number}.pdf";
        Storage::put($path, Pdf::loadView($this->getInvoiceView($invoice), ['invoice' => $invoice])->output());

        return Storage::path($path);
    }

    public function streamInvoicePdf(Invoice $invoice): Response
    {
        $invoice->loadMissing(['customer', 'items.product', 'payments']);

        return Pdf::loadView($this->getInvoiceView($invoice), ['invoice' => $invoice])
            ->stream("{$invoice->invoice_number}.pdf", ['Attachment' => false]);
    }

    public function getInvoiceView(Invoice $invoice): string
    {
        return 'pdf.invoice';
    }
}

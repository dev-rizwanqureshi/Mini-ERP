<?php

namespace App\Jobs;

use App\Mail\InvoiceMailer;
use App\Models\Invoice;
use App\Services\PdfService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendInvoiceEmailJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 180, 600];

    public function __construct(public Invoice $invoice)
    {
        $this->onQueue('emails');
    }

    public function handle(PdfService $pdfService): void
    {
        $path = $pdfService->generateInvoicePdf($this->invoice);
        Mail::to($this->invoice->customer->email)->send(new InvoiceMailer($this->invoice, $path));
        $this->invoice->update(['sent_at' => now()]);
    }
}

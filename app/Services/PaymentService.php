<?php

namespace App\Services;

use App\DTOs\PaymentData;
use App\Events\PaymentReceived;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(private readonly InvoiceService $invoiceService) {}

    public function record(PaymentData $data, User $recorder): Payment
    {
        return DB::transaction(function () use ($data, $recorder): Payment {
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($data->invoiceId);
            abort_if($data->amount > (float) $invoice->balance_amount, 422, 'Payment amount cannot exceed invoice balance.');
            abort_unless($invoice->status->canReceivePayment(), 422, 'This invoice cannot receive payments.');

            $payment = Payment::query()->create([
                'invoice_id' => $invoice->id,
                'customer_id' => $data->customerId,
                'user_id' => $recorder->id,
                'payment_date' => $data->paymentDate,
                'amount' => $data->amount,
                'payment_method' => $data->paymentMethod,
                'reference_number' => $data->referenceNumber,
                'notes' => $data->notes,
            ]);

            $this->invoiceService->updateStatusAfterPayment($invoice->refresh());
            event(new PaymentReceived($payment));

            return $payment->load(['invoice', 'customer', 'user']);
        });
    }

    public function delete(Payment $payment): bool
    {
        return DB::transaction(function () use ($payment): bool {
            $invoice = $payment->invoice;
            $deleted = (bool) $payment->delete();
            $invoice->refresh()->recalculate();

            return $deleted;
        });
    }

    public function getPaymentSummaryForInvoice(Invoice $invoice): array
    {
        return [
            'paid_amount' => (float) $invoice->payments()->sum('amount'),
            'balance_amount' => (float) $invoice->balance_amount,
            'payments_count' => $invoice->payments()->count(),
        ];
    }
}

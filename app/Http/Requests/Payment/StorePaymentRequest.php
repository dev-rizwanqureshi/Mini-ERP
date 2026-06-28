<?php

namespace App\Http\Requests\Payment;

use App\Enums\PaymentMethod;
use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $invoice = Invoice::query()->find($this->invoice_id);
            if ($invoice && (float) $this->amount > (float) $invoice->balance_amount) {
                $validator->errors()->add('amount', "Payment amount ({$this->amount}) cannot exceed invoice balance ({$invoice->balance_amount}).");
            }
            if ($invoice && ! $invoice->status->canReceivePayment()) {
                $validator->errors()->add('invoice_id', "This invoice cannot receive payments in its current status ({$invoice->status->label()}).");
            }
        });
    }
}

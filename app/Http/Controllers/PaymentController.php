<?php

namespace App\Http\Controllers;

use App\DTOs\PaymentData;
use App\Enums\PaymentMethod;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Support\TableQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::query()
            ->with(['invoice', 'customer', 'user'])
            ->when($request->string('search')->toString(), fn ($query, string $term) => $query->where(function ($query) use ($term): void {
                $query->where('reference_number', 'like', "%{$term}%")
                    ->orWhere('payment_method', 'like', "%{$term}%")
                    ->orWhereHas('invoice', fn ($query) => $query->where('invoice_number', 'like', "%{$term}%"))
                    ->orWhereHas('customer', fn ($query) => $query->where('name', 'like', "%{$term}%"));
            }));

        TableQuery::applySort($query, $request, [
            'payment_date' => 'payment_date',
            'amount' => 'amount',
            'payment_method' => 'payment_method',
            'created_at' => 'created_at',
            'customer' => fn ($query, string $direction) => $query->orderBy(Customer::query()->select('name')->whereColumn('customers.id', 'payments.customer_id'), $direction),
            'invoice' => fn ($query, string $direction) => $query->orderBy(Invoice::query()->select('invoice_number')->whereColumn('invoices.id', 'payments.invoice_id'), $direction),
        ], 'payment_date', 'desc');

        return Inertia::render('Payments/Index', [
            'payments' => $query->paginate(15)->withQueryString(),
            'filters' => TableQuery::filters($request, ['search', 'sort', 'direction']),
        ]);
    }

    public function create(Invoice $invoice): Response
    {
        $this->authorize('create', Payment::class);

        return Inertia::render('Payments/Create', ['invoice' => $invoice->load('customer'), 'methods' => PaymentMethod::cases()]);
    }

    public function store(StorePaymentRequest $request, PaymentService $paymentService): RedirectResponse
    {
        $this->authorize('create', Payment::class);
        $payment = $paymentService->record(PaymentData::fromRequest($request), $request->user());

        return redirect()->route('invoices.show', $payment->invoice)->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment): Response
    {
        $this->authorize('view', $payment);

        return Inertia::render('Payments/Show', ['payment' => $payment->load(['invoice', 'customer', 'user'])]);
    }

    public function destroy(Payment $payment, PaymentService $paymentService): RedirectResponse
    {
        $this->authorize('delete', $payment);
        $paymentService->delete($payment);

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }
}

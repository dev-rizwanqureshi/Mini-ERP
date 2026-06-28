<?php

namespace App\Http\Controllers;

use App\DTOs\PaymentData;
use App\Enums\PaymentMethod;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Payment::class);

        return Inertia::render('Payments/Index', [
            'payments' => Payment::query()->with(['invoice', 'customer', 'user'])->latest()->paginate(15)->withQueryString(),
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

        return back()->with('success', 'Payment deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\DTOs\InvoiceData;
use App\Enums\InvoiceStatus;
use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\InvoiceService;
use App\Support\TableQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Invoice::class);

        return Inertia::render('Invoices/Index', [
            'invoices' => $this->invoiceService->paginate($request->only(['search', 'status', 'customer_id', 'date_from', 'date_to', 'sort', 'direction'])),
            'filters' => TableQuery::filters($request, ['search', 'status', 'customer_id', 'date_from', 'date_to', 'sort', 'direction']),
            'statuses' => InvoiceStatus::cases(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Invoice::class);

        return Inertia::render('Invoices/Create', [
            'customers' => Customer::query()->active()->orderBy('name')->get(['id', 'name', 'email']),
            'products' => Product::query()->active()->orderBy('name')->get(['id', 'name', 'sku', 'unit_price', 'tax_rate', 'stock_quantity']),
        ]);
    }

    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $this->authorize('create', Invoice::class);
        $invoice = $this->invoiceService->create(InvoiceData::fromRequest($request), $request->user());

        return redirect()->route('invoices.show', $invoice)->with('success', "Invoice {$invoice->invoice_number} created successfully.");
    }

    public function show(Invoice $invoice): Response
    {
        $this->authorize('view', $invoice);

        return Inertia::render('Invoices/Show', ['invoice' => $invoice->load(['customer', 'items.product', 'payments'])]);
    }

    public function edit(Invoice $invoice): Response
    {
        $this->authorize('update', $invoice);

        return Inertia::render('Invoices/Edit', [
            'invoice' => $invoice->load('items'),
            'customers' => Customer::query()->active()->orderBy('name')->get(['id', 'name', 'email']),
            'products' => Product::query()->active()->orderBy('name')->get(['id', 'name', 'sku', 'unit_price', 'tax_rate', 'stock_quantity']),
        ]);
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);
        $this->invoiceService->update($invoice, InvoiceData::fromRequest($request));

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->authorize('delete', $invoice);
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    public function send(Invoice $invoice): RedirectResponse
    {
        $this->authorize('send', $invoice);
        $this->invoiceService->markAsSent($invoice);

        return back()->with('success', 'Invoice marked as sent.');
    }

    public function cancel(Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);
        $this->invoiceService->cancel($invoice);

        return back()->with('success', 'Invoice cancelled.');
    }
}

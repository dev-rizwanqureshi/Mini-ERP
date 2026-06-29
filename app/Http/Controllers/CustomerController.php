<?php

namespace App\Http\Controllers;

use App\DTOs\CustomerData;
use App\Enums\CustomerStatus;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use App\Support\TableQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Customer::class);

        $query = Customer::query()
            ->withCount('invoices')
            ->search($request->string('search')->toString());

        TableQuery::applySort($query, $request, [
            'name' => 'name',
            'email' => 'email',
            'company_name' => 'company_name',
            'invoices_count' => 'invoices_count',
            'created_at' => 'created_at',
        ]);

        return Inertia::render('Customers/Index', [
            'customers' => $query->paginate(15)->withQueryString(),
            'filters' => TableQuery::filters($request, ['search', 'sort', 'direction']),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Customer::class);

        return Inertia::render('Customers/Create', ['statuses' => CustomerStatus::cases()]);
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $this->authorize('create', Customer::class);
        Customer::query()->create([...CustomerData::fromArray($request->validated())->toArray(), 'created_by' => $request->user()->id]);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer): Response
    {
        $this->authorize('view', $customer);

        return Inertia::render('Customers/Show', ['customer' => $customer->load('invoices')]);
    }

    public function edit(Customer $customer): Response
    {
        $this->authorize('update', $customer);

        return Inertia::render('Customers/Edit', ['customer' => $customer, 'statuses' => CustomerStatus::cases()]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->authorize('update', $customer);
        $customer->update(CustomerData::fromArray($request->validated())->toArray());

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $this->authorize('delete', $customer);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}

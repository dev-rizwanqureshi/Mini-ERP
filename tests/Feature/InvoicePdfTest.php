<?php

namespace Tests\Feature;

use App\Enums\CustomerStatus;
use App\Enums\ProductStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicePdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_pdf_route_returns_pdf_response(): void
    {
        $user = User::factory()->for($this->role('accountant', ['invoices.download']))->create();
        $customer = Customer::factory()->create(['status' => CustomerStatus::Active->value]);
        $product = Product::factory()->create(['status' => ProductStatus::Active->value]);
        $invoice = Invoice::factory()->for($customer)->for($user)->create([
            'subtotal' => 25,
            'total' => 25,
            'balance_amount' => 25,
        ]);

        InvoiceItem::query()->create([
            'invoice_id' => $invoice->id,
            'product_id' => $product->id,
            'description' => $product->name,
            'quantity' => 1,
            'unit_price' => 25,
            'tax_rate' => 0,
            'total' => 25,
        ]);

        $response = $this->actingAs($user)->get(route('invoices.pdf', $invoice));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('inline', (string) $response->headers->get('content-disposition'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    private function role(string $name, array $permissions): Role
    {
        return Role::query()->create([
            'name' => $name,
            'display_name' => str($name)->replace('_', ' ')->title()->toString(),
            'description' => "{$name} role",
            'permissions' => $permissions,
            'is_system' => false,
        ]);
    }
}

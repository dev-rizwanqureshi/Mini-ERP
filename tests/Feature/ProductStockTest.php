<?php

namespace Tests\Feature;

use App\DTOs\InvoiceData;
use App\DTOs\InvoiceItemData;
use App\Enums\CustomerStatus;
use App\Enums\ProductStatus;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_creation_records_opening_stock(): void
    {
        $user = User::factory()->for($this->role('inventory_admin', ['products.create']))->create();

        $this->actingAs($user)
            ->post(route('products.store'), [
                'name' => 'Widget',
                'sku' => 'WID-001',
                'description' => 'Starter widget',
                'unit_price' => 25,
                'cost_price' => 10,
                'stock_quantity' => 12,
                'low_stock_threshold' => 3,
                'unit' => 'pcs',
                'tax_rate' => 0,
                'status' => 'active',
            ])
            ->assertRedirect(route('products.index'));

        $product = Product::query()->where('sku', 'WID-001')->firstOrFail();

        $this->assertSame(12, (int) $product->stock_quantity);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'purchase',
            'quantity' => 12,
            'quantity_before' => 0,
            'quantity_after' => 12,
            'notes' => 'Opening stock',
        ]);
    }

    public function test_stock_adjustment_adds_quantity_and_records_transaction(): void
    {
        $user = User::factory()->for($this->role('inventory_admin', ['products.adjustStock']))->create();
        $product = Product::factory()->create(['stock_quantity' => 3]);

        $this->actingAs($user)
            ->post(route('products.stock-adjustments.store', $product), [
                'mode' => 'add',
                'quantity' => 7,
                'notes' => 'Supplier delivery',
            ])
            ->assertRedirect();

        $this->assertSame(10, (int) $product->refresh()->stock_quantity);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'purchase',
            'quantity' => 7,
            'quantity_before' => 3,
            'quantity_after' => 10,
            'notes' => 'Supplier delivery',
        ]);
    }

    public function test_stock_cannot_be_removed_below_zero(): void
    {
        $user = User::factory()->for($this->role('inventory_admin', ['products.adjustStock']))->create();
        $product = Product::factory()->create(['stock_quantity' => 3]);

        $this->actingAs($user)
            ->from(route('products.show', $product))
            ->post(route('products.stock-adjustments.store', $product), [
                'mode' => 'remove',
                'quantity' => 4,
                'notes' => 'Damaged stock',
            ])
            ->assertRedirect(route('products.show', $product))
            ->assertSessionHasErrors('quantity');

        $this->assertSame(3, (int) $product->refresh()->stock_quantity);
    }

    public function test_stock_section_filters_inventory_warnings(): void
    {
        $user = User::factory()->for($this->role('inventory_admin', ['products.viewAny']))->create();
        Product::factory()->create(['name' => 'Empty Bin', 'sku' => 'STK-OUT', 'stock_quantity' => 0, 'low_stock_threshold' => 5]);
        Product::factory()->create(['name' => 'Low Bin', 'sku' => 'STK-LOW', 'stock_quantity' => 3, 'low_stock_threshold' => 5]);
        Product::factory()->create(['name' => 'Soon Bin', 'sku' => 'STK-SOON', 'stock_quantity' => 8, 'low_stock_threshold' => 5]);
        Product::factory()->create(['name' => 'Healthy Bin', 'sku' => 'STK-OK', 'stock_quantity' => 30, 'low_stock_threshold' => 5]);

        $this->actingAs($user)
            ->get(route('stock.index', ['stock_filter' => 'below_10']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Stock/Index')
                ->has('products.data', 3)
                ->where('stats.out', 1)
                ->where('stats.low', 1)
                ->where('stats.soon', 1)
            );
    }

    public function test_invoice_validation_combines_duplicate_product_lines_for_stock(): void
    {
        $user = User::factory()->for($this->role('sales_user', ['invoices.create']))->create();
        $customer = Customer::factory()->create(['status' => CustomerStatus::Active->value]);
        $product = Product::factory()->create(['stock_quantity' => 10, 'status' => ProductStatus::Active->value]);

        $this->actingAs($user)
            ->from(route('invoices.create'))
            ->post(route('invoices.store'), $this->invoicePayload($customer, [
                ['product_id' => $product->id, 'description' => $product->name, 'quantity' => 6, 'unit_price' => 10],
                ['product_id' => $product->id, 'description' => $product->name, 'quantity' => 6, 'unit_price' => 10],
            ]))
            ->assertRedirect(route('invoices.create'))
            ->assertSessionHasErrors(['items.0.quantity', 'items.1.quantity']);

        $this->assertSame(10, (int) $product->refresh()->stock_quantity);
    }

    public function test_invoice_update_counts_existing_invoice_stock_as_available(): void
    {
        $user = User::factory()->for($this->role('sales_user', ['invoices.update', 'invoices.view']))->create();
        $customer = Customer::factory()->create(['status' => CustomerStatus::Active->value]);
        $product = Product::factory()->create(['stock_quantity' => 10, 'status' => ProductStatus::Active->value]);
        $invoice = app(InvoiceService::class)->create(new InvoiceData(
            customerId: $customer->id,
            invoiceDate: now()->toDateString(),
            dueDate: now()->addDays(30)->toDateString(),
            discountType: 'fixed',
            discountValue: 0,
            notes: null,
            terms: null,
            footer: null,
            currencyCode: 'USD',
            currencySymbol: '$',
            items: [new InvoiceItemData($product->id, $product->name, 8, 10, 0)],
        ), $user);

        $this->assertSame(2, (int) $product->refresh()->stock_quantity);

        $this->actingAs($user)
            ->put(route('invoices.update', $invoice), $this->invoicePayload($customer, [
                ['product_id' => $product->id, 'description' => $product->name, 'quantity' => 9, 'unit_price' => 10],
            ]))
            ->assertRedirect(route('invoices.show', $invoice));

        $this->assertSame(1, (int) $product->refresh()->stock_quantity);
    }

    private function invoicePayload(Customer $customer, array $items): array
    {
        return [
            'customer_id' => $customer->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'currency_code' => 'USD',
            'currency_symbol' => '$',
            'notes' => null,
            'terms' => null,
            'footer' => null,
            'items' => array_map(fn (array $item): array => $item + [
                'tax_rate' => 0,
                'discount_type' => 'fixed',
                'discount_value' => 0,
            ], $items),
        ];
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

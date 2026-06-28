<?php

namespace Database\Seeders;

use App\DTOs\InvoiceData;
use App\DTOs\InvoiceItemData;
use App\DTOs\PaymentData;
use App\Enums\PaymentMethod;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
            CustomerSeeder::class,
            ProductSeeder::class,
        ]);

        $user = User::query()->where('email', 'admin@erp.test')->firstOrFail();
        $invoiceService = app(InvoiceService::class);
        $paymentService = app(PaymentService::class);

        if (Invoice::query()->exists()) {
            return;
        }

        for ($i = 0; $i < 20; $i++) {
            $customer = Customer::query()->inRandomOrder()->first();
            $products = Product::query()->active()->where('stock_quantity', '>', 5)->inRandomOrder()->limit(2)->get();

            if (! $customer || $products->isEmpty()) {
                continue;
            }

            $invoice = $invoiceService->create(new InvoiceData(
                customerId: $customer->id,
                invoiceDate: now()->subDays(rand(1, 90))->toDateString(),
                dueDate: now()->addDays(rand(5, 45))->toDateString(),
                discountType: 'fixed',
                discountValue: rand(0, 25),
                notes: 'Seed invoice',
                terms: 'Net 30',
                footer: 'Thank you.',
                currencyCode: 'USD',
                currencySymbol: '$',
                items: $products->values()->map(fn (Product $product, int $index) => new InvoiceItemData(
                    productId: $product->id,
                    description: $product->name,
                    quantity: rand(1, 3),
                    unitPrice: (float) $product->unit_price,
                    taxRate: (float) $product->tax_rate,
                    sortOrder: $index,
                ))->all(),
            ), $user);

            if ($i % 3 !== 0 || $i % 4 === 0) {
                $invoiceService->markAsSent($invoice);
            }

            if ($i % 4 === 0) {
                $paymentService->record(new PaymentData($invoice->id, $customer->id, now()->toDateString(), (float) $invoice->total, PaymentMethod::BankTransfer, 'SEED-'.$i, 'Seed payment'), $user);
            }
        }
    }
}

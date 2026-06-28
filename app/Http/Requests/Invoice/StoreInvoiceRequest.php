<?php

namespace App\Http\Requests\Invoice;

use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:invoice_date'],
            'discount_type' => ['required', 'in:fixed,percentage'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'currency_code' => ['nullable', 'string', 'max:3'],
            'currency_symbol' => ['nullable', 'string', 'max:5'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'terms' => ['nullable', 'string', 'max:2000'],
            'footer' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'integer', 'exists:products,id'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'items.*.discount_type' => ['required', 'in:fixed,percentage'],
            'items.*.discount_value' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($this->discount_type === 'percentage' && (float) $this->discount_value > 100) {
                $validator->errors()->add('discount_value', 'Percentage discount cannot exceed 100%.');
            }

            foreach ($this->items ?? [] as $index => $item) {
                if (($item['discount_type'] ?? '') === 'percentage' && (float) ($item['discount_value'] ?? 0) > 100) {
                    $validator->errors()->add("items.{$index}.discount_value", 'Percentage discount cannot exceed 100%.');
                }

                if (! empty($item['product_id'])) {
                    $product = Product::query()->find($item['product_id']);
                    if ($product && ($product->status !== ProductStatus::Active || $product->stock_quantity < (float) $item['quantity'])) {
                        $validator->errors()->add("items.{$index}.quantity", "Insufficient active stock for {$product->name}.");
                    }
                }
            }
        });
    }
}

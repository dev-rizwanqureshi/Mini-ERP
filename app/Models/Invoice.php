<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['invoice_number', 'customer_id', 'user_id', 'invoice_date', 'due_date', 'subtotal', 'tax_amount', 'discount_type', 'discount_value', 'discount_amount', 'total', 'paid_amount', 'balance_amount', 'currency_code', 'currency_symbol', 'status', 'notes', 'terms', 'footer', 'sent_at', 'paid_at'])]
class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_amount' => 'decimal:2',
            'status' => InvoiceStatus::class,
            'sent_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function recalculate(): void
    {
        $this->loadMissing('items');

        $subtotal = round((float) $this->items->sum(fn (InvoiceItem $item) => (float) $item->quantity * (float) $item->unit_price), 2);
        $tax = round((float) $this->items->sum('tax_amount'), 2);
        $lineDiscount = round((float) $this->items->sum('discount_amount'), 2);
        $invoiceDiscount = $this->discount_type === 'percentage'
            ? round($subtotal * ((float) $this->discount_value / 100), 2)
            : round((float) $this->discount_value, 2);
        $total = max(0, round($subtotal + $tax - $lineDiscount - $invoiceDiscount, 2));
        $paid = round((float) $this->payments()->sum('amount'), 2);

        $this->forceFill([
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'discount_amount' => $invoiceDiscount + $lineDiscount,
            'total' => $total,
            'paid_amount' => $paid,
            'balance_amount' => max(0, round($total - $paid, 2)),
        ])->save();
    }

    public function scopeForCustomer(Builder $query, int $customerId): Builder
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByStatus(Builder $query, InvoiceStatus|string|null $status): Builder
    {
        return $query->when($status, fn (Builder $query) => $query->where('status', $status instanceof InvoiceStatus ? $status->value : $status));
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereIn('status', [InvoiceStatus::Sent, InvoiceStatus::Partial])
            ->whereDate('due_date', '<', today());
    }

    public function scopeDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        return $query
            ->when($from, fn (Builder $query) => $query->whereDate('invoice_date', '>=', $from))
            ->when($to, fn (Builder $query) => $query->whereDate('invoice_date', '<=', $to));
    }

    public function isEditable(): bool
    {
        return $this->status->canEdit();
    }

    public function isPaid(): bool
    {
        return $this->status === InvoiceStatus::Paid;
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && ! $this->isPaid();
    }

    public function getFormattedTotalAttribute(): string
    {
        return $this->currency_symbol.number_format((float) $this->total, 2);
    }
}

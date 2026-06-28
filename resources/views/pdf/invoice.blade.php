<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { color: #17201d; font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { border-bottom: 3px solid #0f766e; padding-bottom: 18px; }
        .brand { font-size: 24px; font-weight: 700; }
        .grid { display: table; width: 100%; }
        .col { display: table-cell; vertical-align: top; width: 50%; }
        .box { border: 1px solid #d6d3d1; margin-top: 18px; padding: 14px; }
        table { border-collapse: collapse; margin-top: 18px; width: 100%; }
        th { background: #0f766e; color: white; text-align: left; }
        th, td { border: 1px solid #d6d3d1; padding: 8px; }
        .right { text-align: right; }
        .badge { background: #ccfbf1; border-radius: 4px; color: #115e59; display: inline-block; padding: 4px 8px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">{{ \App\Models\Setting::where('key', 'company_name')->value('value') ?? config('app.name') }}</div>
        <div>{{ \App\Models\Setting::where('key', 'company_address')->value('value') }} · {{ \App\Models\Setting::where('key', 'company_phone')->value('value') }} · {{ \App\Models\Setting::where('key', 'company_email')->value('value') }}</div>
    </div>

    <div class="grid box">
        <div class="col">
            <h1>INVOICE</h1>
            <span class="badge">{{ $invoice->status->label() }}</span>
        </div>
        <div class="col right">
            <strong>Invoice #:</strong> {{ $invoice->invoice_number }}<br>
            <strong>Date:</strong> {{ $invoice->invoice_date->format('d M Y') }}<br>
            <strong>Due Date:</strong> {{ $invoice->due_date->format('d M Y') }}
        </div>
    </div>

    <div class="box">
        <strong>Bill To</strong><br>
        {{ $invoice->customer->name }}<br>
        {{ $invoice->customer->company_name }}<br>
        {{ $invoice->customer->address }} {{ $invoice->customer->city }} {{ $invoice->customer->country }}<br>
        @if($invoice->customer->tax_number) Tax No: {{ $invoice->customer->tax_number }} @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="right">Qty</th>
                <th class="right">Price</th>
                <th class="right">Tax</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="right">{{ $item->quantity }}</td>
                    <td class="right">{{ $invoice->currency_symbol }}{{ number_format((float) $item->unit_price, 2) }}</td>
                    <td class="right">{{ $item->tax_rate }}%</td>
                    <td class="right">{{ $invoice->currency_symbol }}{{ number_format((float) $item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <tr><td class="right">Subtotal</td><td class="right">{{ $invoice->currency_symbol }}{{ number_format((float) $invoice->subtotal, 2) }}</td></tr>
        <tr><td class="right">Discount</td><td class="right">-{{ $invoice->currency_symbol }}{{ number_format((float) $invoice->discount_amount, 2) }}</td></tr>
        <tr><td class="right">Tax</td><td class="right">{{ $invoice->currency_symbol }}{{ number_format((float) $invoice->tax_amount, 2) }}</td></tr>
        <tr><td class="right"><strong>Total</strong></td><td class="right"><strong>{{ $invoice->currency_symbol }}{{ number_format((float) $invoice->total, 2) }}</strong></td></tr>
        <tr><td class="right">Paid</td><td class="right">{{ $invoice->currency_symbol }}{{ number_format((float) $invoice->paid_amount, 2) }}</td></tr>
        <tr><td class="right">Balance</td><td class="right">{{ $invoice->currency_symbol }}{{ number_format((float) $invoice->balance_amount, 2) }}</td></tr>
    </table>

    <div class="box">
        <strong>Payment History</strong><br>
        @forelse($invoice->payments as $payment)
            {{ $payment->payment_date->format('d M Y') }} · {{ $payment->payment_method->label() }} · {{ $payment->reference_number }} · {{ $invoice->currency_symbol }}{{ number_format((float) $payment->amount, 2) }}<br>
        @empty
            No payments recorded.
        @endforelse
    </div>

    <div class="box">
        <strong>Notes:</strong> {{ $invoice->notes }}<br>
        <strong>Terms:</strong> {{ $invoice->terms }}<br>
        <strong>Bank:</strong> {{ \App\Models\Setting::where('key', 'invoice_bank_details')->value('value') }}
    </div>
</body>
</html>

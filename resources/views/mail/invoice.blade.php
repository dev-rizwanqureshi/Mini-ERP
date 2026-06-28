<!doctype html>
<html>
<body style="font-family: Arial, sans-serif; color: #17201d;">
    <div style="max-width: 620px; margin: 0 auto; padding: 24px;">
        <h1 style="color: #0f766e;">{{ config('app.name') }}</h1>
        <p>Hello {{ $invoice->customer->name }},</p>
        <p>Your invoice <strong>{{ $invoice->invoice_number }}</strong> is attached.</p>
        <div style="border: 1px solid #d6d3d1; padding: 16px; margin: 20px 0;">
            <p><strong>Date:</strong> {{ $invoice->invoice_date->format('d M Y') }}</p>
            <p><strong>Due:</strong> {{ $invoice->due_date->format('d M Y') }}</p>
            <p><strong>Total:</strong> {{ $invoice->currency_symbol }}{{ number_format((float) $invoice->total, 2) }}</p>
        </div>
        <p>Please use the payment instructions included in the invoice PDF.</p>
    </div>
</body>
</html>

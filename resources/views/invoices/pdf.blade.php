<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        .summary { margin-top: 20px; }
    </style>
</head>
<body>
<h1>Invoice #{{ $invoice->invoice_number }}</h1>
<p>{{ $invoice->customer?->name ?? $invoice->supplier?->name ?? 'N/A' }}</p>
<p>Warehouse: {{ $invoice->warehouse->name }}</p>

<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>unit Price</th>
            <th>Tax</th>
            <th>Discount</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $item)
        <tr>
            <td>{{ $item->product->name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ $item->unit_price }}</td>
            <td>{{ $item->tax_amount }}</td>
            <td>{{ $item->discount_amount }}</td>
            <td>{{ $item->total_price }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="summary">
    <p>Subtotal: {{ $invoice->sub_total }}</p>
    <p>Total Tax: {{ $invoice->tax_amount }}</p>
    <p>Total Discount: {{ $invoice->discount_amount }}</p>
    <p>Grand Total: {{ $invoice->grand_total }}</p>
</div>
</body>
</html>

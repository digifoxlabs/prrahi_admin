<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tax Invoice - {{ $order->invoice_no }}</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .container {
            width: 100%;
        }

        h2, h3 {
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .no-border td {
            border: none;
            padding: 3px;
        }

        .right { text-align: right; }
        .center { text-align: center; }
        .bold { font-weight: bold; }

        .small {
            font-size: 11px;
        }

        .signature {
            margin-top: 50px;
            text-align: right;
        }
    </style>
</head>

<body onload="window.print()">

<div class="container">

    <!-- ================= HEADER ================= -->
    <table class="no-border">
        <tr>
            <td>
                <h2>Tax Invoice</h2>
                <h3>{{ config('app.name') }}</h3>
                <p class="small">
                    {{ config('company.address_line_1') ?? '' }}<br>
                    {{ config('company.address_line_2') ?? '' }}<br>
                    GSTIN/UIN: {{ config('company.gst') ?? 'N/A' }}<br>
                    State Name: {{ config('company.state') ?? '' }}
                </p>
            </td>

            <td class="right">
                <p><strong>Invoice No:</strong> {{ $order->invoice_no }}</p>
                <p><strong>Dated:</strong> {{ \Carbon\Carbon::parse($order->invoice_date)->format('d-M-Y') }}</p>
            </td>
        </tr>
    </table>

    <!-- ================= BUYER / CONSIGNEE ================= -->
    <table>
        <tr>
            <td width="50%">
                <strong>Buyer (Bill To)</strong><br>
                {{ $order->distributor->firm_name }}<br>
                {!! nl2br(e($order->billing_address)) !!}<br>
                GSTIN: {{ $order->distributor->gst ?? 'N/A' }}
            </td>

            <td width="50%">
                <strong>Consignee (Ship To)</strong><br>
                {{ $order->distributor->firm_name }}<br>
                {!! nl2br(e($order->billing_address)) !!}<br>
                GSTIN: {{ $order->distributor->gst ?? 'N/A' }}
            </td>
        </tr>
    </table>

    <!-- ================= ITEMS ================= -->
    <table style="margin-top:10px;">
        <thead>
            <tr>
                <th>Sl</th>
                <th>Description of Goods</th>
                <th>HSN/SAC</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Disc %</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>
                        {{ $item->product->type === 'variant'
                            ? $item->product->parent->name
                            : $item->product->name }}

                        @if($item->product->attributes)
                            <span class="small">
                                —
                                {{ $item->product->attributes['fragrance'] ?? '' }}
                                @if(!empty($item->product->attributes['size']))
                                    ({{ $item->product->attributes['size'] }})
                                @endif
                            </span>
                        @endif
                    </td>
                    <td class="center">{{ $item->product->hsn_code ?? '-' }}</td>
                    <td class="center">
                        {{ $item->quantity }} {{ $item->base_unit }}
                    </td>
                    <td class="right">
                        {{ number_format($item->rate, 2) }}
                    </td>
                    <td class="center">
                        {{ number_format($item->discount_percent ?? 0, 2) }}%
                    </td>
                    <td class="right">
                        {{ number_format($item->total, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ================= TOTALS ================= -->
    <table style="margin-top:10px;">
        <tr>
            <td class="right bold">Sub Total</td>
            <td class="right" width="150">{{ number_format($order->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="right bold">Discount</td>
            <td class="right">{{ number_format($order->discount, 2) }}</td>
        </tr>
        <tr>
            <td class="right bold">CGST</td>
            <td class="right">{{ number_format($order->cgst, 2) }}</td>
        </tr>
        <tr>
            <td class="right bold">SGST</td>
            <td class="right">{{ number_format($order->sgst, 2) }}</td>
        </tr>
        <tr>
            <td class="right bold">Round Off</td>
            <td class="right">{{ number_format($order->round_off, 2) }}</td>
        </tr>
        <tr>
            <td class="right bold">Total</td>
            <td class="right bold">{{ number_format($order->total_amount, 2) }}</td>
        </tr>
    </table>

    <!-- ================= AMOUNT IN WORDS ================= -->
    <p style="margin-top:10px;">
        <strong>Amount Chargeable (in words):</strong><br>
        {{ \NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($order->total_amount) }} Only
    </p>

    <!-- ================= DECLARATION ================= -->
    <p class="small">
        Declaration:<br>
        We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.
    </p>

    <!-- ================= SIGNATURE ================= -->
    <div class="signature">
        <p>for <strong>{{ config('app.name') }}</strong></p>
        <p>Authorised Signatory</p>
    </div>

    <p class="center small">
        This is a Computer Generated Invoice
    </p>

</div>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt #{{ $transaction->id }}</title>
    <style>
        body {
            font-family: monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .total {
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
            text-align: right;
            font-weight: bold;
        }
        @media print {
            body {
                width: 100%;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>Apotek App</h2>
        <p>Date: {{ $transaction->transaction_date->format('Y-m-d') }}</p>
        <p>Transaction ID: #{{ $transaction->id }}</p>
    </div>

    <div class="items">
        @foreach($transaction->items as $item)
            <div class="item">
                <span>{{ $item->medicine->nama_obat }} (x{{ $item->quantity }})</span>
                <span>Rp {{ number_format($item->total_price, 0, ',', '.') }}</span>
            </div>
        @endforeach
    </div>

    <div class="total">
        Total: Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $sale->id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }
        .receipt-container {
            width: 80mm; /* Standard thermal printer width */
            padding: 10px;
            box-sizing: border-box;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        
        .header { margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 2px 0; font-size: 12px; }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 10px;
        }
        th { border-bottom: 1px dashed #000; padding-bottom: 4px; }
        th, td { text-align: left; padding: 2px 0; }
        .qty-col { width: 15%; text-align: center; }
        .price-col { width: 25%; text-align: right; }
        .total-col { width: 25%; text-align: right; }
        
        .totals-container {
            margin-top: 10px;
            font-size: 13px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .grand-total {
            font-size: 16px;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
        }
        
        .barcode {
            margin: 15px auto;
            text-align: center;
            font-family: 'Libre Barcode 39', sans-serif;
            font-size: 32px;
        }

        /* Hide everything else and remove margins when actually printing */
        @media print {
            body { margin: 0; padding: 0; background-color: #fff; }
            .receipt-container { width: 100%; padding: 0; }
            @page {
                margin: 0;
                size: 80mm auto; /* Thermal roll size */
            }
        }
        
        /* Non-print UI buttons */
        .actions {
            position: fixed;
            top: 20px;
            right: 20px;
        }
        .btn {
            background-color: #2563eb;
            color: #fff;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-family: sans-serif;
            margin-left: 10px;
            cursor: pointer;
            border: none;
        }
        .btn-secondary {
            background-color: #6b7280;
        }
        @media print {
            .actions { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="actions">
        <a href="{{ route('sales.create') }}" class="btn btn-secondary">New Sale</a>
        <button onclick="window.print()" class="btn">Print Receipt</button>
    </div>

    <div class="receipt-container">
        
        {{-- Header --}}
        <div class="header text-center">
            <h1>{{ config('app.name', 'POS System') }}</h1>
            <p>{{ $sale->branch->name ?? 'Main Store' }}</p>
            <p>{{ $sale->branch->address ?? '123 Main Street' }}</p>
            <p>Tel: {{ $sale->branch->phone ?? '555-0199' }}</p>
        </div>
        
        <div class="divider"></div>
        
        {{-- Sale Info --}}
        <div class="info-row">
            <span>Date:</span>
            <span>{{ $sale->created_at->format('Y-m-d H:i') }}</span>
        </div>
        <div class="info-row">
            <span>Receipt:</span>
            <span>#{{ str_pad($sale->id, 8, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="info-row">
            <span>Cashier:</span>
            <span>{{ $sale->user->name ?? 'System' }}</span>
        </div>
        @if($sale->customer)
        <div class="info-row">
            <span>Customer:</span>
            <span>{{ $sale->customer->name }}</span>
        </div>
        @endif

        <div class="divider"></div>

        {{-- Items Table --}}
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="qty-col">Qty</th>
                    <th class="price-col">Price</th>
                    <th class="total-col">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ Str::limit($item->product->name, 15) }}</td>
                    <td class="qty-col">{{ $item->quantity }}</td>
                    <td class="price-col">${{ number_format($item->price, 2) }}</td>
                    <td class="total-col">${{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        {{-- Totals --}}
        <div class="totals-container">
            <div class="totals-row">
                <span>Subtotal:</span>
                <span>${{ number_format($sale->total, 2) }}</span>
            </div>
            @if($sale->discount_type !== 'none' && $sale->discount_value > 0)
            <div class="totals-row">
                <span>Discount @if($sale->discount_type === 'percentage')({{ floatval($sale->discount_value) }}%)@endif:</span>
                <span>-${{ number_format($sale->total - $sale->discountedTotal, 2) }}</span>
            </div>
            @endif
            <div class="totals-row">
                <span>Tax (0%):</span>
                <span>$0.00</span>
            </div>
            <div class="totals-row grand-total">
                <span>TOTAL:</span>
                <span>${{ number_format($sale->discountedTotal, 2) }}</span>
            </div>
            
            <div class="totals-row" style="margin-top: 10px;">
                <span>Payment ({{ ucfirst($sale->payment_status) }}):</span>
                <span>${{ number_format($sale->discountedTotal, 2) }}</span>
            </div>
            
            @if($sale->payment_method === 'cash' && $sale->amount_tendered !== null)
            <div class="totals-row">
                <span>Tendered:</span>
                <span>${{ number_format($sale->amount_tendered, 2) }}</span>
            </div>
            <div class="totals-row font-bold">
                <span>Change:</span>
                <span>${{ number_format(max(0, $sale->amount_tendered - $sale->discountedTotal), 2) }}</span>
            </div>
            @endif
        </div>

        <div class="divider"></div>

        {{-- Footer --}}
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>Please come again.</p>
            
            <div class="barcode">
                *{!! $sale->id !!}*
            </div>
        </div>

    </div>

    @if(request('print'))
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
    @endif
</body>
</html>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoiceNumber }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8px;
            line-height: 1.2;
            color: #333;
            margin: 0;
            padding: 10px;
            background: #fff;
        }
        
        .invoice-container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border: 1px solid #ccc;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header {
            background: #1f2a38;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .company-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }
        
        .company-info h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 2px 0;
            letter-spacing: 0.5px;
        }
        
        .company-info p {
            font-size: 9px;
            margin: 0;
            opacity: 0.9;
        }
        

        
        .content {
            padding: 15px 20px;
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .invoice-details {
            flex: 1;
        }
        
        .invoice-number {
            font-size: 14px;
            font-weight: bold;
            color: #1f2a38;
            margin-bottom: 3px;
        }
        
        .order-number {
            font-size: 9px;
            color: #666;
            margin-bottom: 2px;
        }
        
        .date {
            font-size: 9px;
            color: #666;
        }
        
        .status-section {
            text-align: right;
        }
        
        .status-item {
            margin-bottom: 5px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        
        .status-label {
            font-size: 7px;
            color: #666;
            margin-right: 8px;
            font-weight: bold;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-radius: 2px;
            line-height: 1;
            vertical-align: middle;
        }
        
        .status-paid { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #fff3cd; color: #856404; }
        .status-shipped { background: #d1ecf1; color: #0c5460; }
        .status-delivered { background: #d4edda; color: #155724; }
        
        .addresses {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .address-section {
            background: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        
        .address-title {
            font-weight: bold;
            color: #1f2a38;
            margin-bottom: 5px;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }
        
        .address-content {
            font-size: 7px;
            line-height: 1.3;
            color: #333;
        }
        
        .address-content strong {
            color: #1f2a38;
            font-weight: bold;
        }
        
        .payment-info {
            background: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            margin-bottom: 15px;
        }
        
        .payment-title {
            font-weight: bold;
            color: #1f2a38;
            margin-bottom: 5px;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }
        
        .payment-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px;
        }
        
        .payment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2px 0;
        }
        
        .payment-label {
            font-weight: bold;
            color: #333;
            font-size: 7px;
        }
        
        .payment-value {
            font-weight: bold;
            color: #1f2a38;
            font-size: 7px;
        }
        
        .fpl-section {
            background: #e3f2fd;
            padding: 10px;
            border: 1px solid #bbdefb;
            border-radius: 3px;
            margin-bottom: 15px;
        }
        
        .fpl-title {
            font-weight: bold;
            color: #1565c0;
            margin-bottom: 5px;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 1px solid #bbdefb;
            padding-bottom: 3px;
        }
        
        .items-section {
            margin-bottom: 15px;
        }
        
        .section-title {
            color: #1f2a38;
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 1px solid #1f2a38;
            padding-bottom: 3px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
            margin-bottom: 12px;
            font-size: 7px;
        }
        
        .items-table th {
            background: #1f2a38 !important;
            color: #fff !important;
            padding: 6px 4px;
            text-align: left;
            font-weight: bold;
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #1f2a38;
        }
        
        .items-table td {
            padding: 4px;
            border: 1px solid #ddd;
            font-size: 7px;
            vertical-align: middle;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .items-table .item-name { width: 35%; font-weight: bold; color: #1f2a38; }
        .items-table .item-variation { width: 25%; color: #666; font-style: italic; }
        .items-table .item-quantity { width: 10%; text-align: center; font-weight: bold; }
        .items-table .item-price { width: 15%; text-align: right; font-weight: bold; }
        .items-table .item-total { width: 15%; text-align: right; font-weight: bold; color: #1f2a38; }
        
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
        }
        
        .totals {
            background: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            width: 200px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            font-size: 8px;
        }
        
        .total-row.subtotal {
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
            margin-bottom: 4px;
            font-weight: bold;
        }
        
        .total-row.grand-total {
            font-size: 10px;
            font-weight: bold;
            color: #1f2a38;
            border-top: 1px solid #1f2a38;
            padding-top: 4px;
            margin-top: 4px;
        }
        
        .footer {
            background: #1f2a38;
            color: white;
            padding: 12px 20px;
            text-align: center;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .footer-section h4 {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 3px;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .footer-section p {
            font-size: 7px;
            opacity: 0.9;
            line-height: 1.2;
            margin: 0;
        }
        
        .footer-bottom {
            border-top: 1px solid #495057;
            padding-top: 8px;
            font-size: 7px;
            opacity: 0.8;
        }
        
        .footer-bottom p {
            margin: 1px 0;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="logo-section">
                <img src="{{ public_path('images/official-logo.png') }}" alt="MyGooners Logo" class="company-logo">
            </div>
        </div>

        <div class="content">
            <div class="invoice-header">
                <div class="invoice-details">
                    <div class="invoice-number">INVOICE #{{ $invoiceNumber }}</div>
                    <div class="order-number">Pesanan #{{ $order->order_number }}</div>
                    <div class="date">Tarikh: {{ $invoiceDate }} {{ now()->format('h:i A') }}</div>
                </div>
                <div class="status-section">
                    <div class="status-item">
                        <span class="status-label">Status Pembayaran:</span>
                        <span class="status-badge status-{{ $order->payment_status }}">{{ $order->getPaymentStatusDisplayName() }}</span>
                        <span class="status-label" style="margin-left: 20px;">Status Pesanan:</span>
                        <span class="status-badge status-{{ $order->status }}">{{ $order->getOrderStatusDisplayName() }}</span>
                    </div>
                </div>
            </div>

            <div class="addresses">
                <div class="address-section">
                    <div class="address-title">Bill Kepada</div>
                    <div class="address-content">
                        <strong>{{ $order->billing_name }}</strong><br>
                        {{ $order->billing_email }}<br>
                        {{ $order->billing_phone }}<br>
                        {{ $order->billing_address }}<br>
                        {{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_postal_code }}<br>
                        {{ $order->billing_country }}
                    </div>
                </div>
                
                <div class="address-section">
                    <div class="address-title">Hantar Kepada</div>
                    <div class="address-content">
                        <strong>{{ $order->shipping_name }}</strong><br>
                        {{ $order->shipping_email }}<br>
                        {{ $order->shipping_phone }}<br>
                        {{ $order->shipping_address }}<br>
                        {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                        {{ $order->shipping_country }}
                    </div>
                </div>
            </div>

            <div class="payment-info">
                <div class="payment-title">Maklumat Pembayaran</div>
                <div class="payment-grid">
                    <div class="payment-item">
                        <span class="payment-label">Kaedah Pembayaran:</span>
                        <span class="payment-value">{{ $order->getPaymentMethodDisplayName() }}</span>
                    </div>
                    <div class="payment-item">
                        <span class="payment-label">Tarikh Pesanan:</span>
                        <span class="payment-value">{{ $order->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            @if($order->fpl_manager_name && $order->fpl_team_name)
                <div class="fpl-section">
                    <div class="fpl-title">Fantasy Premier League</div>
                    <div class="payment-grid">
                        <div class="payment-item">
                            <span class="payment-label">Nama Manager:</span>
                            <span class="payment-value">{{ $order->fpl_manager_name }}</span>
                        </div>
                        <div class="payment-item">
                            <span class="payment-label">Nama Pasukan:</span>
                            <span class="payment-value">{{ $order->fpl_team_name }}</span>
                        </div>
                        <div class="payment-item">
                            <span class="payment-label">Kod Liga:</span>
                            <span class="payment-value">8nx2p4</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="items-section">
                <div class="section-title">Senarai Item</div>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th class="item-name">ITEM</th>
                            <th class="item-variation">VARIASI</th>
                            <th class="item-quantity">KUANTITI</th>
                            <th class="item-price">HARGA</th>
                            <th class="item-total">JUMLAH</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td class="item-name">{{ $item->product_name }}</td>
                                <td class="item-variation">{{ $item->variation_name ?: '-' }}</td>
                                <td class="item-quantity">{{ $item->quantity }}</td>
                                <td class="item-price">RM{{ number_format($item->price, 2) }}</td>
                                <td class="item-total">RM{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="totals-section">
                <div class="totals">
                    <div class="total-row subtotal">
                        <span>Jumlah Sebelum:</span>
                        <span>RM{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->shipping_cost > 0)
                        <div class="total-row">
                            <span>Kos Penghantaran:</span>
                            <span>RM{{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                    @endif
                    @if($order->tax > 0)
                        <div class="total-row">
                            <span>Cukai:</span>
                            <span>RM{{ number_format($order->tax, 2) }}</span>
                        </div>
                    @endif
                    <div class="total-row grand-total">
                        <span>Jumlah:</span>
                        <span>RM{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Hubungi Kami</h4>
                    <p>support@mygooners.my</p>
                </div>
                <div class="footer-section">
                    <h4>Lawati Kami</h4>
                    <p>www.mygooners.my<br>Ikuti kami di media sosial</p>
                </div>
                <div class="footer-section">
                    <h4>Sokongan</h4>
                    <p>Sokongan Pelanggan 24/7<br>Sembang langsung tersedia</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>Terima kasih kerana memilih MyGooners!</p>
                <p>&copy; {{ date('Y') }} MyGooners. Hak cipta terpelihara.</p>
            </div>
        </div>
    </div>
</body>
</html> 
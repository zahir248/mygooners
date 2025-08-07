<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoiceNumber }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        
        .page-container {
            background: white;
            max-width: 800px;
            margin: 0 auto 20px auto;
            border: 1px solid #000;
            border-radius: 0.5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .page-container:last-child {
            margin-bottom: 0;
        }
        
        .header {
            background: #1f2a38;
            color: white;
            padding: 30px;
            text-align: center;
            border-bottom: 2px solid #000;
            position: relative;
        }
        
        .company-logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
            letter-spacing: 2px;
        }
        
        .company-tagline {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .invoice-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #fff;
            color: #000;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid #000;
        }
        
        .content {
            padding: 30px;
        }
        
        .page-1 {
            page-break-after: always;
        }
        
        .page-2 {
            page-break-after: always;
        }
        
        .page-3 {
            page-break-after: avoid;
        }
        
        .invoice-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #000;
        }
        
        .invoice-number {
            font-size: 20px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }
        
        .order-number {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
        }
        
        .date {
            font-size: 14px;
            color: #333;
        }
        
        .addresses {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .address-section {
            flex: 1;
            background: #f9f9f9;
            padding: 20px;
            border: 1px solid #000;
        }
        
        .address-title {
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .address-content {
            font-size: 12px;
            line-height: 1.5;
            color: #000;
        }
        
        .address-content strong {
            color: #000;
            font-weight: bold;
        }
        
        .payment-section {
            background: #f9f9f9;
            padding: 20px;
            border: 1px solid #000;
            margin-bottom: 30px;
        }
        
        .payment-title {
            font-weight: bold;
            color: #000;
            margin-bottom: 15px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .payment-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .payment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
        }
        
        .payment-label {
            font-weight: bold;
            color: #000;
        }
        
        .payment-value {
            font-weight: bold;
            color: #000;
        }
        
        .status-badge {
            padding: 4px 8px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #000;
        }
        
        .status-paid {
            background: #28a745;
            color: #fff;
        }
        
        .status-pending {
            background: #ffc107;
            color: #000;
        }
        
        .status-processing {
            background: #ffc107;
            color: #000;
        }
        
        .status-shipped {
            background: #17a2b8;
            color: #fff;
        }
        
        .status-delivered {
            background: #28a745;
            color: #fff;
        }
        
        .items-section {
            margin-bottom: 30px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #1f2a38;
            margin-bottom: 20px;
        }
        
        .items-table th {
            background: #1f2a38 !important;
            color: #fff !important;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #1f2a38;
            white-space: nowrap;
            overflow: visible;
        }
        
        .items-table td {
            padding: 10px 12px;
            border: 1px solid #1f2a38;
            font-size: 12px;
            vertical-align: middle;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #fff;
        }
        
        .items-table tr:nth-child(odd) {
            background-color: #f8f9fa;
        }
        
        .items-table .item-name {
            width: 30%;
            font-weight: bold;
            color: #1f2a38;
        }
        
        .items-table .item-variation {
            width: 20%;
            color: #666;
            font-style: italic;
        }
        
        .items-table .item-quantity {
            width: 12%;
            text-align: center;
            font-weight: bold;
            color: #1f2a38;
        }
        
        .items-table .item-price {
            width: 18%;
            text-align: right;
            font-weight: bold;
            color: #1f2a38;
        }
        
        .items-table .item-total {
            width: 20%;
            text-align: right;
            font-weight: bold;
            color: #1f2a38;
        }
        
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        
        .totals {
            background: #f9f9f9;
            padding: 20px;
            border: 1px solid #000;
            width: 300px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 13px;
        }
        
        .total-row.subtotal {
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .total-row.grand-total {
            font-size: 16px;
            font-weight: bold;
            color: #000;
            border-top: 2px solid #000;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .footer {
            background: #1f2a38;
            color: white;
            padding: 20px;
            text-align: center;
            border-top: 2px solid #000;
        }
        
        .footer-content {
            margin-bottom: 15px;
        }
        
        .footer-section {
            margin-bottom: 10px;
        }
        
        .footer-section h4 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .footer-section p {
            font-size: 11px;
            opacity: 0.9;
            line-height: 1.4;
        }
        
        .footer-bottom {
            border-top: 1px solid #fff;
            padding-top: 15px;
            font-size: 10px;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <!-- PAGE 1: Header, Invoice Info, Addresses, Payment Info -->
    <div class="page-container page-1">
        <div class="header">
            <div class="invoice-badge">INVOICE</div>
            <div class="company-logo">MYGOONERS</div>
        </div>

        <div class="content">
            <div class="invoice-header">
                <div class="invoice-number">INVOICE #{{ $invoiceNumber }}</div>
                <div class="order-number">Pesanan #{{ $order->order_number }}</div>
                <div class="date">Tarikh: {{ $invoiceDate }} {{ now()->format('H:i') }}</div>
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

            <div class="payment-section">
                <div class="payment-title">Maklumat Pembayaran</div>
                <div class="payment-grid">
                    <div class="payment-item">
                        <span class="payment-label">Kaedah Pembayaran:</span>
                        <span class="payment-value">{{ $order->getPaymentMethodDisplayName() }}</span>
                    </div>
                    <div class="payment-item">
                        <span class="payment-label">Status Pembayaran:</span>
                        <span class="status-badge status-{{ $order->payment_status }}">{{ $order->getPaymentStatusDisplayName() }}</span>
                    </div>
                    <div class="payment-item">
                        <span class="payment-label">Status Pesanan:</span>
                        <span class="status-badge status-{{ $order->status }}">{{ $order->getOrderStatusDisplayName() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PAGE 2: Items List and Totals -->
    <div class="page-container page-2">
        <div class="content">
            <div class="items-section">
                <h3 style="color: #1f2a38; font-size: 16px; font-weight: bold; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 1px;">Senarai Item</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th class="item-name">ITEM</th>
                            <th class="item-variation">VARIASI</th>
                            <th class="item-quantity">QTY</th>
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
    </div>

    <!-- PAGE 3: Footer -->
    <div class="page-container page-3">
        <div class="footer">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Hubungi Kami</h4>
                    <p>support@mygooners.com<br>+60 12-345 6789</p>
                </div>
                <div class="footer-section">
                    <h4>Lawati Kami</h4>
                    <p>www.mygooners.com<br>Ikuti kami di media sosial</p>
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
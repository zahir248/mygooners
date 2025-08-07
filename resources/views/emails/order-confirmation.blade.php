<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengesahan Pesanan #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #1f2937;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 0 0 8px 8px;
        }
        .order-details {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #3b82f6;
        }
        .order-number {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-processing {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .item {
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 0;
        }
        .item:last-child {
            border-bottom: none;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
        }
        .address-section {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .address-title {
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MyGooners</h1>
        <p>Pengesahan Pesanan</p>
    </div>

    <div class="content">
        <p>Hai {{ $order->shipping_name }},</p>
        
        <p>Terima kasih atas pesanan anda! Kami telah menerima pembayaran anda dan sedang memproses pesanan anda.</p>

        <div class="order-details">
            <div class="order-number">Pesanan #{{ $order->order_number }}</div>
            <div>
                <span class="status-badge status-{{ $order->payment_status }}">
                    {{ $order->payment_status === 'paid' ? 'Telah Dibayar' : ucfirst($order->payment_status) }}
                </span>
                <span class="status-badge status-{{ $order->status }}">
                    {{ $order->status === 'processing' ? 'Sedang Diproses' : ucfirst($order->status) }}
                </span>
            </div>
            
            <p><strong>Tarikh Pesanan:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Kaedah Pembayaran:</strong> {{ $order->getPaymentMethodDisplayName() }}</p>
        </div>

        <h3>Item Pesanan:</h3>
        @foreach($order->items as $item)
            <div class="item">
                <div style="display: flex; justify-content: space-between;">
                    <div>
                        <strong>{{ $item->product_name }}</strong>
                        @if($item->variation_name)
                            <br><small>{{ $item->variation_name }}</small>
                        @endif
                        <br><small>Kuantiti: {{ $item->quantity }}</small>
                    </div>
                    <div style="text-align: right;">
                        <strong>{{ $item->getFormattedSubtotal() }}</strong>
                        <br><small>{{ $item->getFormattedPrice() }} setiap unit</small>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="total">
            <div>Subtotal: RM{{ number_format($order->subtotal, 2) }}</div>
            @if($order->shipping_cost > 0)
                <div>Kos Penghantaran: RM{{ number_format($order->shipping_cost, 2) }}</div>
            @endif
            @if($order->tax > 0)
                <div>Cukai: RM{{ number_format($order->tax, 2) }}</div>
            @endif
            <div style="font-size: 20px; color: #dc2626;">Jumlah: {{ $order->getFormattedTotal() }}</div>
        </div>

        <div class="address-section">
            <div class="address-title">Alamat Penghantaran:</div>
            <div>
                {{ $order->shipping_name }}<br>
                {{ $order->shipping_email }}<br>
                {{ $order->shipping_phone }}<br>
                {{ $order->shipping_address }}<br>
                {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                {{ $order->shipping_country }}
            </div>
        </div>

        @if($order->billing_email !== $order->shipping_email)
            <div class="address-section">
                <div class="address-title">Alamat Bil:</div>
                <div>
                    {{ $order->billing_name }}<br>
                    {{ $order->billing_email }}<br>
                    {{ $order->billing_phone }}<br>
                    {{ $order->billing_address }}<br>
                    {{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_postal_code }}<br>
                    {{ $order->billing_country }}
                </div>
            </div>
        @endif

        @if($order->fpl_manager_name && $order->fpl_team_name)
            <div class="address-section">
                <div class="address-title">Fantasy Premier League:</div>
                <div>
                    <strong>Nama Manager:</strong> {{ $order->fpl_manager_name }}<br>
                    <strong>Nama Pasukan:</strong> {{ $order->fpl_team_name }}<br>
                    <strong>Kod Liga:</strong> k7l1d7
                </div>
            </div>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('checkout.show', $order->id) }}" class="button">Lihat Pesanan</a>
        </div>

        <div style="background-color: #fef3c7; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h4 style="margin-top: 0; color: #92400e;">Langkah Seterusnya:</h4>
            <ul style="margin: 0; padding-left: 20px;">
                <li>Tim kami akan memproses pesanan anda dalam masa 1-2 hari bekerja</li>
                <li>Anda akan menerima emel dengan nombor pengesanan apabila pesanan dihantar</li>
                <li>Anda boleh menjejaki status pesanan anda melalui pautan di atas</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <p>Jika anda mempunyai sebarang pertanyaan, sila hubungi kami di support@mygooners.com</p>
        <p>&copy; {{ date('Y') }} MyGooners. Hak cipta terpelihara.</p>
    </div>
</body>
</html> 
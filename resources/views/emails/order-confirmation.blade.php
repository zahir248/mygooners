<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trans('email.order_confirmation_title', ['number' => $order->order_number]) }}</title>
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
        <p>{{ trans('email.order_confirmation_heading') }}</p>
    </div>

    <div class="content">
        <p>{{ trans('email.greeting', ['name' => $order->shipping_name]) }}</p>
        
        <p>{{ trans('email.order_confirmation_intro') }}</p>

        <div class="order-details">
            <div class="order-number">{{ trans('email.order_number', ['number' => $order->order_number]) }}</div>
            <div>
                <span class="status-badge status-{{ $order->payment_status }}">
                    {{ $order->payment_status === 'paid' ? __('Telah Dibayar') : ucfirst($order->payment_status) }}
                </span>
                <span class="status-badge status-{{ $order->status }}">
                    {{ $order->status === 'processing' ? __('Sedang Diproses') : ucfirst($order->status) }}
                </span>
            </div>
            
            <p><strong>{{ trans('email.order_date') }}</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>{{ trans('email.payment_method') }}</strong> {{ $order->getPaymentMethodDisplayName() }}</p>
        </div>

        <h3>{{ trans('email.order_items') }}</h3>
        @foreach($order->items as $item)
            <div class="item">
                <div style="display: flex; justify-content: space-between;">
                    <div>
                        <strong>{{ $item->product_name }}</strong>
                        @if($item->variation_name)
                            <br><small>{{ $item->variation_name }}</small>
                        @endif
                        <br><small>{{ trans('email.quantity', ['qty' => $item->quantity]) }}</small>
                    </div>
                    <div style="text-align: right;">
                        <strong>{{ $item->getFormattedSubtotal() }}</strong>
                        <br><small>{{ trans('email.per_unit', ['price' => $item->getFormattedPrice()]) }}</small>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="total">
            <div>{{ trans('email.subtotal') }} RM{{ number_format($order->subtotal, 2) }}</div>
            @if($order->shipping_cost > 0)
                <div>{{ trans('email.shipping_cost') }} RM{{ number_format($order->shipping_cost, 2) }}</div>
            @endif
            @if($order->tax > 0)
                <div>{{ trans('email.tax') }} RM{{ number_format($order->tax, 2) }}</div>
            @endif
            <div style="font-size: 20px; color: #dc2626;">{{ trans('email.total') }} {{ $order->getFormattedTotal() }}</div>
        </div>

        <div class="address-section">
            <div class="address-title">{{ trans('email.shipping_address') }}</div>
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
                <div class="address-title">{{ trans('email.billing_address') }}</div>
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

        @if(fpl_module_enabled() && $order->fpl_manager_name && $order->fpl_team_name)
            <div class="address-section">
                <div class="address-title">{{ trans('email.fpl_section') }}</div>
                <div>
                    <strong>{{ trans('email.manager_name') }}</strong> {{ $order->fpl_manager_name }}<br>
                    <strong>{{ trans('email.team_name') }}</strong> {{ $order->fpl_team_name }}<br>
                    <strong>{{ trans('email.league_code') }}</strong> 8nx2p4
                </div>
            </div>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('checkout.show', $order->id) }}" class="button">{{ trans('email.view_order') }}</a>
        </div>

        <div style="background-color: #fef3c7; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h4 style="margin-top: 0; color: #92400e;">{{ trans('email.next_steps') }}</h4>
            <ul style="margin: 0; padding-left: 20px;">
                <li>{{ trans('email.next_step_process') }}</li>
                <li>{{ trans('email.next_step_tracking') }}</li>
                <li>{{ trans('email.next_step_track_online') }}</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <p>{{ trans('email.footer_questions') }}</p>
        <p>&copy; {{ date('Y') }} MyGooners. {{ trans('email.footer_copyright') }}</p>
    </div>
</body>
</html>

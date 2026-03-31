<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 640px;
            margin: 0 auto;
        }
        .container {
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        .order-reference {
            background: #f5f5f5;
            padding: 14px;
            border-left: 4px solid #0284c7;
            margin: 18px 0;
            font-weight: 700;
        }
        .changes-list {
            margin: 0;
            padding-left: 20px;
        }
        .changes-list li {
            margin-bottom: 8px;
        }
        .section-title {
            margin-top: 22px;
            margin-bottom: 10px;
            font-size: 18px;
            color: #111;
        }
        .order-details {
            margin: 14px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            color: #666;
            font-weight: 600;
            min-width: 120px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
        }
        .items-table th {
            background: #f5f5f5;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #0284c7;
            font-size: 13px;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
            font-size: 13px;
        }
        .total-row {
            text-align: right;
            color: #0284c7;
            font-weight: 700;
            margin-top: 8px;
        }
        .footer {
            background: #f9f9f9;
            border-top: 1px solid #ddd;
            color: #666;
            text-align: center;
            font-size: 12px;
            padding: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('messages.email.order_updated.header') }}</h1>
        </div>

        <div class="content">
            <p>{{ __('messages.email.order_updated.greeting', ['name' => ($address['fullName'] ?? __('messages.email.order_confirmation.customer_fallback'))]) }}</p>
            <p>{{ __('messages.email.order_updated.intro') }}</p>

            <div class="order-reference">
                {{ __('messages.email.order_confirmation.order_number') }}: {{ $order->reference }}
            </div>

            <h2 class="section-title">{{ __('messages.email.order_updated.changes_title') }}</h2>
            @if(!empty($changeLines))
                <ul class="changes-list">
                    @foreach($changeLines as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            @else
                <p>{{ __('messages.email.order_updated.no_changes') }}</p>
            @endif

            <h2 class="section-title">{{ __('messages.email.order_updated.current_state_title') }}</h2>
            <div class="order-details">
                <div class="detail-row">
                    <span class="detail-label">{{ __('messages.email.order_confirmation.status') }}:</span>
                    <span>{{ $statusLabel }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ __('messages.email.order_confirmation.delivery_method') }}:</span>
                    <span>{{ $deliveryMethodLabel }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ __('messages.email.order_confirmation.payment_method') }}:</span>
                    <span>{{ $paymentMethodLabel }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ __('messages.email.order_confirmation.address') }}:</span>
                    <span>
                        {{ $address['street'] ?? '' }}<br>
                        {{ $address['zip'] ?? '' }} {{ $address['city'] ?? '' }}<br>
                        {{ $address['country'] ?? '' }}
                    </span>
                </div>
            </div>

            <h2 class="section-title">{{ __('messages.email.order_confirmation.items_title') }}</h2>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.email.order_confirmation.table_product') }}</th>
                        <th>{{ __('messages.email.order_confirmation.table_options') }}</th>
                        <th>{{ __('messages.email.order_confirmation.table_quantity') }}</th>
                        <th>{{ __('messages.email.order_confirmation.table_price') }}</th>
                        <th>{{ __('messages.email.order_confirmation.table_total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        @php
                            $productId = data_get($item, 'product_id');
                            $productTitle = data_get($item, 'product_title');
                            $quantity = (int) data_get($item, 'quantity', 0);
                            $price = (float) data_get($item, 'price', 0);
                            $rawVariantOptions = data_get($item, 'variant_options');
                            $variantOptions = is_string($rawVariantOptions)
                                ? json_decode($rawVariantOptions, true)
                                : $rawVariantOptions;
                        @endphp
                        <tr>
                            <td>{{ $productTitle ?? __('messages.email.order_updated.product_fallback', ['id' => $productId]) }}</td>
                            <td>
                                @if(is_array($variantOptions) && count($variantOptions) > 0)
                                    {{ implode(', ', array_map(fn($k, $v) => ucfirst($k) . ': ' . $v, array_keys($variantOptions), $variantOptions)) }}
                                @else
                                    {{ __('messages.email.order_confirmation.no_options') }}
                                @endif
                            </td>
                            <td>{{ $quantity }}</td>
                            <td>{{ number_format($price, 2) }} €</td>
                            <td>{{ number_format($price * $quantity, 2) }} €</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p class="total-row">{{ __('messages.email.order_confirmation.total_amount') }}: {{ number_format($order->total, 2) }} €</p>

            <p>{{ __('messages.email.order_updated.footer_notice') }}</p>
        </div>

        <div class="footer">
            <p>{{ __('messages.email.common.rights_reserved', ['year' => date('Y'), 'app' => config('app.name')]) }}</p>
            <p>{{ __('messages.email.common.automated_notice') }}</p>
        </div>
    </div>
</body>
</html>

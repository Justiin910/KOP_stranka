<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .container {
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        .content {
            padding: 20px;
        }
        .order-reference {
            background: #f5f5f5;
            padding: 15px;
            border-left: 4px solid #667eea;
            margin: 20px 0;
            font-weight: bold;
        }
        .order-details {
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #666;
            font-weight: 600;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th {
            background: #f5f5f5;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #667eea;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .total-row {
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
            text-align: right;
            padding: 15px 0;
            border-top: 2px solid #667eea;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background: #f9f9f9;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #ddd;
        }
        .status-badge {
            display: inline-block;
            background: #ffc107;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('messages.email.order_confirmation.header') }} ✓</h1>
        </div>

        <div class="content">
            <p>{{ __('messages.email.order_confirmation.greeting', ['name' => ($address['fullName'] ?? __('messages.email.order_confirmation.customer_fallback'))]) }}</p>

            <p>{{ __('messages.email.order_confirmation.intro') }}</p>

            <div class="order-reference">
                {{ __('messages.email.order_confirmation.order_number') }}: {{ $order->reference }}
            </div>

            <div class="order-details">
                <div class="detail-row">
                    <span class="detail-label">{{ __('messages.email.order_confirmation.order_date') }}:</span>
                    <span>{{ $order->created_at->format('d.m.Y H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ __('messages.email.order_confirmation.status') }}:</span>
                    @php
                        $statusLabels = trans('messages.email.order_confirmation.statuses');
                        $statusLabel = is_array($statusLabels)
                            ? ($statusLabels[$order->status] ?? ucfirst($order->status))
                            : ucfirst($order->status);
                    @endphp
                    <span><span class="status-badge">{{ $statusLabel }}</span></span>
                </div>
            </div>

            <h2>{{ __('messages.email.order_confirmation.items_title') }}</h2>
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
                    <tr>
                        <td>{{ $item['product_title'] ?? __('messages.email.order_confirmation.product_fallback', ['id' => $item['product_id']]) }}</td>
                        <td>
                            @if(!empty($item['variant_options']))
                                @php
                                    $variantOptions = is_string($item['variant_options']) 
                                        ? json_decode($item['variant_options'], true) 
                                        : $item['variant_options'];
                                    if ($variantOptions && is_array($variantOptions)) {
                                        echo implode(', ', array_map(fn($k, $v) => ucfirst($k) . ': ' . $v, array_keys($variantOptions), $variantOptions));
                                    }
                                @endphp
                            @else
                                {{ __('messages.email.order_confirmation.no_options') }}
                            @endif
                        </td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>{{ number_format($item['price'], 2) }} €</td>
                        <td>{{ number_format($item['price'] * $item['quantity'], 2) }} €</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total-row">
                {{ __('messages.email.order_confirmation.total_amount') }}: {{ number_format($order->total, 2) }} €
            </div>

            <h2>{{ __('messages.email.order_confirmation.delivery_title') }}</h2>
            <div class="order-details">
                <div class="detail-row">
                    <span class="detail-label">{{ __('messages.email.order_confirmation.delivery_method') }}:</span>
                    @php
                        $deliveryMethods = trans('messages.email.order_confirmation.delivery_methods');
                        $deliveryMethodLabel = is_array($deliveryMethods)
                            ? ($deliveryMethods[$order->delivery_method] ?? ucfirst($order->delivery_method))
                            : ucfirst($order->delivery_method);
                    @endphp
                    <span>{{ $deliveryMethodLabel }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ __('messages.email.order_confirmation.address') }}:</span>
                    <span>
                        {{ $address['street'] ?? '' }}<br>
                        {{ $address['zip'] ?? '' }} {{ $address['city'] ?? '' }}<br>
                        {{ $address['country'] ?? '' }}
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ __('messages.email.order_confirmation.phone') }}:</span>
                    @php
                        $formattedPhone = '';
                        if (!empty($phone)) {
                            $digits = preg_replace('/\D/', '', $phone);
                            if (strpos($digits, '421') === 0) {
                                $local = substr($digits, 3);
                            } elseif (strpos($digits, '0') === 0) {
                                $local = substr($digits, 1);
                            } else {
                                $local = $digits;
                            }
                            // keep last 9 digits of local number if longer
                            if (strlen($local) > 9) {
                                $local = substr($local, -9);
                            }
                            $parts = str_split($local, 3);
                            if (!empty($parts)) {
                                $formattedPhone = '+421 ' . implode(' ', $parts);
                            }
                        }
                    @endphp
                    <span>{{ $formattedPhone }}</span>
                </div>
            </div>

            <h2>{{ __('messages.email.order_confirmation.payment_title') }}</h2>
            <div class="order-details">
                <div class="detail-row">
                    <span class="detail-label">{{ __('messages.email.order_confirmation.payment_method') }}:</span>
                    <span>{{ $paymentMethod['label'] ?? '' }}</span>
                </div>
            </div>

            <p>{{ __('messages.email.order_confirmation.thanks') }}</p>

           <!-- <a href="#" class="button">Sledovať objednávku</a> -->

            <p style="color: #999; font-size: 12px; margin-top: 30px;">{{ __('messages.email.order_confirmation.ignore_notice') }}</p>
        </div>

        <div class="footer">
            <p>{{ __('messages.email.order_confirmation.footer_rights', ['year' => date('Y')]) }}</p>
            <p>{{ __('messages.email.order_confirmation.footer_contact', ['email' => 'techstore99x@gmail.com']) }}</p>
        </div>
    </div>
</body>
</html>

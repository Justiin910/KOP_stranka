<!DOCTYPE html>
<html>
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
            <h1>Objednávka potvrdená! ✓</h1>
        </div>

        <div class="content">
            <p>Dobrý deň {{ $address['fullName'] ?? 'Zákazníče' }},</p>

            <p>Vaša objednávka bola úspešne vytvorená a je v procese spracovania. Nájdete nižšie detaily vašej objednávky:</p>

            <div class="order-reference">
                Číslo objednávky: {{ $order->reference }}
            </div>

            <div class="order-details">
                <div class="detail-row">
                    <span class="detail-label">Dátum objednávky:</span>
                    <span>{{ $order->created_at->format('d.m.Y H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Stav:</span>
                    <span><span class="status-badge">{{ ucfirst($order->status) }}</span></span>
                </div>
            </div>

            <h2>Položky v objednávke:</h2>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Produkt</th>
                        <th>Množstvo</th>
                        <th>Cena</th>
                        <th>Spolu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item['product_title'] ?? 'Produkt #' . $item['product_id'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>{{ number_format($item['price'], 2) }} €</td>
                        <td>{{ number_format($item['price'] * $item['quantity'], 2) }} €</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total-row">
                Celková suma: {{ number_format($order->total, 2) }} €
            </div>

            <h2>Doručenie:</h2>
            <div class="order-details">
                <div class="detail-row">
                    <span class="detail-label">Spôsob:</span>
                    <span>{{ ucfirst($order->delivery_method) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Adresa:</span>
                    <span>
                        {{ $address['street'] ?? '' }}<br>
                        {{ $address['zip'] ?? '' }} {{ $address['city'] ?? '' }}<br>
                        {{ $address['country'] ?? '' }}
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Telefón:</span>
                    <span>{{ $phone }}</span>
                </div>
            </div>

            <h2>Spôsob platby:</h2>
            <div class="order-details">
                <div class="detail-row">
                    <span class="detail-label">Metóda:</span>
                    <span>{{ $paymentMethod }}</span>
                </div>
            </div>

            <p>Ďakujeme za vašu objednávku! Budeme vás informovať o jej stave prostredníctvom e-mailu.</p>

            <a href="#" class="button">Sledovať objednávku</a>

            <p style="color: #999; font-size: 12px; margin-top: 30px;">Ak ste si túto objednávku neobjednali, prosím ignorujte tento e-mail.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} TechShop s.r.o. Všetky práva vyhradené.</p>
            <p>Kontakt: techstore99x@gmail.com</p>
        </div>
    </div>
</body>
</html>

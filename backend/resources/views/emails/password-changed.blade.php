<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .alert-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #856404;
        }
        .alert-box strong {
            display: block;
            margin-bottom: 5px;
            font-size: 16px;
        }
        .info-section {
            background-color: #f8f9fa;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            border-left: 4px solid #667eea;
        }
        .info-section p {
            margin: 8px 0;
            font-size: 14px;
            color: #555;
        }
        .info-section strong {
            color: #333;
        }
        .action-text {
            font-size: 15px;
            margin: 20px 0;
            color: #555;
        }
        .security-note {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #721c24;
            font-size: 14px;
        }
        .security-note strong {
            display: block;
            margin-bottom: 8px;
            font-size: 15px;
        }
        .support-section {
            background-color: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #0c5460;
            font-size: 14px;
        }
        .support-section strong {
            display: block;
            margin-bottom: 8px;
            font-size: 15px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            font-size: 12px;
            color: #6c757d;
        }
        .divider {
            height: 1px;
            background-color: #e9ecef;
            margin: 20px 0;
        }
        a {
            color: #667eea;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Heslo zmenené</h1>
        </div>
        
        <div class="content">
            <p class="greeting">Dobrý deň {{ $user->name }},</p>
            
            <p>Potvrdzujeme, že vaše heslo k účtu {{ $user->email }} bolo úspešne zmenené.</p>
            
            <div class="info-section">
                <p><strong>Čas zmeny:</strong> {{ $changeTime ?? 'dnes' }}</p>
            </div>
            
            <div class="alert-box">
                <strong>⚠️ Pozor!</strong>
                Ak ste túto zmenu neuskutočnili, je možné, že váš účet bol kompromitovaný. V takomto prípade prosím<br>
                <a href="mailto:{{ config('mail.from.address') }}">kontaktujte nás ihneď</a>.
            </div>
            
            <div class="support-section">
                <strong>💡 Bezpečnostný tip</strong>
                Ak ste zmenu nevykonali vy, odporúčame vám:
                <ul style="margin: 8px 0 0 20px; padding: 0;">
                    <li>Zmeniť heslo spätne</li>
                    <li>Skontrolovať bezpečnosť svojho emailu</li>
                    <li>Aktivovať dvojfaktorovú autentifikáciu ak je dostupná</li>
                </ul>
            </div>
            
            <div class="divider"></div>
            
            <p style="font-size: 14px; color: #666;">Ak máte ďalšie otázky alebo potrebujete pomoc, neváhajte nás kontaktovať na adrese <a href="mailto:techstore99x@gmail.com">techstore99x@gmail.com</a>.</p>
            
            <p style="font-size: 14px; color: #666;">S pozdravom,<br><strong>{{ config('mail.from.name') }}</strong></p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Všetky práva vyhradené.</p>
            <p>Toto je automatizovaná správa. Prosím, neodpovedajte na túto správu.</p>
        </div>
    </div>
</body>
</html>

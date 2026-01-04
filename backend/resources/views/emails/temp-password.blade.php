<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 5px; }
        .content { background: #f9f9f9; padding: 20px; border-radius: 5px; margin-top: 20px; }
        .password-box { background: white; border: 2px solid #667eea; padding: 15px; border-radius: 5px; text-align: center; margin: 20px 0; }
        .password-box code { font-size: 18px; font-weight: bold; color: #667eea; font-family: 'Courier New', monospace; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vaše dočasné heslo</h1>
        </div>
        
        <div class="content">
            <p>Dobrý deň <strong>{{ $userName }}</strong>,</p>
            
            <p>Administrátor vám vygeneroval nové dočasné heslo na prístup do vášho konta.</p>
            
            <div class="password-box">
                <p style="margin: 0 0 10px 0; color: #666; font-size: 12px;">Dočasné heslo:</p>
                <code>{{ $tempPassword }}</code>
            </div>
            
            <h3>Ako sa prihlásiť:</h3>
            <ol>
                <li>Prejdite na prihlásovaciu stránku</li>
                <li>Zadajte vašu emailovú adresu</li>
                <li>Zadajte toto dočasné heslo</li>
                <li>Po prihlásení si prosím zmeniť heslo na nové silné heslo</li>
            </ol>
            
            <p style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 10px; border-radius: 3px;">
                <strong>⚠️ Bezpečnosť:</strong> Toto heslo je dočasné. Zmeniť si ho môžete v nastaveniach svojho profilu.
            </p>
        </div>
        
        <div class="footer">
            <p>Ak ste si nepožiadali toto heslo alebo máte otázky, kontaktujte nás.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Všetky práva vyhradené.</p>
        </div>
    </div>
</body>
</html>

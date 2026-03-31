<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
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
            <h1>🔐 {{ __('messages.email.password_changed.title') }}</h1>
        </div>
        
        <div class="content">
            <p class="greeting">{{ __('messages.email.password_changed.greeting', ['name' => $user->name]) }}</p>
            
            <p>{{ __('messages.email.password_changed.intro', ['email' => $user->email]) }}</p>
            
            <div class="info-section">
                <p><strong>{{ __('messages.email.password_changed.change_time_label') }}:</strong> {{ $changeTime ?? __('messages.email.password_changed.today') }}</p>
            </div>
            
            <div class="alert-box">
                <strong>⚠️ {{ __('messages.email.password_changed.alert_title') }}</strong>
                {{ __('messages.email.password_changed.alert_body') }}<br>
                <a href="mailto:{{ config('mail.from.address') }}">{{ __('messages.email.password_changed.contact_now') }}</a>.
            </div>
            
            <div class="support-section">
                <strong>💡 {{ __('messages.email.password_changed.security_tip_title') }}</strong>
                {{ __('messages.email.password_changed.security_tip_intro') }}
                <ul style="margin: 8px 0 0 20px; padding: 0;">
                    <li>{{ __('messages.email.password_changed.tip_reset_password') }}</li>
                    <li>{{ __('messages.email.password_changed.tip_secure_email') }}</li>
                    <li>{{ __('messages.email.password_changed.tip_enable_2fa') }}</li>
                </ul>
            </div>
            
            <div class="divider"></div>
            
            <p style="font-size: 14px; color: #666;">{{ __('messages.email.password_changed.support_contact', ['email' => 'techstore99x@gmail.com']) }}</p>
            
            <p style="font-size: 14px; color: #666;">{{ __('messages.email.common.regards') }}<br><strong>{{ config('mail.from.name') }}</strong></p>
        </div>
        
        <div class="footer">
            <p>{{ __('messages.email.common.rights_reserved', ['year' => date('Y'), 'app' => config('app.name')]) }}</p>
            <p>{{ __('messages.email.common.automated_notice') }}</p>
        </div>
    </div>
</body>
</html>

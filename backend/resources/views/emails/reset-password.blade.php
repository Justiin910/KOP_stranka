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
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
        }
        .button:hover {
            opacity: 0.9;
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
        .url-box {
            background-color: #f8f9fa;
            padding: 12px;
            border-radius: 4px;
            word-break: break-all;
            font-size: 12px;
            color: #666;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔑 {{ __('messages.email.reset_password.title') }}</h1>
        </div>
        
        <div class="content">
            <p class="greeting">{{ __('messages.email.reset_password.greeting') }}</p>
            
            <p>{{ __('messages.email.reset_password.intro') }}</p>
            
            <div class="button-container">
                <a href="{{ $resetUrl }}" class="button">{{ __('messages.email.reset_password.button') }}</a>
            </div>
            
            <p style="text-align: center; font-size: 14px; color: #666;">{{ __('messages.email.reset_password.copy_link') }}</p>
            <div class="url-box">{{ $resetUrl }}</div>
            
            <div class="info-section">
                <p><strong>{{ __('messages.email.reset_password.validity_label') }}:</strong> {{ __('messages.email.reset_password.validity', ['minutes' => config('auth.passwords.users.expire', 60)]) }}</p>
            </div>
            
            <div class="security-note">
                <strong>⚠️ {{ __('messages.email.reset_password.security_title') }}</strong>
                {{ __('messages.email.reset_password.security_body') }}
            </div>
            
            <div class="divider"></div>
            
            <p style="font-size: 14px; color: #666;">{{ __('messages.email.reset_password.support_contact', ['email' => 'techstore99x@gmail.com']) }}</p>
            
            <p style="font-size: 14px; color: #666;">{{ __('messages.email.common.regards') }}<br><strong>{{ config('mail.from.name') }}</strong></p>
        </div>
        
        <div class="footer">
            <p>{{ __('messages.email.common.rights_reserved', ['year' => date('Y'), 'app' => config('app.name')]) }}</p>
            <p>{{ __('messages.email.common.automated_notice') }}</p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
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
            <h1>{{ __('messages.email.temp_password.title') }}</h1>
        </div>
        
        <div class="content">
            <p>{{ __('messages.email.temp_password.greeting', ['name' => $userName]) }}</p>
            
            <p>{{ __('messages.email.temp_password.intro') }}</p>
            
            <div class="password-box">
                <p style="margin: 0 0 10px 0; color: #666; font-size: 12px;">{{ __('messages.email.temp_password.password_label') }}:</p>
                <code>{{ $tempPassword }}</code>
            </div>
            
            <h3>{{ __('messages.email.temp_password.how_to_login') }}:</h3>
            <ol>
                <li>{{ __('messages.email.temp_password.step_1') }}</li>
                <li>{{ __('messages.email.temp_password.step_2') }}</li>
                <li>{{ __('messages.email.temp_password.step_3') }}</li>
                <li>{{ __('messages.email.temp_password.step_4') }}</li>
            </ol>
            
            <p style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 10px; border-radius: 3px;">
                <strong>⚠️ {{ __('messages.email.temp_password.security_title') }}:</strong> {{ __('messages.email.temp_password.security_body') }}
            </p>
        </div>
        
        <div class="footer">
            <p>{{ __('messages.email.temp_password.footer_help') }}</p>
            <p>{{ __('messages.email.common.rights_reserved', ['year' => date('Y'), 'app' => config('app.name')]) }}</p>
        </div>
    </div>
</body>
</html>

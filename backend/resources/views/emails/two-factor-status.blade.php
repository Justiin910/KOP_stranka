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
        .status-pill {
            display: inline-block;
            margin: 10px 0 16px;
            padding: 6px 12px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 13px;
            color: #fff;
            background: {{ ($enabled ?? false) ? '#16a34a' : '#dc2626' }};
        }
        .meta {
            margin-top: 12px;
            background: #f8fafc;
            border-left: 4px solid #0284c7;
            padding: 10px 12px;
            border-radius: 4px;
            font-size: 14px;
            color: #444;
        }
        .notice {
            margin-top: 18px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px 12px;
            border-radius: 4px;
            color: #856404;
            font-size: 14px;
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
    @php
        $isEnabled = (bool) ($enabled ?? false);
    @endphp
    <div class="container">
        <div class="header">
            <h1>{{ $isEnabled ? __('messages.email.two_factor_status.header_enabled') : __('messages.email.two_factor_status.header_disabled') }}</h1>
        </div>

        <div class="content">
            <p>{{ __('messages.email.two_factor_status.greeting', ['name' => ($userName ?: __('messages.email.order_confirmation.customer_fallback'))]) }}</p>
            <p>{{ $isEnabled ? __('messages.email.two_factor_status.intro_enabled') : __('messages.email.two_factor_status.intro_disabled') }}</p>

            <span class="status-pill">
                {{ $isEnabled ? __('messages.auth.two_factor_enabled') : __('messages.auth.two_factor_disabled') }}
            </span>

            <div class="meta">
                {{ __('messages.email.two_factor_status.changed_at') }}: {{ $changedAt ?? now()->format('d.m.Y H:i') }}
            </div>

            <div class="notice">
                {{ __('messages.email.two_factor_status.security_notice') }}
            </div>
        </div>

        <div class="footer">
            <p>{{ __('messages.email.common.rights_reserved', ['year' => date('Y'), 'app' => config('app.name')]) }}</p>
            <p>{{ __('messages.email.common.automated_notice') }}</p>
        </div>
    </div>
</body>
</html>

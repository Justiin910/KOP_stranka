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
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        .meta {
            margin-top: 16px;
            background: #f8fafc;
            border-left: 4px solid #dc2626;
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
    <div class="container">
        <div class="header">
            <h1>{{ __('messages.email.account_deleted.header') }}</h1>
        </div>

        <div class="content">
            <p>{{ __('messages.email.account_deleted.greeting', ['name' => ($userName ?: __('messages.email.order_confirmation.customer_fallback'))]) }}</p>
            <p>{{ __('messages.email.account_deleted.intro') }}</p>

            <div class="meta">
                {{ __('messages.email.account_deleted.deleted_at') }}: {{ $deletedAt ?? now()->format('d.m.Y H:i') }}
            </div>

            <div class="notice">
                {{ __('messages.email.account_deleted.security_notice') }}
            </div>
        </div>

        <div class="footer">
            <p>{{ __('messages.email.common.rights_reserved', ['year' => date('Y'), 'app' => config('app.name')]) }}</p>
            <p>{{ __('messages.email.common.automated_notice') }}</p>
        </div>
    </div>
</body>
</html>

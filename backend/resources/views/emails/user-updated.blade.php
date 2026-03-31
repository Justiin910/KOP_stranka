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
        .changes-list {
            margin: 12px 0;
            padding-left: 20px;
        }
        .changes-list li {
            margin-bottom: 8px;
        }
        .meta {
            margin-top: 16px;
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
    <div class="container">
        <div class="header">
            <h1>{{ __('messages.email.user_updated.header') }}</h1>
        </div>

        <div class="content">
            <p>{{ __('messages.email.user_updated.greeting', ['name' => ($userName ?: __('messages.email.order_confirmation.customer_fallback'))]) }}</p>
            <p>{{ __('messages.email.user_updated.intro') }}</p>

            <h3>{{ __('messages.email.user_updated.changes_title') }}</h3>
            @if(!empty($changeLines))
                <ul class="changes-list">
                    @foreach($changeLines as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            @else
                <p>{{ __('messages.email.user_updated.no_changes') }}</p>
            @endif

            <div class="meta">
                {{ __('messages.email.user_updated.changed_at') }}: {{ $changedAt ?? now()->format('d.m.Y H:i') }}
            </div>

            <div class="notice">
                {{ __('messages.email.user_updated.security_notice') }}
            </div>
        </div>

        <div class="footer">
            <p>{{ __('messages.email.common.rights_reserved', ['year' => date('Y'), 'app' => config('app.name')]) }}</p>
            <p>{{ __('messages.email.common.automated_notice') }}</p>
        </div>
    </div>
</body>
</html>

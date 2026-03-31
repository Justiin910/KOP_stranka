<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.auth.two_factor_email_subject') }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f4f6f8;
            font-family: Arial, sans-serif;
            color: #1f2937;
        }

        .container {
            max-width: 620px;
            margin: 32px auto;
            padding: 0 16px;
        }

        .card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            box-shadow: 0 6px 24px rgba(15, 23, 42, 0.06);
        }

        .header {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: #ffffff;
            padding: 22px 24px;
            font-size: 20px;
            font-weight: 700;
            text-align: center;
        }

        .content {
            padding: 26px 24px 20px;
            line-height: 1.55;
            font-size: 15px;
        }

        .code-wrap {
            margin: 18px 0 12px;
            text-align: center;
        }

        .code {
            display: inline-block;
            letter-spacing: 8px;
            font-size: 34px;
            font-weight: 700;
            color: #0f172a;
            background: #f8fafc;
            border: 1px dashed #93c5fd;
            border-radius: 10px;
            padding: 12px 18px;
        }

        .note {
            margin-top: 16px;
            font-size: 13px;
            color: #64748b;
        }

        .footer {
            border-top: 1px solid #e5e7eb;
            padding: 14px 24px 20px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }

        @media (max-width: 480px) {
            .code {
                letter-spacing: 5px;
                font-size: 28px;
                padding: 10px 14px;
            }
        }
    </style>
</head>
<body>
    @php
        $isEmailChange = ($purpose ?? 'login') === 'email_change';
    @endphp
    <div class="container">
        <div class="card">
            <div class="header">
                {{ $isEmailChange ? __('messages.auth.two_factor_email_title_email_change') : __('messages.auth.two_factor_email_title_login') }}
            </div>
            <div class="content">
                <p>{{ __('messages.auth.two_factor_email_greeting') }}</p>
                @if($isEmailChange)
                    <p>{{ __('messages.auth.two_factor_email_intro_email_change') }}</p>
                @else
                    <p>{{ __('messages.auth.two_factor_email_intro_login') }}</p>
                @endif

                <div class="code-wrap">
                    <div class="code">{{ $code }}</div>
                </div>

                <p>{{ __('messages.auth.two_factor_email_valid_for', ['minutes' => ($expiresInMinutes ?? 10)]) }}</p>
                <p class="note">
                    {{ __('messages.auth.two_factor_email_note') }}
                </p>
            </div>
            <div class="footer">
                {{ __('messages.auth.two_factor_email_footer') }}
            </div>
        </div>
    </div>
</body>
</html>

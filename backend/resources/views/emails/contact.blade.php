<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 30px;
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
        .field {
            margin-bottom: 20px;
        }
        .field-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #667eea;
        }
        .field-value {
            color: #555;
            font-size: 16px;
            padding: 10px 12px;
            background-color: #f9f9f9;
            border-left: 3px solid #667eea;
            border-radius: 3px;
        }
        .message-section {
            background-color: #f0f4ff;
            border-radius: 6px;
            padding: 20px;
            margin-top: 20px;
            border-left: 4px solid #667eea;
        }
        .message-label {
            font-weight: 600;
            color: #667eea;
            margin-bottom: 12px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .message-content {
            color: #333;
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #eee;
            font-size: 13px;
            color: #999;
        }
        .reply-section {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
            font-size: 14px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📨 Nová správa z kontaktného formulára</h1>
        </div>
        
        <div class="content">
            <div class="field">
                <div class="field-label">Meno</div>
                <div class="field-value">{{ $name }}</div>
            </div>

            <div class="field">
                <div class="field-label">Email na odpoveď</div>
                <div class="field-value">{{ $email }}</div>
            </div>

            <div class="field">
                <div class="field-label">Predmet</div>
                <div class="field-value">{{ $subject ?? 'Bez predmetu' }}</div>
            </div>

            <div class="message-section">
                <div class="message-label">💬 Správa</div>
                <div class="message-content">{{ $message }}</div>
            </div>

            <div class="reply-section">
                <strong>💡 Tip:</strong> Na túto správu odpovedajte priamo na tejto emailovej konverzácii - odoslateľ bude vidieť vašu odpoveď na adrese {{ $email }}
            </div>
        </div>

        <div class="footer">
            <p>Táto správa bola odoslaná z kontaktného formulára na vašej webstránke.</p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('maintenance.title') }}</title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <style>
        html, body {
            height: 100%;
        }
        body {
            min-height: 100vh;
            height: 100%;
            margin: 0;
            padding: 0;
            background: #f7f9fa;
            font-family: 'Montserrat', Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .center-wrapper {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .logo {
            display: block;
            margin: 0 auto 0 auto;
            max-width: 140px;
        }
        .org-title {
            text-align: center;
            margin-top: 16px;
            font-size: 2.2rem;
            font-weight: 700;
            color: #ff0000;
            letter-spacing: 2px;
        }
        .org-subtitle, .org-en {
            text-align: center;
            font-size: 1.05rem;
            color: #222;
            margin-bottom: 6px;
        }
        .org-en { margin-bottom: 28px; }
        .error-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(60,72,88,0.10);
            max-width: 600px;
            margin: 32px auto 0 auto;
            padding: 36px 28px 28px 28px;
            text-align: center;
        }
        .error-title {
            font-size: 1.7rem;
            font-weight: 700;
            color: #000;
            margin-bottom: 10px;
        }
        .error-message {
            font-size: 1.08rem;
            color: #475569;
            margin-bottom: 12px;
        }
        .maintenance-icon {
            font-size: 3rem;
            margin-bottom: 16px;
        }
        @media (max-width: 600px) {
            .error-card { padding: 18px 16px; }
            .org-title { font-size: 1.3rem; }
        }
    </style>
</head>
<body>
    <div class="center-wrapper">
        <img src="/images/logo.png" alt="Logo" class="logo" onerror="this.style.display='none'">
        <div class="org-title">MYGOONERS</div>
        <div class="org-subtitle">{{ setting('site_name', 'MyGooners') }}</div>
        <div class="org-en">{{ setting('site_description', 'Your trusted marketplace for products and services') }}</div>
        <div class="error-card">
            <div class="maintenance-icon">&#128736;</div>
            <div class="error-title">{{ __('maintenance.heading') }}</div>
            <div class="error-message">{{ __('maintenance.message') }}</div>
            <div class="error-message" style="color:#888;">{{ __('maintenance.message_en') }}</div>
        </div>
    </div>
</body>
</html>

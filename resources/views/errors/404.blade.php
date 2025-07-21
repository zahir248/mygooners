<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <!-- Favicon -->
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
            margin-bottom: 22px;
        }
        .btn-row {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 14px;
        }
        .btn {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.05rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 11px 28px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }
        .btn-red {
            background: #ff0000;
            color: #fff;
        }
        .btn-red:hover {
            background: #cc0000;
        }
        .btn-light {
            background: #ffeaea;
            color: #ff0000;
            border: 1px solid #ff0000;
        }
        .btn-light:hover {
            background: #ffd6d6;
        }
        .btn svg {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }
        @media (max-width: 600px) {
            .error-card { padding: 18px 4px; }
            .org-title { font-size: 1.3rem; }
        }
    </style>
</head>
<body>
    <div class="center-wrapper">
        <img src="/images/logo.png" alt="Logo" class="logo" onerror="this.style.display='none'">
        <div class="org-title">MYGOONERS</div>
        <div class="org-subtitle">Your Trusted Platform</div>
        <div class="org-en">Connecting Fans &amp; Services</div>
        <div class="error-card">
            <div class="error-title">MAAF / SORRY</div>
            <div class="error-message">Laman tidak dijumpai <span style="color:#888;">/ Page not found</span></div>
            <div class="btn-row">
                <a href="/" class="btn btn-red">
                    <svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                    Kembali ke Laman Utama
                </a>
                <a href="/" class="btn btn-light">
                    <svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                    Back to Main Page
                </a>
            </div>
        </div>
    </div>
</body>
</html> 
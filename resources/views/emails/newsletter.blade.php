<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            padding: 30px 20px;
            text-align: center;
        }
        .logo {
            display: inline-flex;
            align-items: center;
            color: white;
            text-decoration: none;
            font-size: 24px;
            font-weight: bold;
        }
        .logo-icon {
            background-color: white;
            color: #dc2626;
            border-radius: 8px;
            padding: 8px 12px;
            margin-right: 10px;
            font-weight: bold;
        }
        .content {
            padding: 40px 30px;
        }
        .subject {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 20px;
            text-align: center;
        }
        .message {
            font-size: 16px;
            line-height: 1.8;
            color: #4b5563;
            margin-bottom: 30px;
        }
        .cta-button {
            display: inline-block;
            background-color: #dc2626;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            background-color: #f3f4f6;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
            color: #6b7280;
            font-size: 14px;
        }
        .unsubscribe {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .unsubscribe a {
            color: #6b7280;
            text-decoration: none;
            font-size: 12px;
        }
        .unsubscribe a:hover {
            text-decoration: underline;
        }
        .social-links {
            margin-top: 20px;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #6b7280;
            text-decoration: none;
        }
        .social-links a:hover {
            color: #dc2626;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 0;
                box-shadow: none;
            }
            .content {
                padding: 20px;
            }
            .header {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="{{ route('home') }}" class="logo">
                <div class="logo-icon">MG</div>
                MyGooners
            </a>
        </div>
        
        <div class="content">
            <h1 class="subject">{{ $subject }}</h1>
            
            <div class="message">
                {!! nl2br(e($content)) !!}
            </div>
            
            <div style="text-align: center;">
                <a href="{{ route('home') }}" class="cta-button">
                    Lawati MyGooners
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>MyGooners</strong></p>
            <p>Komuniti peminat Arsenal terbaik</p>
            <p>Berita terkini, video, dan kandungan eksklusif</p>
            
            <div class="social-links">
                <a href="#">Twitter</a>
                <a href="#">Facebook</a>
                <a href="#">Instagram</a>
                <a href="#">YouTube</a>
            </div>
            
            <div class="unsubscribe">
                <p>
                    <a href="{{ $unsubscribeUrl }}">
                        Berhenti melanggani newsletter ini
                    </a>
                </p>
            </div>
            
            <p style="margin-top: 20px; font-size: 12px; color: #9ca3af;">
                © {{ date('Y') }} MyGooners. Hak cipta terpelihara.
            </p>
        </div>
    </div>
</body>
</html>

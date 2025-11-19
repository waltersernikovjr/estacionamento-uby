<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verificado - Estacionamento Uby</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #FF6A00 0%, #CC5500 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            padding: 50px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 30px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.5s ease-out 0.2s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .checkmark {
            width: 50px;
            height: 50px;
            stroke: white;
            stroke-width: 3;
            stroke-linecap: round;
            fill: none;
            animation: drawCheck 0.5s ease-out 0.5s both;
        }

        @keyframes drawCheck {
            from {
                stroke-dasharray: 100;
                stroke-dashoffset: 100;
            }
            to {
                stroke-dasharray: 100;
                stroke-dashoffset: 0;
            }
        }

        h1 {
            color: #1f2937;
            font-size: 28px;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .message {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .date {
            color: #9ca3af;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #FF6A00 0%, #CC5500 100%);
            color: white;
            padding: 15px 40px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(255, 106, 0, 0.4);
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 106, 0, 0.6);
        }

        .button:active {
            transform: translateY(0);
        }

        .footer {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
            color: #9ca3af;
            font-size: 13px;
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 24px;
            }

            .success-icon {
                width: 60px;
                height: 60px;
            }

            .checkmark {
                width: 35px;
                height: 35px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <svg class="checkmark" viewBox="0 0 52 52">
                <path d="M14 27l7 7 17-17"/>
            </svg>
        </div>

        <h1>✅ Email Verificado!</h1>

        <p class="message">
            Seu email foi verificado com sucesso.<br>
            Agora você pode fazer login no sistema.
        </p>

        <p class="date">
            Verificado em: {{ $verified_at }}
        </p>

        <a href="{{ config('app.frontend_url', 'http://localhost:3000') }}/login" class="button">
            Fazer Login
        </a>

        <div class="footer">
            <p>Estacionamento Uby &copy; {{ date('Y') }}</p>
            <p style="margin-top: 5px; color: #d1d5db;">Gerenciamento inteligente de vagas</p>
        </div>
    </div>
</body>
</html>

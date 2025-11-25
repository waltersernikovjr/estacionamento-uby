<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Inválido - Estacionamento Uby</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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

        .error-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 30px;
            background: #ef4444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
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
            margin-bottom: 30px;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 15px 40px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
            margin: 0 5px;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.6);
        }

        .button:active {
            transform: translateY(0);
        }

        .button.secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            box-shadow: 0 4px 15px rgba(107, 114, 128, 0.4);
        }

        .button.secondary:hover {
            box-shadow: 0 6px 20px rgba(107, 114, 128, 0.6);
        }

        .buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
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

            .error-icon {
                width: 60px;
                height: 60px;
                font-size: 30px;
            }

            .buttons {
                flex-direction: column;
            }

            .button {
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">
            ❌
        </div>

        <h1>Link Inválido</h1>

        <p class="message">
            Este link de verificação é inválido ou expirou.<br>
            Por favor, solicite um novo email de verificação.
        </p>

        <div class="buttons">
            <a href="{{ config('app.frontend_url', 'http://localhost:3000') }}/resend-verification" class="button">
                Reenviar Email
            </a>
            <a href="{{ config('app.frontend_url', 'http://localhost:3000') }}/login" class="button secondary">
                Voltar ao Login
            </a>
        </div>

        <div class="footer">
            <p>Estacionamento Uby &copy; {{ date('Y') }}</p>
            <p style="margin-top: 5px; color: #d1d5db;">Gerenciamento inteligente de vagas</p>
        </div>
    </div>
</body>
</html>

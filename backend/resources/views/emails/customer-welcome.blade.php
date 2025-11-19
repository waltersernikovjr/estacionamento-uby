<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao Estacionamento Uby</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #FF6A00 0%, #CC5500 100%);
        }
        .container {
            background-color: #ffffff;
            padding: 0;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            text-align: center;
            padding: 40px 30px;
            background: linear-gradient(135deg, #FF6A00 0%, #CC5500 100%);
            color: white;
        }
        .header h1 {
            color: white;
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .content {
            padding: 30px;
        }
        .button {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #FF6A00 0%, #CC5500 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 25px;
            margin: 20px 0;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(255, 106, 0, 0.4);
            transition: transform 0.2s;
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 106, 0, 0.5);
        }
        .footer {
            text-align: center;
            padding: 20px 30px;
            background-color: #f9f9f9;
            color: #666;
            font-size: 12px;
        }
        .info-box {
            background-color: #FFF5EE;
            padding: 20px;
            border-left: 4px solid #FF6A00;
            border-radius: 8px;
            margin: 20px 0;
        }
        ul {
            padding-left: 20px;
        }
        ul li {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚗 Estacionamento Uby</h1>
        </div>

        <div class="content">
            <h2>Olá, {{ $customer->name }}! 👋</h2>

            <p>Seja muito bem-vindo ao <strong>Estacionamento Uby</strong>!</p>

            <p>Estamos felizes em tê-lo como nosso cliente. Para começar a usar nossos serviços, você precisa confirmar seu endereço de email.</p>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">
                    ✅ Confirmar Email
                </a>
            </div>

            <div class="info-box">
                <strong>📋 Seus dados cadastrados:</strong><br>
                <strong>Nome:</strong> {{ $customer->name }}<br>
                <strong>Email:</strong> {{ $customer->email }}<br>
                <strong>CPF:</strong> {{ $customer->cpf }}
            </div>

            <p><strong>⚠️ Importante:</strong></p>
            <ul>
                <li>Este link expira em <strong>24 horas</strong></li>
                <li>Após confirmar, você poderá reservar vagas e acessar todas as funcionalidades</li>
                <li>Se você não solicitou este cadastro, ignore este email</li>
            </ul>

            <p>Caso o botão não funcione, copie e cole este link no navegador:</p>
            <p style="word-break: break-all; color: #007bff;">{{ $verificationUrl }}</p>
        </div>

        <div class="footer">
            <p>© 2025 Estacionamento Uby - Muzambinho/MG</p>
            <p>Este é um email automático, por favor não responda.</p>
        </div>
    </div>
</body>
</html>

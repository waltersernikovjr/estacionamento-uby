<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao Estacionamento Uby</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
        }
        .content {
            padding: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .info-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin: 20px 0;
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

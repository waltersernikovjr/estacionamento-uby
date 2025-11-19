<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao Sistema Uby</title>
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
            border-bottom: 2px solid #28a745;
        }
        .header h1 {
            color: #28a745;
            margin: 0;
        }
        .content {
            padding: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #28a745;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background-color: #218838;
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
            border-left: 4px solid #28a745;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👨‍💼 Sistema Operacional Uby</h1>
        </div>
        
        <div class="content">
            <h2>Olá, {{ $operator->name }}! 👋</h2>
            
            <p>Bem-vindo ao <strong>Sistema de Gerenciamento de Estacionamento Uby</strong>!</p>
            
            <p>Seu cadastro como operador foi criado com sucesso. Para ativar sua conta e começar a gerenciar as vagas, confirme seu endereço de email.</p>
            
            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">
                    ✅ Confirmar Email
                </a>
            </div>
            
            <div class="info-box">
                <strong>📋 Seus dados cadastrados:</strong><br>
                <strong>Nome:</strong> {{ $operator->name }}<br>
                <strong>Email:</strong> {{ $operator->email }}<br>
                <strong>CPF:</strong> {{ $operator->cpf }}
            </div>
            
            <p><strong>🔐 Como operador, você poderá:</strong></p>
            <ul>
                <li>Gerenciar vagas de estacionamento</li>
                <li>Visualizar reservas ativas</li>
                <li>Atender clientes via chat</li>
                <li>Gerar relatórios</li>
            </ul>
            
            <p><strong>⚠️ Importante:</strong></p>
            <ul>
                <li>Este link expira em <strong>24 horas</strong></li>
                <li>Após confirmar, você terá acesso total ao sistema</li>
                <li>Mantenha suas credenciais seguras</li>
            </ul>
            
            <p>Caso o botão não funcione, copie e cole este link no navegador:</p>
            <p style="word-break: break-all; color: #28a745;">{{ $verificationUrl }}</p>
        </div>
        
        <div class="footer">
            <p>© 2025 Estacionamento Uby - Muzambinho/MG</p>
            <p>Este é um email automático, por favor não responda.</p>
        </div>
    </div>
</body>
</html>

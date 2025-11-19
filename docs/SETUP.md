# 🅿️ Sistema de Estacionamento - Setup Guide

## 📋 Pré-requisitos

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Redis >= 7.0
- Node.js >= 18 (para o serviço de chat separado)

## 🚀 Instalação

### 1. Clonar o Repositório
```bash
git clone <repository-url>
cd estacionamento-uby
```

### 2. Instalar Dependências
```bash
composer install
```

### 3. Configurar Ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar .env
```env
APP_NAME="Parking Management"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=parking_management
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

SANCTUM_STATEFUL_DOMAINS=localhost
```

### 5. Criar Banco de Dados
```bash
mysql -u root -p
CREATE DATABASE parking_management;
EXIT;
```

### 6. Executar Migrations
```bash
php artisan migrate
```

### 7. (Opcional) Popular com Dados de Teste
```bash
php artisan db:seed
```

### 8. Iniciar Servidor
```bash
php artisan serve
```

A API estará disponível em: `http://localhost:8000`

---

## 🏗️ Estrutura do Projeto

```
app/
├── Application/
│   ├── DTOs/              # Data Transfer Objects
│   └── Services/          # Lógica de negócio
├── Domain/
│   ├── Entities/          # Modelos Eloquent
│   └── Repositories/      # Interfaces de repositório
├── Http/
│   ├── Controllers/       # Controllers da API
│   ├── Requests/          # Form Requests (validação)
│   └── Resources/         # API Resources (serialização)
└── Infrastructure/
    └── Repositories/      # Implementações de repositório
```

---

## 🧪 Executar Testes

### Testes Unitários
```bash
php artisan test --filter Unit
```

### Testes de Integração
```bash
php artisan test --filter Feature
```

### Todos os Testes
```bash
php artisan test
```

### Com Cobertura
```bash
php artisan test --coverage
```

---

## 📚 Documentação da API

A documentação completa dos endpoints está em: [`docs/API.md`](./docs/API.md)

### Endpoints Principais

- **Auth**: `/api/v1/operators|customers/register|login|logout|me`
- **Resources**: `/api/v1/operators|customers|vehicles|parking-spots|reservations|payments`
- **Utilities**: `/api/v1/address/{cep}` (consulta ViaCEP)

---

## 🔐 Autenticação

A API utiliza **Laravel Sanctum** para autenticação baseada em tokens.

### Fluxo de Autenticação

1. **Registrar/Login**: Obter token
```bash
curl -X POST http://localhost:8000/api/v1/customers/register \
  -H "Content-Type: application/json" \
  -d '{"name":"João","email":"joao@test.com","password":"senha123","password_confirmation":"senha123"}'
```

2. **Usar Token**: Incluir em todas as requisições
```bash
curl -X GET http://localhost:8000/api/v1/parking-spots-available \
  -H "Authorization: Bearer SEU_TOKEN"
```

---

## 🎯 Fluxo de Uso Completo

### 1. Registrar Cliente
```bash
POST /api/v1/customers/register
```

### 2. Fazer Login
```bash
POST /api/v1/customers/login
```

### 3. Cadastrar Veículo
```bash
POST /api/v1/vehicles
Authorization: Bearer {token}
```

### 4. Ver Vagas Disponíveis
```bash
GET /api/v1/parking-spots-available
Authorization: Bearer {token}
```

### 5. Criar Reserva
```bash
POST /api/v1/reservations
Authorization: Bearer {token}
```

### 6. Finalizar Reserva
```bash
POST /api/v1/reservations/{id}/complete
Authorization: Bearer {token}
```

### 7. Criar Pagamento
```bash
POST /api/v1/payments
Authorization: Bearer {token}
```

### 8. Marcar Pagamento como Pago
```bash
POST /api/v1/payments/{id}/mark-as-paid
Authorization: Bearer {token}
```

---

## 🐳 Docker (Opcional)

### Iniciar Containers
```bash
docker-compose up -d
```

### Parar Containers
```bash
docker-compose down
```

---

## 🔧 Comandos Úteis

### Limpar Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Recriar Banco
```bash
php artisan migrate:fresh --seed
```

### Verificar Rotas
```bash
php artisan route:list
```

### Gerar IDE Helper (Autocompletar)
```bash
composer require --dev barryvdh/laravel-ide-helper
php artisan ide-helper:generate
php artisan ide-helper:models -N
```

---

## 🌐 Integração ViaCEP

O sistema integra com a API ViaCEP para consulta de endereços por CEP.

- **Cache**: 24 horas
- **Timeout**: 10 segundos
- **Endpoint**: `GET /api/v1/address/{cep}`

---

## 💬 Serviço de Chat (Node.js)

O chat em tempo real é um microserviço **separado** desenvolvido em Node.js.

Ver: [`README-CHAT.md`](./README-CHAT.md) _(se implementado)_

---

## 📝 Padrões de Código

- **Clean Architecture**: Domain, Application, Infrastructure, Presentation
- **Repository Pattern**: Abstração de acesso a dados
- **DTO Pattern**: Transferência de dados entre camadas
- **SOLID Principles**: Single Responsibility, Open/Closed, etc.
- **PSR-12**: Code Style padrão PHP

---

## 🤝 Contribuição

### Fluxo Git

```bash
# Criar branch
git checkout -b feature/nova-funcionalidade

# Commit semântico
git commit -m "feat: adiciona nova funcionalidade"

# Push
git push origin feature/nova-funcionalidade

# Criar Pull Request para develop
```

### Convenção de Commits

- `feat:` Nova funcionalidade
- `fix:` Correção de bug
- `docs:` Documentação
- `test:` Testes
- `refactor:` Refatoração
- `chore:` Manutenção

---

## 🐛 Troubleshooting

### Erro de Conexão MySQL
```bash
# Verificar se MySQL está rodando
sudo systemctl status mysql

# Reiniciar MySQL
sudo systemctl restart mysql
```

### Erro de Permissão
```bash
# Dar permissão nas pastas
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Redis Não Conecta
```bash
# Verificar Redis
redis-cli ping

# Iniciar Redis
sudo systemctl start redis
```

---

## 📧 Suporte

Para dúvidas ou problemas, abra uma issue no repositório.

---

## 📄 Licença

Este projeto é parte de um teste técnico.

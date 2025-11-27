# 🚀 Guia de Instalação - Estacionamento Uby

## 📋 Pré-requisitos

- **Docker** 20.10+
- **Docker Compose** 1.29+
- **Git**
- **Porta 3000** (Frontend), **8000** (Backend/API), **3001** (Chat), **8025** (Mailhog)

## 🔧 Instalação do Zero

### 1. Clonar o Repositório

```bash
git clone https://github.com/ranielisilveira/estacionamento-uby.git
cd estacionamento-uby
```

### 2. Subir os Containers Docker

```bash
docker-compose up -d
```

Este comando irá:
- ✅ Criar rede Docker `estacionamento-network`
- ✅ Subir MySQL (porta 3307)
- ✅ Subir Redis (porta 6380)
- ✅ Subir Backend Laravel (PHP 8.3)
- ✅ Subir Frontend React (porta 3000)
- ✅ Subir Chat Service Node.js (porta 3001)
- ✅ Subir Nginx (porta 8000)
- ✅ Subir Mailhog para testes de email (porta 8025)

**⏱️ Aguarde aproximadamente 2 minutos** para todos os containers iniciarem corretamente.

### 3. Verificar Status dos Containers

```bash
docker ps
```

Você deve ver **7 containers rodando**:
- `estacionamento-frontend` - Frontend React (healthy)
- `estacionamento-backend` - Backend Laravel (healthy)
- `estacionamento-nginx` - Servidor Web (healthy)
- `estacionamento-mysql` - Banco de dados (healthy)
- `estacionamento-redis` - Cache (healthy)
- `estacionamento-chat` - WebSocket Chat
- `estacionamento-mailhog` - Email testing

### 4. Executar Migrations e Seeders

```bash
# Entrar no container do backend
docker-compose exec backend bash

# Rodar migrations
php artisan migrate

# Popular banco de dados com dados de exemplo
php artisan db:seed

# Criar usuários de teste
php artisan db:seed --class=TestUsersSeeder

# Sair do container
exit
```

### 5. Verificar Instalação

Acesse os seguintes URLs no navegador:

#### ✅ Frontend
- **URL:** http://localhost:3000
- **Descrição:** Interface do sistema (Login/Dashboard)

#### ✅ API Backend
- **URL:** http://localhost:8000/api/v1
- **Documentação Swagger:** http://localhost:8000/api/documentation

#### ✅ Mailhog (Emails de teste)
- **URL:** http://localhost:8025
- **Descrição:** Interface para visualizar emails enviados pelo sistema

#### ✅ Chat Service
- **URL:** ws://localhost:3001
- **Descrição:** WebSocket para chat em tempo real

---

## 👥 Usuários de Teste

### 🙋 Cliente (Customer)
- **Email:** `cliente@uby.com`
- **Senha:** `senha123`
- **Dashboard:** http://localhost:3000/customer/dashboard
- **Funcionalidades:**
  - Ver vagas disponíveis
  - Fazer reservas
  - Gerenciar veículos
  - Chat com operadores
  - Histórico de reservas

### 👨‍💼 Operador (Operator)
- **Email:** `operador@uby.com`
- **Senha:** `senha123`
- **Dashboard:** http://localhost:3000/operator/dashboard
- **Funcionalidades:**
  - Gerenciar vagas (criar, editar, deletar)
  - Ver todas as reservas
  - Finalizar reservas com cálculo de pagamento
  - Chat com clientes
  - Dashboard com estatísticas

---

## 🧪 Testes

### Executar Testes Unitários (46 testes)

```bash
docker-compose exec backend php artisan test --testsuite=Unit
```

**Resultado esperado:** 46 testes passando com 187 assertions

### Executar Todos os Testes

```bash
docker-compose exec backend php artisan test
```

---

## 🔌 Endpoints Principais da API

### Autenticação

#### Login Cliente
```bash
curl -X POST http://localhost:8000/api/v1/customers/login \
  -H "Content-Type: application/json" \
  -d '{"email":"cliente@uby.com","password":"senha123"}'
```

#### Login Operador
```bash
curl -X POST http://localhost:8000/api/v1/operators/login \
  -H "Content-Type: application/json" \
  -d '{"email":"operador@uby.com","password":"senha123"}'
```

### Vagas (Requer autenticação)

#### Listar Vagas Disponíveis
```bash
curl -X GET http://localhost:8000/api/v1/parking-spots-available \
  -H "Authorization: Bearer {seu_token}"
```

#### Criar Vaga (Operador)
```bash
curl -X POST http://localhost:8000/api/v1/parking-spots \
  -H "Authorization: Bearer {token_operador}" \
  -H "Content-Type: application/json" \
  -d '{
    "number": "A-25",
    "type": "regular",
    "hourly_price": 5.00,
    "width": 2.5,
    "length": 5.0
  }'
```

### Reservas

#### Criar Reserva (Cliente)
```bash
curl -X POST http://localhost:8000/api/v1/reservations \
  -H "Authorization: Bearer {token_cliente}" \
  -H "Content-Type: application/json" \
  -d '{
    "parking_spot_id": 1,
    "vehicle_id": 1,
    "entry_time": "2025-11-27T10:00:00Z"
  }'
```

#### Finalizar Reserva (Operador)
```bash
curl -X POST http://localhost:8000/api/v1/operator/reservations/{id}/finish \
  -H "Authorization: Bearer {token_operador}" \
  -H "Content-Type: application/json" \
  -d '{
    "exit_time": "2025-11-27T12:00:00Z"
  }'
```

### Veículos

#### Listar Meus Veículos (Cliente)
```bash
curl -X GET http://localhost:8000/api/v1/vehicles \
  -H "Authorization: Bearer {token_cliente}"
```

#### Adicionar Veículo (Cliente)
```bash
curl -X POST http://localhost:8000/api/v1/vehicles \
  -H "Authorization: Bearer {token_cliente}" \
  -H "Content-Type: application/json" \
  -d '{
    "license_plate": "ABC-1234",
    "brand": "Toyota",
    "model": "Corolla",
    "color": "Prata",
    "type": "car"
  }'
```

---

## 📚 Documentação Adicional

- **Swagger/OpenAPI:** http://localhost:8000/api/documentation
- **Arquitetura:** Veja `docs/architecture/backend-structure.md`
- **Schema do Banco:** Veja `docs/database/schema.md`
- **Progresso do Projeto:** Veja `PROGRESSO.md`
- **Checklist de Validação:** Veja `CHECKLIST.md`

---

## 🐛 Solução de Problemas

### Containers não iniciam
```bash
# Parar todos os containers
docker-compose down

# Limpar volumes (⚠️ apaga dados - migrations/seeders rodam automaticamente ao resubir)
docker-compose down -v

# Rebuildar e iniciar
docker-compose build --no-cache
docker-compose up -d
# ✅ Migrations e seeders executam automaticamente!
```

### Erro de permissão no Laravel
```bash
docker-compose exec backend chmod -R 777 storage bootstrap/cache
```

### Reset completo do banco de dados
```bash
# Opção 1: Via comando artisan (manual)
docker-compose exec backend php artisan migrate:fresh --seed

# Opção 2: Remover volumes (recomendado - setup automático)
docker-compose down -v
docker-compose up -d
# ✅ Migrations e seeders rodam automaticamente!
```

### Ver logs de um container
```bash
# Backend
docker logs estacionamento-backend

# Frontend
docker logs estacionamento-frontend

# Nginx
docker logs estacionamento-nginx
```

---

## 🛑 Parar o Sistema

```bash
# Parar containers (mantém dados)
docker-compose stop

# Parar e remover containers (mantém volumes)
docker-compose down

# Parar, remover containers E volumes (⚠️ apaga tudo)
docker-compose down -v
```

---

## 🔄 Atualizar o Sistema

```bash
# Baixar atualizações
git pull origin main

# Rebuildar containers
docker-compose build

# Reiniciar
docker-compose down
docker-compose up -d

# Rodar novas migrations (apenas se houver novas)
docker-compose exec backend php artisan migrate
```

> **💡 Nota:** Migrations iniciais e seeders rodam **automaticamente** na primeira execução!

---

## ✨ Funcionalidades Implementadas

- ✅ **Clean Architecture** (Domain, Application, Infrastructure, Presentation)
- ✅ **SOLID Principles**
- ✅ **Repository Pattern**
- ✅ **DTO Pattern**
- ✅ **Service Layer**
- ✅ **JWT Authentication** (Laravel Sanctum)
- ✅ **Email Verification**
- ✅ **Chat em Tempo Real** (Socket.io)
- ✅ **Integração ViaCEP**
- ✅ **Docker Multi-container**
- ✅ **46 Testes Unitários** (187 assertions)
- ✅ **Documentação Swagger/OpenAPI**
- ✅ **Redis Cache**
- ✅ **Queue Jobs** para emails
- ✅ **Validação de formulários**
- ✅ **API RESTful versionada** (v1)

---

## 📞 Suporte

Em caso de dúvidas:
1. Verifique a documentação Swagger: http://localhost:8000/api/documentation
2. Verifique os logs dos containers
3. Consulte os arquivos de documentação em `docs/`

---

**Desenvolvido com ❤️ para o desafio Full Stack da Uby**

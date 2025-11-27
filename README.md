# 🅿️ Estacionamento Uby - Sistema de Gestão

Sistema completo de gerenciamento de estacionamento com backend Laravel, frontend React e chat em tempo real.

## 📋 Sobre o Projeto

Sistema desenvolvido para gestão completa de estacionamento, incluindo:
- ✅ Cadastro e autenticação de clientes e operadores
- ✅ Gerenciamento de veículos e vagas
- ✅ Sistema de reservas e pagamentos
- ✅ Chat em tempo real entre cliente e operador
- ✅ Notificações por email
- ✅ Dashboard interativo para clientes e operadores

## 🚀 Quick Start

```bash
# Clone o repositório
git clone https://github.com/ranielisilveira/estacionamento-uby.git
cd estacionamento-uby

# Inicie todos os serviços
docker-compose up -d

# Aguarde ~30 segundos e acesse:
# Frontend: http://localhost:3000
# Backend API: http://localhost:8000
# Swagger Docs: http://localhost:8000/api/documentation
# MailHog: http://localhost:8025
```

## 📚 Documentação

### Setup e Instalação
- **[Guia de Instalação Completo](docs/setup/INSTALL.md)** - Passo a passo detalhado
- **[Credenciais de Teste](docs/setup/CREDENTIALS.md)** - Usuários para teste

### Desenvolvimento
- **[Guia de Desenvolvimento](docs/development/DEVELOPMENT.md)** - Como desenvolver
- **[Progresso do Projeto](docs/PROGRESSO.md)** - Status de desenvolvimento
- **[Checklist de Entrega](docs/CHECKLIST.md)** - Validação de requisitos

### Arquitetura e Técnica
- **[Estrutura do Backend](docs/architecture/backend-structure.md)** - Clean Architecture
- **[Schema do Banco](docs/database/schema.md)** - Modelagem completa
- **[API REST](docs/API.md)** - Endpoints documentados
- **[Sistema de Email](docs/EMAIL_VERIFICATION_SYSTEM.md)** - Verificação de email

### Resumo Executivo
- **[Resumo do Projeto](docs/RESUMO-EXECUTIVO.md)** - Visão geral completa

## 🛠️ Stack Tecnológica

### Backend
- **PHP 8.2** - Linguagem de programação
- **Laravel 12** - Framework PHP
- **MySQL 8.0** - Banco de dados
- **Redis 7.4** - Cache e filas
- **Laravel Sanctum** - Autenticação
- **Swagger/OpenAPI** - Documentação da API

### Frontend
- **React 19.2** - Framework JavaScript
- **TypeScript 5.9** - Tipagem estática
- **Vite 7.2** - Build tool com hot reload
- **Tailwind CSS 3.4** - Estilização
- **Zustand 5.0** - Gerenciamento de estado
- **React Router 7.9** - Roteamento
- **Socket.io Client 4.8.1** - WebSocket client

### Infraestrutura
- **Docker & Docker Compose** - Containerização
- **Node.js 20** - Runtime JavaScript
- **Socket.io 4.7** - Chat em tempo real (server)
- **Express 4.18** - Framework web Node.js
- **Nginx 1.27** - Web server
- **MailHog 1.0** - Testes de email

## 🏗️ Arquitetura

### Backend (Clean Architecture)
```
app/
├── Domain/          # Entidades e contratos
├── Application/     # Casos de uso e serviços
├── Infrastructure/  # Implementações (repos, mail, etc)
└── Presentation/    # Controllers e API Resources
```

### Frontend (Clean Architecture)
```
src/
├── domain/          # Tipos e lógica de negócio
├── application/     # Stores e casos de uso
├── infrastructure/  # APIs e clientes HTTP
└── presentation/    # Componentes React
```

## 🔑 Usuários de Teste

### Operador
```
Email: operador@uby.com
Senha: senha123
```

### Cliente
```
Email: cliente@uby.com
Senha: senha123
```

## 📊 Endpoints Principais

### Autenticação
- `POST /api/v1/customers/login` - Login cliente
- `POST /api/v1/operators/login` - Login operador
- `POST /api/v1/customers/register` - Registro

### Vagas e Reservas
- `GET /api/v1/parking-spots-available` - Vagas disponíveis
- `POST /api/v1/reservations` - Criar reserva
- `GET /api/v1/reservations` - Minhas reservas

### Veículos
- `GET /api/v1/vehicles` - Listar veículos
- `POST /api/v1/vehicles` - Cadastrar veículo

📖 **Documentação completa:** http://localhost:8000/api/documentation

## �� Testes

### Backend - Testes Unitários
```bash
docker-compose exec backend php artisan test --testsuite=Unit
```

**46 testes unitários** com cobertura de:
- Services (ParkingSpot, Payment, Reservation, Vehicle)
- Cálculo de preços
- Validações de negócio

## 🐳 Containers e Portas

| Serviço | Container | Porta | Descrição |
|---------|-----------|-------|-----------|
| Frontend | estacionamento-frontend | 3000 | React + Vite (hot reload) |
| Backend API | estacionamento-backend | 8000 | Laravel 12 |
| Chat Service | estacionamento-chat | 3001 | WebSocket (Socket.io) |
| MySQL | estacionamento-mysql | 3307 | Banco de dados |
| Redis | estacionamento-redis | 6380 | Cache (Redis 7.4) |
| MailHog | estacionamento-mailhog | 8025 | Interface de emails |
| Nginx | estacionamento-nginx | 8000 | Proxy reverso |

## 📝 Comandos Úteis

```bash
# Ver logs
docker-compose logs -f frontend
docker-compose logs -f backend

# Rodar migrations
docker-compose exec backend php artisan migrate

# Rodar seeders
docker-compose exec backend php artisan db:seed

# Limpar cache
docker-compose exec backend php artisan cache:clear

# Parar todos os containers
docker-compose down

# Rebuildar containers
docker-compose up -d --build
```

## 🔒 Segurança

- ✅ Senhas hasheadas com bcrypt
- ✅ Autenticação via Laravel Sanctum
- ✅ Verificação obrigatória de email
- ✅ CORS configurado
- ✅ Rate limiting em rotas sensíveis
- ✅ SQL Injection protection (Eloquent)
- ✅ XSS protection

## 🎯 Funcionalidades Implementadas

### Cliente
- [x] Cadastro com verificação de email
- [x] Login/Logout
- [x] Dashboard com estatísticas
- [x] Gerenciamento de veículos
- [x] Visualização de vagas disponíveis
- [x] Criação de reservas
- [x] Histórico de reservas
- [x] Chat com operador
- [x] Cálculo automático de pagamento

### Operador
- [x] Login/Logout
- [x] Dashboard com estatísticas
- [x] Gerenciamento de vagas
- [x] Visualização de todas as reservas
- [x] Busca por placa
- [x] Finalização de reservas
- [x] Chat com clientes
- [x] Observações em reservas

## 📦 Estrutura do Projeto

```
estacionamento-uby/
├── backend/           # Laravel 12 API
├── frontend/          # React 19 + TypeScript
├── chat-service/      # Node.js WebSocket
├── nginx/             # Configuração Nginx
├── docs/              # Documentação completa
│   ├── setup/         # Guias de instalação
│   ├── development/   # Guias de desenvolvimento
│   ├── architecture/  # Documentação técnica
│   ├── database/      # Schema e modelagem
│   └── api/           # Documentação de APIs
└── docker-compose.yml # Orquestração dos containers
```

## 🤝 Contribuindo

1. Fork o projeto
2. Crie uma branch (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto é proprietário e foi desenvolvido para fins acadêmicos.

## 👤 Autor

**Ranieli Silveira**
- GitHub: [@ranielisilveira](https://github.com/ranielisilveira)

---

**Desenvolvido com ❤️ em Gravataí/RS - Brasil**  
**Data:** Novembro 2025  
**Versão:** 1.0.0

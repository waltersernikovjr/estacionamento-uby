# API Documentation - Parking Management System

## Base URL
```
http://localhost/api/v1
```

## Authentication
A API utiliza **Laravel Sanctum** para autenticação baseada em tokens.

### Endpoints Públicos (Não requerem autenticação)

#### 1. Registrar Operador
```http
POST /operators/register
Content-Type: application/json

{
  "name": "João Silva",
  "email": "joao@example.com",
  "password": "senha123",
  "password_confirmation": "senha123",
  "phone": "11999999999"
}
```

**Response (201):**
```json
{
  "user": {
    "id": 1,
    "name": "João Silva",
    "email": "joao@example.com",
    "phone": "11999999999",
    "type": "operator"
  },
  "token": "1|abc123..."
}
```

#### 2. Login Operador
```http
POST /operators/login
Content-Type: application/json

{
  "email": "joao@example.com",
  "password": "senha123"
}
```

#### 3. Registrar Cliente
```http
POST /customers/register
Content-Type: application/json

{
  "name": "Maria Santos",
  "email": "maria@example.com",
  "cpf": "12345678900",
  "password": "senha123",
  "password_confirmation": "senha123",
  "phone": "11988888888",
  "street": "Rua Exemplo",
  "neighborhood": "Centro",
  "city": "São Paulo",
  "state": "SP",
  "zip_code": "01310100"
}
```

#### 4. Login Cliente
```http
POST /customers/login
Content-Type: application/json

{
  "email": "maria@example.com",
  "password": "senha123"
}
```

#### 5. Consultar CEP
```http
GET /address/01310100
```

**Response (200):**
```json
{
  "data": {
    "zip_code": "01310-100",
    "street": "Avenida Paulista",
    "neighborhood": "Bela Vista",
    "city": "São Paulo",
    "state": "SP",
    "complement": "",
    "ibge": "3550308"
  }
}
```

---

### Endpoints Protegidos (Requerem token de autenticação)

**Todas as requisições protegidas devem incluir o header:**
```
Authorization: Bearer {token}
```

#### Informações do Usuário

##### Dados do Operador Logado
```http
GET /operators/me
Authorization: Bearer {token}
```

##### Dados do Cliente Logado
```http
GET /customers/me
Authorization: Bearer {token}
```

##### Logout
```http
POST /operators/logout
POST /customers/logout
Authorization: Bearer {token}
```

---

## Resources (CRUD)

### Operadores
```http
GET    /operators         # Listar todos
POST   /operators         # Criar novo
GET    /operators/{id}    # Ver detalhes
PUT    /operators/{id}    # Atualizar
DELETE /operators/{id}    # Deletar
```

### Clientes
```http
GET    /customers         # Listar todos
POST   /customers         # Criar novo
GET    /customers/{id}    # Ver detalhes
PUT    /customers/{id}    # Atualizar
DELETE /customers/{id}    # Deletar
```

### Vagas de Estacionamento
```http
GET    /parking-spots              # Listar todas
GET    /parking-spots-available    # Listar apenas disponíveis
POST   /parking-spots              # Criar nova
GET    /parking-spots/{id}         # Ver detalhes
PUT    /parking-spots/{id}         # Atualizar
DELETE /parking-spots/{id}         # Deletar
```

**Criar Vaga:**
```json
{
  "number": "A01",
  "type": "regular",     // regular|motorcycle|disabled|electric
  "status": "available"  // available|occupied|maintenance
}
```

### Veículos
```http
GET    /vehicles                    # Listar (filtrar por customer_id)
POST   /vehicles                    # Cadastrar novo
GET    /vehicles/{id}               # Ver detalhes
PUT    /vehicles/{id}               # Atualizar
DELETE /vehicles/{id}               # Deletar
```

**Criar Veículo:**
```json
{
  "customer_id": 1,
  "license_plate": "ABC1234",
  "brand": "Toyota",
  "model": "Corolla",
  "color": "Prata",
  "type": "car"  // car|motorcycle|truck|van
}
```

### Reservas
```http
GET    /reservations              # Listar todas
POST   /reservations              # Criar nova
GET    /reservations/{id}         # Ver detalhes
DELETE /reservations/{id}         # Deletar (não permitido)
POST   /reservations/{id}/complete # Finalizar reserva
POST   /reservations/{id}/cancel   # Cancelar reserva
```

**Criar Reserva:**
```json
{
  "customer_id": 1,
  "vehicle_id": 1,
  "parking_spot_id": 1,
  "entry_time": "2025-11-18T14:30:00",
  "expected_exit_time": "2025-11-18T18:30:00"
}
```

**Finalizar Reserva:**
```json
{
  "exit_time": "2025-11-18T17:45:00"
}
```

**Response:** Calcula automaticamente o valor baseado em:
- R$ 5,00 por hora
- R$ 1,00 por cada bloco de 15 minutos adicional

### Pagamentos
```http
POST   /payments                     # Criar pagamento
GET    /payments/{id}                # Ver detalhes
PUT    /payments/{id}                # Atualizar
DELETE /payments/{id}                # Deletar
POST   /payments/{id}/mark-as-paid   # Marcar como pago
```

**Criar Pagamento:**
```json
{
  "reservation_id": 1,
  "amount": 25.00,
  "payment_method": "credit_card",  // credit_card|debit_card|pix|cash
  "status": "pending"  // pending|paid|failed|refunded
}
```

---

## Códigos de Status HTTP

- `200` - OK (Sucesso)
- `201` - Created (Recurso criado)
- `204` - No Content (Deletado com sucesso)
- `404` - Not Found (Recurso não encontrado)
- `422` - Unprocessable Entity (Erro de validação)
- `500` - Internal Server Error (Erro do servidor)

## Mensagens de Erro

**Formato padrão:**
```json
{
  "message": "Descrição do erro"
}
```

**Erros de validação:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["O email já está em uso"],
    "password": ["A senha deve ter no mínimo 8 caracteres"]
  }
}
```

---

## Regras de Negócio

1. **Reservas**: Uma vaga só pode ter uma reserva ativa por vez
2. **Pagamentos**: Uma reserva só pode ter um pagamento
3. **Vagas Ocupadas**: Não podem ser deletadas
4. **Cálculo de Estacionamento**: Automático na finalização da reserva
5. **Cache**: Endereços do ViaCEP são cacheados por 24 horas
6. **Autenticação**: Tokens Sanctum válidos até logout explícito

---

## Exemplos de Uso com cURL

### Registrar e Fazer Login
```bash
# Registrar
curl -X POST http://localhost/api/v1/customers/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Silva",
    "email": "joao@test.com",
    "cpf": "12345678900",
    "password": "senha123",
    "password_confirmation": "senha123"
  }'

# Login
curl -X POST http://localhost/api/v1/customers/login \
  -H "Content-Type: application/json" \
  -d '{"email": "joao@test.com", "password": "senha123"}'
```

### Usar Endpoints Protegidos
```bash
# Listar vagas disponíveis
curl -X GET http://localhost/api/v1/parking-spots-available \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"

# Criar reserva
curl -X POST http://localhost/api/v1/reservations \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 1,
    "vehicle_id": 1,
    "parking_spot_id": 1,
    "entry_time": "2025-11-18T14:00:00"
  }'
```

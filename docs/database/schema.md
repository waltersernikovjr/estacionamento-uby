# Database Schema - Estacionamento Uby

## Visão Geral

O banco de dados MySQL 8.0 utiliza arquitetura relacional normalizada seguindo as melhores práticas de design. Todas as tabelas possuem timestamps (`created_at`, `updated_at`) e chaves primárias auto-incrementais.

## Diagrama de Relacionamentos

```
customers (1) ──→ (N) vehicles
customers (1) ──→ (N) reservations
operators (1) ──→ (N) parking_spots
parking_spots (1) ──→ (N) reservations
vehicles (1) ──→ (N) reservations
reservations (1) ──→ (1) payments
customers (1) ──→ (N) chat_sessions
operators (1) ──→ (N) chat_sessions
chat_sessions (1) ──→ (N) chat_messages
```

---

## Tabelas Principais

### 1. customers
Armazena dados dos clientes do estacionamento.

**Campos:**
- `id`: BIGINT UNSIGNED PRIMARY KEY
- `name`: VARCHAR(255) NOT NULL
- `cpf`: VARCHAR(14) NOT NULL UNIQUE
- `rg`: VARCHAR(20) NULLABLE
- `email`: VARCHAR(255) NOT NULL UNIQUE
- `password`: VARCHAR(255) NOT NULL
- `phone`: VARCHAR(20) NULLABLE
- `email_verified_at`: TIMESTAMP NULL
- `address_zipcode`: VARCHAR(9) NOT NULL
- `address_street`: VARCHAR(255) NOT NULL
- `address_number`: VARCHAR(20) NOT NULL
- `address_complement`: VARCHAR(255) NULLABLE
- `address_neighborhood`: VARCHAR(255) NOT NULL
- `address_city`: VARCHAR(255) NOT NULL
- `address_state`: VARCHAR(2) NOT NULL
- `remember_token`: VARCHAR(100) NULLABLE
- `created_at`, `updated_at`: TIMESTAMP

**Índices:** email, cpf

---

### 2. operators
Armazena dados dos operadores do estacionamento.

**Campos:**
- `id`: BIGINT UNSIGNED PRIMARY KEY
- `name`: VARCHAR(255) NOT NULL
- `cpf`: VARCHAR(14) NOT NULL UNIQUE
- `email`: VARCHAR(255) NOT NULL UNIQUE
- `password`: VARCHAR(255) NOT NULL
- `phone`: VARCHAR(20) NULLABLE
- `email_verified_at`: TIMESTAMP NULL
- `remember_token`: VARCHAR(100) NULLABLE
- `created_at`, `updated_at`: TIMESTAMP

**Índices:** email, cpf

---

### 3. vehicles
Armazena veículos cadastrados pelos clientes.

**Campos:**
- `id`: BIGINT UNSIGNED PRIMARY KEY
- `customer_id`: BIGINT UNSIGNED NOT NULL (FK → customers)
- `license_plate`: VARCHAR(10) NOT NULL
- `brand`: VARCHAR(100) NOT NULL
- `model`: VARCHAR(100) NOT NULL
- `color`: VARCHAR(50) NOT NULL
- `type`: ENUM('car', 'motorcycle', 'truck') NOT NULL
- `deleted_at`: TIMESTAMP NULL (soft delete)
- `created_at`, `updated_at`: TIMESTAMP

**Índices:** customer_id, license_plate  
**Unique:** license_plate + deleted_at (permite reutilização após soft delete)

---

### 4. parking_spots
Vagas de estacionamento gerenciadas por operadores.

**Campos:**
- `id`: BIGINT UNSIGNED PRIMARY KEY
- `operator_id`: BIGINT UNSIGNED NOT NULL (FK → operators)
- `number`: VARCHAR(10) NOT NULL UNIQUE
- `type`: ENUM('regular', 'vip', 'disabled') NOT NULL
- `hourly_price`: DECIMAL(8,2) NOT NULL
- `width`: DECIMAL(5,2) NOT NULL (metros)
- `length`: DECIMAL(5,2) NOT NULL (metros)
- `status`: ENUM('available', 'occupied', 'maintenance', 'reserved') DEFAULT 'available'
- `created_at`, `updated_at`: TIMESTAMP

**Índices:** status, operator_id, number

---

### 5. reservations
Reservas de vagas pelos clientes.

**Campos:**
- `id`: BIGINT UNSIGNED PRIMARY KEY
- `customer_id`: BIGINT UNSIGNED NOT NULL (FK → customers)
- `vehicle_id`: BIGINT UNSIGNED NOT NULL (FK → vehicles)
- `parking_spot_id`: BIGINT UNSIGNED NOT NULL (FK → parking_spots)
- `operator_id`: BIGINT UNSIGNED NULLABLE (FK → operators)
- `entry_time`: TIMESTAMP NOT NULL
- `exit_time`: TIMESTAMP NULL
- `total_amount`: DECIMAL(10,2) NULLABLE
- `operator_notes`: TEXT NULLABLE
- `status`: ENUM('active', 'completed', 'cancelled') DEFAULT 'active'
- `created_at`, `updated_at`: TIMESTAMP

**Índices:** customer_id, vehicle_id, parking_spot_id, status, entry_time

---

### 6. payments
Pagamentos das reservas.

**Campos:**
- `id`: BIGINT UNSIGNED PRIMARY KEY
- `reservation_id`: BIGINT UNSIGNED NOT NULL UNIQUE (FK → reservations)
- `amount`: DECIMAL(10,2) NOT NULL
- `hours_parked`: DECIMAL(10,2) NOT NULL
- `payment_method`: ENUM('credit_card', 'debit_card', 'cash', 'pix') NOT NULL
- `status`: ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending'
- `paid_at`: TIMESTAMP NULL
- `created_at`, `updated_at`: TIMESTAMP

**Índices:** reservation_id, status

---

### 7. chat_sessions
Sessões de chat entre clientes e operadores.

**Campos:**
- `id`: BIGINT UNSIGNED PRIMARY KEY
- `customer_id`: BIGINT UNSIGNED NOT NULL (FK → customers)
- `operator_id`: BIGINT UNSIGNED NULLABLE (FK → operators)
- `status`: ENUM('active', 'closed') DEFAULT 'active'
- `created_at`, `updated_at`: TIMESTAMP

**Índices:** customer_id, operator_id, status

---

### 8. chat_messages
Mensagens trocadas nas sessões de chat.

**Campos:**
- `id`: BIGINT UNSIGNED PRIMARY KEY
- `chat_session_id`: BIGINT UNSIGNED NOT NULL (FK → chat_sessions)
- `sender_id`: BIGINT UNSIGNED NOT NULL
- `sender_type`: ENUM('customer', 'operator') NOT NULL
- `message`: TEXT NOT NULL
- `is_read`: BOOLEAN DEFAULT FALSE
- `created_at`, `updated_at`: TIMESTAMP

**Índices:** chat_session_id, (sender_id + sender_type)

---

### 9. personal_access_tokens
Tokens de autenticação Laravel Sanctum.

**Campos:**
- `id`: BIGINT UNSIGNED PRIMARY KEY
- `tokenable_type`: VARCHAR(255) NOT NULL
- `tokenable_id`: BIGINT UNSIGNED NOT NULL
- `name`: VARCHAR(255) NOT NULL
- `token`: VARCHAR(64) NOT NULL UNIQUE
- `abilities`: TEXT NULL
- `last_used_at`: TIMESTAMP NULL
- `expires_at`: TIMESTAMP NULL
- `created_at`, `updated_at`: TIMESTAMP

**Índices:** (tokenable_type + tokenable_id), token

---

## Regras de Negócio

### Vehicles
- Um cliente pode ter múltiplos veículos
- Placa deve ser única entre veículos ativos
- Soft delete permite histórico

### Parking Spots
- Tipos: regular (carros), vip (caminhões), disabled (PCD)
- Status controlado automaticamente por reservas
- Preço pode variar por vaga

### Reservations
- Uma reserva ativa por vaga
- `exit_time` NULL enquanto ativa
- `total_amount` calculado no checkout
- `operator_notes` para observações

### Payments
- Relacionamento 1:1 com reservations
- `hours_parked` pode ser decimal (ex: 2.5h)
- `amount` = hours_parked × hourly_price

---

## Foreign Keys e Cascade

- **CASCADE**: vehicles (customer deletado remove veículos), chat_messages
- **RESTRICT**: reservations, payments (evita deleções acidentais)
- **SET NULL**: chat_sessions.operator_id (mantém histórico)

---

## Dados de Seed

### Usuários de Teste
```
Operador: operador@uby.com / senha123
Cliente: cliente@uby.com / senha123
```

### Vagas Iniciais
- 20 regulares (A-01 a A-20): R$ 5-8/hora
- 15 motos (M-01 a M-15): R$ 3/hora
- 5 VIP (V-01 a V-05): R$ 12/hora
- 3 PCD (D-01 a D-03): R$ 4/hora

---

**Última atualização:** 27/11/2025  
**MySQL:** 8.0 | **Laravel:** 11.x

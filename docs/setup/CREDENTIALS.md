# 🧪 Credenciais de Teste

Este arquivo contém as credenciais dos usuários de teste criados no sistema.

## 👨‍💼 Operador

**Para testar o fluxo do operador:**

- **Nome:** João Silva (Operador)
- **Email:** `operador@uby.com`
- **Senha:** `senha123`
- **CPF:** 123.456.789-00

**Funcionalidades disponíveis:**
- ✅ Dashboard com estatísticas
- ✅ Gerenciar vagas (criar, editar, deletar)
- ✅ Ver todas as reservas ativas
- ✅ Finalizar reservas com observações
- ✅ Buscar veículos por placa
- ✅ Chat em tempo real com clientes

---

## 👤 Cliente

**Para testar o fluxo do cliente:**

- **Nome:** Maria Santos (Cliente)
- **Email:** `cliente@uby.com`
- **Senha:** `senha123`
- **CPF:** 987.654.321-00
- **RG:** 12.345.678-9

**Endereço:**
- CEP: 37750-000
- Rua: Rua Principal, 123, Apto 101
- Bairro: Centro
- Cidade: Muzambinho - MG

**Funcionalidades disponíveis:**
- ✅ Ver vagas disponíveis
- ✅ Cadastrar veículos
- ✅ Fazer reservas
- ✅ Ver minhas reservas
- ✅ Finalizar/cancelar reservas
- ✅ Chat em tempo real com operador
- ✅ Cálculo automático de preço por tempo

---

## 🌐 URLs de Acesso

- **Frontend:** http://localhost:3000
- **Backend API:** http://localhost:8000/api/v1
- **Swagger Docs:** http://localhost:8000/api/documentation
- **MailHog (Email Testing):** http://localhost:8025
- **Chat Service:** ws://localhost:3001

---

## 🚀 Como Testar

### 1. Fluxo do Operador

1. Acesse http://localhost:3000
2. Clique em "Login como Operador"
3. Use as credenciais do operador
4. Teste:
   - Ver estatísticas no dashboard
   - Criar uma nova vaga
   - Ver reservas ativas
   - Finalizar uma reserva
   - Conversar com clientes no chat

### 2. Fluxo do Cliente

1. Acesse http://localhost:3000
2. Use as credenciais do cliente
3. Teste:
   - Ver vagas disponíveis
   - Cadastrar um veículo
   - Fazer uma reserva
   - Ver histórico de reservas
   - Conversar com operador no chat

---

## 🔄 Recriar Usuários

Se precisar recriar os usuários de teste:

```bash
docker-compose exec backend php artisan db:seed --class=TestUsersSeeder
```

Ou para resetar todo o banco e recriar tudo:

```bash
docker-compose exec backend php artisan migrate:fresh --seed
```

---

## 📝 Notas

- ✅ Todos os usuários já têm email verificado
- ✅ As senhas são simples propositalmente (apenas para teste)
- ✅ O sistema usa autenticação JWT via Laravel Sanctum
- ✅ Chat funciona em tempo real via WebSocket
- ✅ Emails são capturados pelo MailHog (não são enviados de verdade)

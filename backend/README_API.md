# Parking Management API - Documentação

## 📚 Documentação Interativa Swagger

Acesse a documentação completa e interativa em:
**http://localhost:8000/api/documentation**

A documentação Swagger permite:
- ✅ Visualizar todos os endpoints disponíveis
- ✅ Ver exemplos de request/response
- ✅ Testar os endpoints diretamente pelo navegador
- ✅ Copiar código de exemplo em várias linguagens

## 🔑 Autenticação

A API utiliza **Laravel Sanctum** para autenticação via Bearer Token.

### Como obter um token:
```bash
POST /api/v1/customers/register
```

O token retornado deve ser usado em todas as requisições autenticadas:
```
Authorization: Bearer {seu-token-aqui}
```

## 📋 Endpoints Principais

### Authentication
- `POST /api/v1/customers/register` - Registrar novo cliente
- `GET /api/v1/customers/me` - Dados do cliente autenticado

### Vehicles
- `POST /api/v1/vehicles` - Criar veículo
- `GET /api/v1/vehicles` - Listar veículos

### Parking Spots
- `POST /api/v1/parking-spots` - Criar vaga
- `GET /api/v1/parking-spots` - Listar vagas

### Reservations
- `POST /api/v1/reservations` - Criar reserva
- `POST /api/v1/reservations/{id}/complete` - Completar reserva

### Payments
- `POST /api/v1/payments` - Criar pagamento
- `POST /api/v1/payments/{id}/mark-as-paid` - Confirmar pagamento

### Utils
- `GET /api/v1/viacep/{cep}` - Consultar CEP via ViaCEP

## 💰 Cálculo de Valores

O sistema calcula automaticamente o valor da reserva ao completá-la:

- **Valor por hora cheia**: R$ 5,00
- **Valor por fração (15 minutos)**: R$ 1,00

**Exemplo**: 2 horas e 30 minutos = (2 × R$ 5,00) + (2 × R$ 1,00) = **R$ 12,00**

## 🧪 Testando a API

### Script de teste automatizado:
```bash
# Limpar banco e rodar testes
docker-compose exec backend php artisan migrate:fresh --seed
bash test-api.sh
```

### Teste manual com curl:
```bash
# 1. Registrar cliente
curl -X POST http://localhost:8000/api/v1/customers/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Silva",
    "email": "joao@email.com",
    "cpf": "12345678900",
    "password": "password123",
    "password_confirmation": "password123",
    "zip_code": "01310100",
    "street": "Av Paulista",
    "number": "1000",
    "neighborhood": "Bela Vista",
    "city": "São Paulo",
    "state": "SP"
  }'

# 2. Usar o token retornado nas próximas requisições
TOKEN="seu-token-aqui"

# 3. Ver dados do cliente
curl -X GET http://localhost:8000/api/v1/customers/me \
  -H "Authorization: Bearer $TOKEN"
```

## 🗂️ Arquitetura da Documentação

Para manter o código limpo e organizado:

### ✅ Boas Práticas Adotadas:

1. **Documentação Separada**: 
   - Arquivo dedicado: `app/Http/Controllers/Api/ApiDocumentation.php`
   - NÃO misture documentação com lógica de negócio nos controllers

2. **Variáveis de Ambiente**:
   - Todas as configurações estão documentadas em `.env.example`
   - Nunca commitar arquivos `.env` com dados sensíveis

3. **Regenerar Documentação**:
   ```bash
   docker-compose exec backend php artisan l5-swagger:generate
   ```

## 🔧 Configurações Importantes

### Variáveis de Ambiente (.env)

```env
# Swagger
L5_SWAGGER_CONST_HOST=http://localhost:8000

# ViaCEP
VIACEP_URL=https://viacep.com.br/ws

# Pricing
PARKING_HOURLY_RATE=5.00
PARKING_FRACTION_RATE=1.00
PARKING_FRACTION_MINUTES=15
```

## 📖 Regras de Desenvolvimento

### Ao adicionar novos endpoints:

1. ✅ Documente no `ApiDocumentation.php`
2. ✅ Adicione variáveis no `.env.example` se necessário
3. ✅ Regenere a documentação Swagger
4. ✅ Atualize o script `test-api.sh` se aplicável

### Não faça:

- ❌ Adicionar anotações @OA nos controllers de negócio
- ❌ Misturar documentação com lógica
- ❌ Esquecer de documentar novas variáveis de ambiente

## 🚀 Deploy em Produção

Antes de fazer deploy:

1. Configure o `.env` de produção baseado no `.env.example`
2. Desative o debug: `APP_DEBUG=false`
3. Configure SSL/HTTPS
4. Atualize `L5_SWAGGER_CONST_HOST` para o domínio de produção
5. Configure CORS adequadamente

---

**Desenvolvido para Uby - Sistema de Gerenciamento de Estacionamento**

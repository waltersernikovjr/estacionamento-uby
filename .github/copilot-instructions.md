# GitHub Copilot Instructions - Estacionamento Uby

## 📚 LEITURA OBRIGATÓRIA

**SEMPRE** leia os seguintes arquivos antes de gerar qualquer código:

1. **`.ai-guidelines.md`** - Regras críticas de código limpo (SEM comentários redundantes!)
2. **`.ai-context.md`** - Checklist obrigatório de qualidade e workflow
3. **Este arquivo** - Padrões de arquitetura específicos do projeto

## 🎯 Objetivos do Sistema

### Funcionalidades Principais:
- **Operadores:** Gestão de vagas (número, preço, dimensões)
- **Clientes:** Cadastro completo com veículos, reserva de vagas, cálculo de pagamento
- **Chat:** Comunicação em tempo real operador-cliente via WebSocket
- **Emails:** Confirmação de cadastro, notificações
- **Integrações:** API ViaCEP para validação de endereços

## 🏗️ Arquitetura e Padrões

### Estrutura de Camadas (Backend Laravel)
```
backend/
├── app/
│   ├── Domain/               # Entidades, Value Objects, Contratos
│   ├── Application/
│   │   ├── DTOs/            # Data Transfer Objects
│   │   ├── Services/        # Lógica de negócio
│   │   └── UseCases/        # Casos de uso específicos
│   ├── Infrastructure/
│   │   ├── Repositories/    # Implementações de repositórios
│   │   ├── Persistence/     # Eloquent Models
│   │   └── External/        # Integrações externas (ViaCEP)
│   └── Presentation/
│       ├── Http/
│       │   ├── Controllers/ # Controllers REST
│       │   ├── Requests/    # Form Requests (validação)
│       │   ├── Resources/   # API Resources (transformação)
│       │   └── Middleware/
│       └── Console/         # Comandos Artisan
├── tests/
│   ├── Unit/               # Testes unitários
│   ├── Feature/            # Testes de integração
│   └── E2E/               # Testes end-to-end
```

### Design Patterns Obrigatórios:
- **Repository Pattern:** Abstração de acesso a dados
- **Service Layer:** Lógica de negócio isolada
- **DTO Pattern:** Transferência de dados tipada
- **Factory Pattern:** Criação de objetos complexos
- **Strategy Pattern:** Cálculo de preços, políticas de estacionamento
- **Observer Pattern:** Eventos do Laravel (email, logs)
- **Dependency Injection:** Sempre via construtor

### Princípios SOLID:
- **S**ingle Responsibility: Cada classe uma responsabilidade
- **O**pen/Closed: Aberto para extensão, fechado para modificação
- **L**iskov Substitution: Interfaces bem definidas
- **I**nterface Segregation: Interfaces específicas
- **D**ependency Inversion: Dependa de abstrações

## 💻 Padrões de Código

### PHP/Laravel:
```php
// ✅ BOM - Service com DI e tipagem forte
final class ParkingSpotService
{
    public function __construct(
        private readonly ParkingSpotRepositoryInterface $repository,
        private readonly PriceCalculatorInterface $calculator,
        private readonly EventDispatcherInterface $dispatcher
    ) {}

    public function reserve(ReserveParkingSpotDTO $dto): Reservation
    {
        $spot = $this->repository->findAvailable($dto->spotId);
        
        if ($spot === null) {
            throw new ParkingSpotNotAvailableException();
        }

        $reservation = Reservation::create([...]);
        $this->dispatcher->dispatch(new ReservationCreated($reservation));
        
        return $reservation;
    }
}

// ❌ RUIM - Controller com lógica de negócio
public function store(Request $request)
{
    $spot = ParkingSpot::find($request->spot_id);
    if (!$spot) return response()->json(['error' => 'Not found'], 404);
    // Lógica de negócio no controller - EVITAR!
}
```

### Naming Conventions:
- **Classes:** PascalCase (`ParkingSpotService`)
- **Methods:** camelCase (`calculatePrice`)
- **Variáveis:** camelCase (`$totalPrice`)
- **Constants:** UPPER_SNAKE_CASE (`MAX_PARKING_TIME`)
- **Database:** snake_case (`parking_spots`, `created_at`)
- **Routes:** kebab-case (`/parking-spots`)

### Validação:
- Sempre usar **Form Requests** customizados
- Validações complexas em **Rules** customizadas
- DTOs validados na construção

### Responses API:
```php
// Sempre usar API Resources
return new ParkingSpotResource($spot);

// JSON padronizado
{
    "data": {...},
    "meta": {...},
    "links": {...}
}

// Erros padronizados
{
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

## 🧪 Testes

### Cobertura Mínima: 80%
```php
// Testes unitários - Services isolados
public function test_should_calculate_price_correctly(): void
{
    $calculator = new HourlyPriceCalculator();
    $price = $calculator->calculate(hours: 3, basePrice: 10.0);
    
    $this->assertEquals(30.0, $price);
}

// Testes de integração - Controllers + Database
public function test_should_create_reservation_successfully(): void
{
    $customer = Customer::factory()->create();
    $spot = ParkingSpot::factory()->available()->create();
    
    $response = $this->actingAs($customer)
        ->postJson('/api/reservations', [
            'parking_spot_id' => $spot->id,
            'vehicle_id' => $customer->vehicles->first()->id,
        ]);
    
    $response->assertStatus(201)
        ->assertJsonStructure(['data' => ['id', 'status']]);
    
    $this->assertDatabaseHas('reservations', [
        'customer_id' => $customer->id,
        'parking_spot_id' => $spot->id,
    ]);
}
```

### Nomenclatura de Testes:
- `test_should_[expected_behavior]_when_[condition]`
- `test_should_throw_exception_when_[invalid_condition]`

## 🔒 Segurança

- **Autenticação:** Laravel Sanctum + JWT
- **Rate Limiting:** Por rota e por usuário
- **SQL Injection:** Sempre usar Query Builder/Eloquent
- **XSS:** Escape de outputs (automático no Laravel)
- **CSRF:** Tokens em formulários
- **Validação:** Nunca confiar em dados do cliente
- **Logs:** Sem dados sensíveis (senhas, tokens)

## 📊 Performance

- **Eager Loading:** Sempre usar `with()` para relacionamentos
- **Cache:** Redis para queries frequentes (lista de vagas)
- **Queues:** Jobs assíncronos para emails
- **Índices:** Em foreign keys e campos de busca
- **Pagination:** Sempre para listas

```php
// ✅ BOM - Eager loading
$customers = Customer::with(['vehicles', 'reservations'])->get();

// ❌ RUIM - N+1 problem
$customers = Customer::all();
foreach ($customers as $customer) {
    $customer->vehicles; // Query adicional para cada customer
}
```

## 📝 Documentação

### PHPDoc obrigatório:
```php
/**
 * Calculate the total price for a parking reservation.
 *
 * @param Reservation $reservation The parking reservation
 * @param Carbon $exitTime The time when vehicle exits
 * @return float The total price in BRL
 * @throws InvalidReservationException If reservation is invalid
 */
public function calculateTotalPrice(Reservation $reservation, Carbon $exitTime): float
```

### README de cada microserviço deve conter:
- Setup/instalação
- Variáveis de ambiente
- Como rodar testes
- Endpoints principais
- Arquitetura de decisão (ADRs)

## 🔄 Git Workflow

### Commits semânticos:
```
feat: add parking spot reservation endpoint
fix: correct price calculation for overnight parking
refactor: extract price calculation to strategy pattern
test: add unit tests for PriceCalculator
docs: update API documentation
chore: configure Docker for MySQL
```

### Branches:
- `main` - produção
- `develop` - desenvolvimento
- `feature/nome-da-feature`
- `fix/nome-do-bug`

## 🐳 Docker

- Containers isolados por microserviço
- Volume para persistência de dados
- Networks customizadas
- Health checks configurados
- Multi-stage builds para otimização

## 🚨 Checklist de Qualidade

Antes de commitar, verificar:
- [ ] Código segue PSR-12
- [ ] PHPStan/Psalm nível máximo sem erros
- [ ] Testes escritos e passando
- [ ] Sem código comentado
- [ ] Sem `dd()`, `var_dump()` esquecidos
- [ ] Migrations com `down()` implementado
- [ ] DTOs validados
- [ ] API Resources para responses
- [ ] Tratamento de exceções adequado
- [ ] Logs informativos (sem dados sensíveis)

## 🎓 Diferenciais

- **Transações de banco:** Para operações críticas
- **Eventos e Listeners:** Desacoplamento
- **Políticas de acesso:** Gates e Policies
- **Versionamento de API:** `/api/v1/`
- **Documentação OpenAPI/Swagger:** Sempre atualizada
- **Monitoramento:** Logs estruturados para debugging
- **Graceful degradation:** Sistema funciona mesmo se serviço externo falhar

## 💡 Dicas para o Copilot

Quando gerar código para este projeto:
1. Sempre aplicar os padrões acima
2. Priorizar legibilidade sobre cleverness
3. Preferir composição sobre herança
4. Criar testes junto com o código
5. Adicionar PHPDoc completo
6. Pensar em edge cases
7. Validar inputs rigorosamente
8. Retornar tipos explícitos
9. Usar constantes ao invés de magic numbers
10. Código em inglês, comentários em português quando necessário

---

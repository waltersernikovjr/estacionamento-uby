# Arquitetura do Backend - Microserviço Laravel

## 🏗️ Estrutura de Diretórios

```
backend/
├── app/
│   ├── Domain/                          # Camada de Domínio (Entidades, Contratos)
│   │   ├── Entities/                    # Value Objects e Domain Entities
│   │   │   ├── Operator.php
│   │   │   ├── Customer.php
│   │   │   ├── Vehicle.php
│   │   │   ├── ParkingSpot.php
│   │   │   └── Reservation.php
│   │   ├── Contracts/                   # Interfaces (Repository, Services)
│   │   │   ├── Repositories/
│   │   │   │   ├── OperatorRepositoryInterface.php
│   │   │   │   ├── CustomerRepositoryInterface.php
│   │   │   │   ├── VehicleRepositoryInterface.php
│   │   │   │   ├── ParkingSpotRepositoryInterface.php
│   │   │   │   ├── ReservationRepositoryInterface.php
│   │   │   │   └── PaymentRepositoryInterface.php
│   │   │   └── Services/
│   │   │       ├── PriceCalculatorInterface.php
│   │   │       └── AddressValidatorInterface.php
│   │   └── Exceptions/                  # Domain Exceptions
│   │       ├── ParkingSpotNotAvailableException.php
│   │       ├── InvalidReservationException.php
│   │       └── CustomerNotFoundException.php
│   │
│   ├── Application/                     # Camada de Aplicação (Casos de Uso)
│   │   ├── DTOs/                        # Data Transfer Objects
│   │   │   ├── Operator/
│   │   │   │   ├── CreateOperatorDTO.php
│   │   │   │   └── UpdateOperatorDTO.php
│   │   │   ├── Customer/
│   │   │   │   ├── CreateCustomerDTO.php
│   │   │   │   └── UpdateCustomerDTO.php
│   │   │   ├── ParkingSpot/
│   │   │   │   ├── CreateParkingSpotDTO.php
│   │   │   │   └── UpdateParkingSpotDTO.php
│   │   │   └── Reservation/
│   │   │       ├── CreateReservationDTO.php
│   │   │       └── CompleteReservationDTO.php
│   │   │
│   │   ├── Services/                    # Application Services (Lógica de Negócio)
│   │   │   ├── Auth/
│   │   │   │   ├── AuthService.php
│   │   │   │   └── EmailVerificationService.php
│   │   │   ├── Operator/
│   │   │   │   └── OperatorService.php
│   │   │   ├── Customer/
│   │   │   │   └── CustomerService.php
│   │   │   ├── ParkingSpot/
│   │   │   │   └── ParkingSpotService.php
│   │   │   ├── Reservation/
│   │   │   │   ├── ReservationService.php
│   │   │   │   └── PriceCalculatorService.php
│   │   │   └── Payment/
│   │   │       └── PaymentService.php
│   │   │
│   │   └── UseCases/                    # Use Cases específicos (opcional)
│   │       ├── Reservation/
│   │       │   ├── CreateReservationUseCase.php
│   │       │   └── CompleteReservationUseCase.php
│   │       └── ParkingSpot/
│   │           └── FindAvailableSpotsUseCase.php
│   │
│   ├── Infrastructure/                  # Camada de Infraestrutura
│   │   ├── Persistence/                 # Eloquent Models
│   │   │   ├── Models/
│   │   │   │   ├── Operator.php
│   │   │   │   ├── Customer.php
│   │   │   │   ├── Vehicle.php
│   │   │   │   ├── ParkingSpot.php
│   │   │   │   ├── Reservation.php
│   │   │   │   ├── Payment.php
│   │   │   │   ├── ChatSession.php
│   │   │   │   └── ChatMessage.php
│   │   │   └── Seeders/
│   │   │       ├── OperatorSeeder.php
│   │   │       ├── CustomerSeeder.php
│   │   │       └── ParkingSpotSeeder.php
│   │   │
│   │   ├── Repositories/                # Implementações de Repositórios
│   │   │   ├── EloquentOperatorRepository.php
│   │   │   ├── EloquentCustomerRepository.php
│   │   │   ├── EloquentVehicleRepository.php
│   │   │   ├── EloquentParkingSpotRepository.php
│   │   │   ├── EloquentReservationRepository.php
│   │   │   └── EloquentPaymentRepository.php
│   │   │
│   │   ├── External/                    # Integrações Externas
│   │   │   ├── ViaCep/
│   │   │   │   ├── ViaCepClient.php
│   │   │   │   └── ViaCepAddressValidator.php
│   │   │   └── Email/
│   │   │       └── MailgunEmailService.php
│   │   │
│   │   └── Cache/                       # Estratégias de Cache
│   │       ├── RedisCacheService.php
│   │       └── CacheKeys.php
│   │
│   ├── Presentation/                    # Camada de Apresentação
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Api/
│   │   │   │   │   ├── V1/
│   │   │   │   │   │   ├── Auth/
│   │   │   │   │   │   │   ├── LoginController.php
│   │   │   │   │   │   │   ├── RegisterController.php
│   │   │   │   │   │   │   └── EmailVerificationController.php
│   │   │   │   │   │   ├── Operator/
│   │   │   │   │   │   │   ├── OperatorController.php
│   │   │   │   │   │   │   └── ParkingSpotController.php
│   │   │   │   │   │   ├── Customer/
│   │   │   │   │   │   │   ├── CustomerController.php
│   │   │   │   │   │   │   ├── VehicleController.php
│   │   │   │   │   │   │   └── ReservationController.php
│   │   │   │   │   │   └── Payment/
│   │   │   │   │   │       └── PaymentController.php
│   │   │   │
│   │   │   ├── Requests/                # Form Requests (Validação)
│   │   │   │   ├── Auth/
│   │   │   │   │   ├── LoginRequest.php
│   │   │   │   │   └── RegisterRequest.php
│   │   │   │   ├── Operator/
│   │   │   │   │   ├── StoreOperatorRequest.php
│   │   │   │   │   └── UpdateOperatorRequest.php
│   │   │   │   ├── Customer/
│   │   │   │   │   ├── StoreCustomerRequest.php
│   │   │   │   │   └── UpdateCustomerRequest.php
│   │   │   │   ├── ParkingSpot/
│   │   │   │   │   ├── StoreParkingSpotRequest.php
│   │   │   │   │   └── UpdateParkingSpotRequest.php
│   │   │   │   └── Reservation/
│   │   │   │       ├── StoreReservationRequest.php
│   │   │   │       └── CompleteReservationRequest.php
│   │   │   │
│   │   │   ├── Resources/               # API Resources (Transformação)
│   │   │   │   ├── Operator/
│   │   │   │   │   └── OperatorResource.php
│   │   │   │   ├── Customer/
│   │   │   │   │   ├── CustomerResource.php
│   │   │   │   │   └── VehicleResource.php
│   │   │   │   ├── ParkingSpot/
│   │   │   │   │   └── ParkingSpotResource.php
│   │   │   │   ├── Reservation/
│   │   │   │   │   └── ReservationResource.php
│   │   │   │   └── Payment/
│   │   │   │       └── PaymentResource.php
│   │   │   │
│   │   │   └── Middleware/
│   │   │       ├── EnsureEmailIsVerified.php
│   │   │       └── CheckParkingSpotOwnership.php
│   │   │
│   │   └── Console/
│   │       └── Commands/
│   │           └── CleanupExpiredReservations.php
│   │
│   └── Providers/
│       ├── AppServiceProvider.php
│       ├── RepositoryServiceProvider.php  # Bindings de Repositories
│       └── RouteServiceProvider.php
│
├── bootstrap/
├── config/
│   ├── database.php
│   ├── cache.php
│   └── services.php                     # Configurações de serviços externos
│
├── database/
│   ├── factories/
│   │   ├── OperatorFactory.php
│   │   ├── CustomerFactory.php
│   │   ├── VehicleFactory.php
│   │   ├── ParkingSpotFactory.php
│   │   └── ReservationFactory.php
│   │
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_operators_table.php
│   │   ├── 2024_01_01_000002_create_customers_table.php
│   │   ├── 2024_01_01_000003_create_vehicles_table.php
│   │   ├── 2024_01_01_000004_create_parking_spots_table.php
│   │   ├── 2024_01_01_000005_create_reservations_table.php
│   │   ├── 2024_01_01_000006_create_payments_table.php
│   │   ├── 2024_01_01_000007_create_chat_sessions_table.php
│   │   └── 2024_01_01_000008_create_chat_messages_table.php
│   │
│   └── seeders/
│       └── DatabaseSeeder.php
│
├── routes/
│   ├── api.php                          # Rotas da API
│   └── web.php
│
├── storage/
│   └── logs/
│
├── tests/
│   ├── Unit/                            # Testes Unitários
│   │   ├── Services/
│   │   │   ├── PriceCalculatorServiceTest.php
│   │   │   └── ReservationServiceTest.php
│   │   └── Repositories/
│   │       └── EloquentParkingSpotRepositoryTest.php
│   │
│   ├── Feature/                         # Testes de Integração
│   │   ├── Auth/
│   │   │   ├── LoginTest.php
│   │   │   └── RegisterTest.php
│   │   ├── Operator/
│   │   │   └── ParkingSpotManagementTest.php
│   │   ├── Customer/
│   │   │   ├── ReservationTest.php
│   │   │   └── VehicleManagementTest.php
│   │   └── Payment/
│   │       └── PaymentCalculationTest.php
│   │
│   └── TestCase.php
│
├── .env.example
├── .env.testing
├── composer.json
├── phpunit.xml
├── artisan
└── README.md
```

## 🎯 Responsabilidades das Camadas

### 1. **Domain Layer** (Domínio)
- **Propósito:** Regras de negócio puras, independentes de framework
- **Contém:** Interfaces, Value Objects, Exceptions de domínio
- **Não depende de:** Nenhuma outra camada

### 2. **Application Layer** (Aplicação)
- **Propósito:** Orquestração de casos de uso, lógica de aplicação
- **Contém:** Services, DTOs, Use Cases
- **Depende de:** Domain Layer

### 3. **Infrastructure Layer** (Infraestrutura)
- **Propósito:** Implementações técnicas (banco, cache, APIs externas)
- **Contém:** Repositories, Models Eloquent, Integrações
- **Depende de:** Domain e Application

### 4. **Presentation Layer** (Apresentação)
- **Propósito:** Entrada/saída (Controllers, Requests, Resources)
- **Contém:** HTTP Controllers, Form Requests, API Resources
- **Depende de:** Application Layer

## 🔗 Fluxo de Requisição

```
HTTP Request
    ↓
Controller (Presentation)
    ↓
Form Request (Validação)
    ↓
Service (Application) ← DTO
    ↓
Repository (Infrastructure)
    ↓
Model (Eloquent)
    ↓
Database
    ↓
Resource (Presentation)
    ↓
HTTP Response
```

## 📦 Exemplo de Implementação

### DTO (Application/DTOs/Reservation/CreateReservationDTO.php)
```php
<?php

namespace App\Application\DTOs\Reservation;

final readonly class CreateReservationDTO
{
    public function __construct(
        public int $customerId,
        public int $vehicleId,
        public int $parkingSpotId,
        public string $entryTime,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            customerId: $data['customer_id'],
            vehicleId: $data['vehicle_id'],
            parkingSpotId: $data['parking_spot_id'],
            entryTime: $data['entry_time'] ?? now()->toDateTimeString(),
        );
    }
}
```

### Service (Application/Services/Reservation/ReservationService.php)
```php
<?php

namespace App\Application\Services\Reservation;

use App\Application\DTOs\Reservation\CreateReservationDTO;
use App\Domain\Contracts\Repositories\ReservationRepositoryInterface;
use App\Domain\Contracts\Repositories\ParkingSpotRepositoryInterface;
use App\Domain\Exceptions\ParkingSpotNotAvailableException;
use App\Infrastructure\Persistence\Models\Reservation;
use Illuminate\Support\Facades\DB;

final class ReservationService
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservationRepository,
        private readonly ParkingSpotRepositoryInterface $parkingSpotRepository,
    ) {}

    public function create(CreateReservationDTO $dto): Reservation
    {
        return DB::transaction(function () use ($dto) {
            $spot = $this->parkingSpotRepository->findAvailable($dto->parkingSpotId);
            
            if ($spot === null) {
                throw new ParkingSpotNotAvailableException('Vaga não disponível');
            }

            $reservation = $this->reservationRepository->create([
                'customer_id' => $dto->customerId,
                'vehicle_id' => $dto->vehicleId,
                'parking_spot_id' => $dto->parkingSpotId,
                'entry_time' => $dto->entryTime,
                'status' => 'active',
            ]);

            $this->parkingSpotRepository->updateStatus($spot->id, 'occupied');

            return $reservation;
        });
    }
}
```

### Repository Interface (Domain/Contracts/Repositories/ReservationRepositoryInterface.php)
```php
<?php

namespace App\Domain\Contracts\Repositories;

use App\Infrastructure\Persistence\Models\Reservation;

interface ReservationRepositoryInterface
{
    public function create(array $data): Reservation;
    public function findById(int $id): ?Reservation;
    public function findActiveBySpot(int $spotId): ?Reservation;
    public function updateStatus(int $id, string $status): bool;
}
```

### Repository Implementation (Infrastructure/Repositories/EloquentReservationRepository.php)
```php
<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\Repositories\ReservationRepositoryInterface;
use App\Infrastructure\Persistence\Models\Reservation;

final class EloquentReservationRepository implements ReservationRepositoryInterface
{
    public function create(array $data): Reservation
    {
        return Reservation::create($data);
    }

    public function findById(int $id): ?Reservation
    {
        return Reservation::find($id);
    }

    public function findActiveBySpot(int $spotId): ?Reservation
    {
        return Reservation::where('parking_spot_id', $spotId)
            ->where('status', 'active')
            ->first();
    }

    public function updateStatus(int $id, string $status): bool
    {
        return Reservation::where('id', $id)->update(['status' => $status]);
    }
}
```

### Controller (Presentation/Http/Controllers/Api/V1/Customer/ReservationController.php)
```php
<?php

namespace App\Presentation\Http\Controllers\Api\V1\Customer;

use App\Application\DTOs\Reservation\CreateReservationDTO;
use App\Application\Services\Reservation\ReservationService;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Requests\Reservation\StoreReservationRequest;
use App\Presentation\Http\Resources\Reservation\ReservationResource;
use Illuminate\Http\JsonResponse;

final class ReservationController extends Controller
{
    public function __construct(
        private readonly ReservationService $reservationService
    ) {}

    public function store(StoreReservationRequest $request): JsonResponse
    {
        $dto = CreateReservationDTO::fromRequest($request->validated());
        
        $reservation = $this->reservationService->create($dto);
        
        return (new ReservationResource($reservation))
            ->response()
            ->setStatusCode(201);
    }
}
```

## 🔧 Service Provider Bindings

### RepositoryServiceProvider.php
```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Contracts\Repositories\ReservationRepositoryInterface;
use App\Infrastructure\Repositories\EloquentReservationRepository;
// ... outros imports

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ReservationRepositoryInterface::class,
            EloquentReservationRepository::class
        );
        
        // Outros bindings...
    }
}
```

## ✅ Vantagens desta Arquitetura

1. **Testabilidade:** Fácil mockar repositórios e services
2. **Manutenibilidade:** Código organizado e responsabilidades claras
3. **Escalabilidade:** Fácil adicionar novas features
4. **SOLID:** Todos os princípios aplicados
5. **Independência:** Domínio não depende de framework
6. **Reutilização:** Services podem ser usados em controllers, commands, jobs

---

**Próximos passos:**
1. Instalar Laravel
2. Criar estrutura de pastas
3. Implementar migrations
4. Criar repositories e services base
5. Configurar Docker

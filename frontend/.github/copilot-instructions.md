# GitHub Copilot Instructions - Frontend Estacionamento Uby

## 🎯 Contexto do Projeto

Sistema de gerenciamento de estacionamento com frontend React + TypeScript seguindo **Clean Architecture**. Interface para clientes (reservas) e operadores (gestão de vagas).

## 🏗️ Arquitetura Obrigatória

### Estrutura de Pastas
```
src/
├── domain/          # Entidades, Types, Value Objects
├── application/     # Stores (Zustand), Hooks customizados
├── infrastructure/  # APIs (Axios), WebSocket
└── presentation/    # Components, Pages
```

### Camadas e Responsabilidades

**Domain (Domínio)**
- Tipos TypeScript (User, ParkingSpot, Reservation, etc)
- Enums e constantes de negócio
- NUNCA importar de outras camadas
- Exemplo: `domain/types/types.ts`

**Application (Aplicação)**
- Stores globais (Zustand)
- Hooks customizados (lógica reutilizável)
- Casos de uso complexos
- Pode importar: Domain
- Exemplo: `application/stores/authStore.ts`

**Infrastructure (Infraestrutura)**
- Clients HTTP (Axios, Fetch)
- WebSocket (Socket.io)
- APIs externas (ViaCEP)
- localStorage, sessionStorage
- Pode importar: Domain, Application
- Exemplo: `infrastructure/api/authApi.ts`

**Presentation (Apresentação)**
- Componentes React
- Páginas
- Hooks de UI (useState, useEffect)
- Pode importar: Todas as camadas
- Exemplo: `presentation/pages/CustomerDashboard.tsx`

## 💻 Padrões de Código

### React Components

```tsx
// ✅ BOM - Component funcional com tipagem
interface ParkingSpotCardProps {
  spot: ParkingSpot;
  onReserve: (id: number) => void;
  isLoading?: boolean;
}

export function ParkingSpotCard({ spot, onReserve, isLoading = false }: ParkingSpotCardProps) {
  const [isHovered, setIsHovered] = useState(false);
  
  return (
    <div className="card">
      <h3>{spot.number}</h3>
      <button onClick={() => onReserve(spot.id)}>Reservar</button>
    </div>
  );
}

// ❌ RUIM - Sem tipagem, lógica misturada
export default function Card(props: any) {
  // Lógica de API aqui - ERRADO!
  const fetchData = async () => {
    const res = await fetch('/api/spots');
  };
  
  return <div>{props.data}</div>;
}
```

### Hooks Customizados

```tsx
// ✅ BOM - Hook reutilizável com tipagem
export function useParkingSpots() {
  const [spots, setSpots] = useState<ParkingSpot[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    parkingApi.getAvailableSpots()
      .then(setSpots)
      .catch((err) => setError(err.message))
      .finally(() => setIsLoading(false));
  }, []);

  return { spots, isLoading, error, refetch: loadSpots };
}

// ❌ RUIM - Sem tratamento de erro, sem loading
export function useSpots() {
  const [data, setData] = useState();
  useEffect(() => {
    fetch('/spots').then(r => r.json()).then(setData);
  }, []);
  return data;
}
```

### Chamadas de API

```tsx
// ✅ BOM - Try/catch, feedback visual, tipagem
const handleReserve = async (spotId: number) => {
  try {
    setIsLoading(true);
    await parkingApi.createReservation({
      parking_spot_id: spotId,
      vehicle_id: selectedVehicleId,
    });
    
    toast.success('Reserva criada com sucesso!');
    refetchReservations();
  } catch (err: any) {
    toast.error(err.response?.data?.message || 'Erro ao reservar');
  } finally {
    setIsLoading(false);
  }
};

// ❌ RUIM - Sem tratamento de erro, sem feedback
const reserve = (id) => {
  parkingApi.createReservation({ parking_spot_id: id });
  // E se falhar? Usuário não sabe!
};
```

### Estado Global (Zustand)

```tsx
// ✅ BOM - Store tipada com persistência
interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  setAuth: (user: User, token: string) => void;
  clearAuth: () => void;
}

export const useAuthStore = create<AuthState>((set) => ({
  user: null,
  token: null,
  isAuthenticated: false,
  
  setAuth: (user, token) => {
    localStorage.setItem('auth_token', token);
    set({ user, token, isAuthenticated: true });
  },
  
  clearAuth: () => {
    localStorage.removeItem('auth_token');
    set({ user: null, token: null, isAuthenticated: false });
  },
}));

// ❌ RUIM - Sem tipagem, sem persistência
export const useStore = create((set) => ({
  data: null,
  setData: (d) => set({ data: d }),
}));
```

## 🎨 Styling (Tailwind CSS)

### Classes Customizadas

Sempre usar classes customizadas do `index.css`:

```tsx
// ✅ BOM - Usar classes customizadas
<div className="card">
  <button className="btn-primary">Salvar</button>
  <input className="input-field" />
</div>

// ❌ RUIM - Repetir classes inline
<div className="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
  <button className="px-4 py-3 bg-primary-600 text-white rounded-xl">Salvar</button>
</div>
```

### Paleta de Cores

- **Primary**: `primary-{50-900}` (laranja - cor principal)
- **Gray**: `gray-{50-900}` (neutros)
- **Status**:
  - Success: `green-{100,600,800}`
  - Error: `red-{100,600,800}`
  - Warning: `yellow-{100,600,800}`
  - Info: `blue-{100,600,800}`

### Responsividade

```tsx
// ✅ BOM - Mobile-first com breakpoints
<div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  {/* Cards responsivos */}
</div>

// ❌ RUIM - Tamanhos fixos
<div style={{ width: '300px' }}>
  {/* Não responsivo */}
</div>
```

## 🔒 Boas Práticas de Segurança

### Validação de Inputs

```tsx
// ✅ BOM - Validação no frontend + backend
const validateCPF = (cpf: string): boolean => {
  const cleaned = cpf.replace(/\D/g, '');
  if (cleaned.length !== 11) return false;
  // Validação completa...
  return true;
};

<input
  value={cpf}
  onChange={(e) => {
    const value = e.target.value;
    if (validateCPF(value)) {
      setCpf(value);
    } else {
      setError('CPF inválido');
    }
  }}
/>

// ❌ RUIM - Confiar apenas no backend
<input value={cpf} onChange={(e) => setCpf(e.target.value)} />
```

### Proteção de Rotas

```tsx
// ✅ BOM - HOC para proteção
<ProtectedRoute allowedTypes={['customer']}>
  <CustomerDashboard />
</ProtectedRoute>

// ❌ RUIM - Lógica no componente
function Dashboard() {
  const { user } = useAuthStore();
  if (!user) return <Navigate to="/login" />;
  // ...
}
```

### Sanitização de Dados

```tsx
// ✅ BOM - Sanitizar antes de exibir
import DOMPurify from 'dompurify';

<div dangerouslySetInnerHTML={{ 
  __html: DOMPurify.sanitize(userContent) 
}} />

// ❌ RUIM - XSS vulnerability
<div dangerouslySetInnerHTML={{ __html: userContent }} />
```

## 📝 Nomenclatura

### TypeScript

```typescript
// Interfaces/Types: PascalCase
interface ParkingSpot { }
type AuthResponse = { };

// Funções: camelCase
function handleSubmit() { }
const calculatePrice = () => { };

// Componentes: PascalCase
function CustomerDashboard() { }
export const ParkingSpotCard = () => { };

// Hooks: camelCase com prefixo 'use'
function useAuthStore() { }
const useParkingSpots = () => { };

// Constantes: UPPER_SNAKE_CASE
const API_URL = 'http://localhost:8000';
const MAX_RESERVATIONS = 5;

// Enums: PascalCase
enum ParkingSpotStatus {
  Available = 'available',
  Occupied = 'occupied',
}
```

### Arquivos

```
PascalCase: Componentes React
- CustomerDashboard.tsx
- ParkingSpotCard.tsx

camelCase: Utilities, hooks, services
- authStore.ts
- httpClient.ts
- useParkingSpots.ts

kebab-case: CSS/SCSS
- customer-dashboard.module.css
```

## 🚀 Performance

### Code Splitting

```tsx
// ✅ BOM - Lazy loading de rotas
const CustomerDashboard = lazy(() => import('./pages/CustomerDashboard'));
const OperatorDashboard = lazy(() => import('./pages/OperatorDashboard'));

<Suspense fallback={<LoadingSpinner />}>
  <Routes>
    <Route path="/customer" element={<CustomerDashboard />} />
  </Routes>
</Suspense>
```

### Memoização

```tsx
// ✅ BOM - Memoizar componentes pesados
const ParkingSpotCard = memo(({ spot, onReserve }: Props) => {
  return <div>{/* ... */}</div>;
});

// ✅ BOM - Callbacks memoizados
const handleReserve = useCallback((id: number) => {
  parkingApi.createReservation({ parking_spot_id: id });
}, []);
```

### Listas

```tsx
// ✅ BOM - Key único e estável
{spots.map((spot) => (
  <ParkingSpotCard key={spot.id} spot={spot} />
))}

// ❌ RUIM - Index como key (problemas de reordenação)
{spots.map((spot, idx) => (
  <ParkingSpotCard key={idx} spot={spot} />
))}
```

## 🧪 Testing (Prioridade Futura)

```tsx
// Estrutura de teste esperada
describe('ParkingSpotCard', () => {
  it('should render spot number', () => {
    const spot = { id: 1, number: 'A-01', type: 'car' };
    render(<ParkingSpotCard spot={spot} />);
    expect(screen.getByText('A-01')).toBeInTheDocument();
  });

  it('should call onReserve when button clicked', () => {
    const onReserve = vi.fn();
    const spot = { id: 1, number: 'A-01', type: 'car' };
    render(<ParkingSpotCard spot={spot} onReserve={onReserve} />);
    
    fireEvent.click(screen.getByRole('button', { name: /reservar/i }));
    expect(onReserve).toHaveBeenCalledWith(1);
  });
});
```

## 🔄 Integração com Backend

### Estrutura de Response

Backend retorna:
```json
{
  "data": { /* payload */ },
  "message": "Sucesso",
  "errors": { /* validação */ }
}
```

Frontend deve tratar:
```typescript
// authApi.ts - Retorno direto sem wrapper
async login(credentials: LoginCredentials): Promise<AuthResponse> {
  const response = await httpClient.post<AuthResponse>('/customers/login', credentials);
  return response; // { user, token }
}

// parkingApi.ts - Retorno com data wrapper
async getAvailableSpots(): Promise<ParkingSpot[]> {
  const response = await httpClient.get<ApiResponse<ParkingSpot[]>>('/parking-spots-available');
  return response.data; // Extrair data
}
```

### Tratamento de Erros

```typescript
try {
  await parkingApi.createReservation(data);
} catch (err: any) {
  // Erro de validação (422)
  if (err.response?.status === 422) {
    const errors = err.response.data.errors;
    setFieldErrors(errors);
  }
  // Erro não autorizado (401)
  else if (err.response?.status === 401) {
    clearAuth();
    navigate('/login');
  }
  // Erro genérico
  else {
    setError(err.response?.data?.message || 'Erro desconhecido');
  }
}
```

## 📋 Checklist de PR/Commit

Antes de commitar, verificar:
- [ ] Tipagem TypeScript completa (sem `any`)
- [ ] Componentes na camada correta da Clean Architecture
- [ ] Classes Tailwind customizadas (não inline repetidas)
- [ ] Try/catch em chamadas de API
- [ ] Loading states implementados
- [ ] Feedback visual de sucesso/erro
- [ ] Responsividade mobile testada
- [ ] Sem console.log esquecidos
- [ ] Keys únicas em listas
- [ ] Props tipadas em componentes
- [ ] Hooks com dependências corretas

## 🎓 Diferenciais

- **Acessibilidade**: aria-labels, roles, keyboard navigation
- **SEO**: meta tags, helmet
- **PWA**: Service worker, offline mode
- **Internacionalização**: i18n preparado
- **Analytics**: Tracking de eventos
- **Error Boundary**: Captura de erros React
- **Optimistic Updates**: UX responsiva

## 💡 Dicas para o Copilot

Quando gerar código para este projeto:
1. **Sempre** seguir Clean Architecture
2. **Sempre** tipar com TypeScript
3. **Sempre** tratar erros de API
4. **Sempre** adicionar loading states
5. **Sempre** usar classes Tailwind customizadas
6. Componentes pequenos e reutilizáveis
7. Hooks customizados para lógica complexa
8. Memoização para performance
9. Feedback visual para usuário
10. Mobile-first responsive design

---

**Padrão de Qualidade**: Código deve ser production-ready, escalável e mantível.

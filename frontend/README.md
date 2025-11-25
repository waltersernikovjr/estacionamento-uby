# Frontend - Estacionamento Uby

## 🎯 Visão Geral

Aplicação React com TypeScript seguindo Clean Architecture para o sistema de gerenciamento de estacionamento.

## 📋 Stack Tecnológica

- **React 19.2.0** - Biblioteca UI
- **TypeScript 5.9.3** - Tipagem estática
- **Vite 7.2.4** - Build tool e dev server
- **Tailwind CSS 3.4.17** - Framework CSS utility-first
- **React Router DOM** - Roteamento SPA
- **Axios 1.6.8** - Cliente HTTP
- **Zustand** - Gerenciamento de estado
- **Socket.io Client 4.7.2** - WebSocket para chat em tempo real

## 🏗️ Arquitetura Clean Architecture

```
src/
├── domain/                    # Camada de Domínio
│   ├── types/                # Entidades e tipos do domínio
│   │   ├── types.ts         # User, Customer, Operator, ParkingSpot, etc
│   │   └── index.ts         # Barrel export
│   └── contracts/           # Interfaces e contratos (futuro)
│
├── application/              # Camada de Aplicação
│   ├── stores/              # Estado global (Zustand)
│   │   └── authStore.ts    # Gerenciamento de autenticação
│   ├── hooks/               # React Hooks customizados (futuro)
│   └── services/            # Lógica de negócio (futuro)
│
├── infrastructure/           # Camada de Infraestrutura
│   ├── api/                 # Clientes HTTP
│   │   ├── httpClient.ts   # Axios configurado com interceptors
│   │   ├── authApi.ts      # Endpoints de autenticação
│   │   ├── parkingApi.ts   # Endpoints de vagas/reservas
│   │   └── vehicleApi.ts   # Endpoints de veículos
│   └── websocket/           # Cliente Socket.io (futuro)
│
└── presentation/             # Camada de Apresentação
    ├── components/          # Componentes reutilizáveis
    │   ├── common/         # Componentes genéricos
    │   │   └── ProtectedRoute.tsx
    │   └── parking/        # Componentes de negócio
    │       ├── ParkingSpotCard.tsx
    │       └── ReservationCard.tsx
    └── pages/              # Páginas completas
        ├── LoginPage.tsx
        ├── RegisterPage.tsx
        ├── CustomerDashboard.tsx
        └── OperatorDashboard.tsx (futuro)
```

## 🔧 Setup e Instalação

### Pré-requisitos
- Node.js 18+ 
- npm ou yarn
- Backend rodando em `http://localhost:8000`

### Instalação

```bash
# 1. Instalar dependências
cd frontend
npm install

# 2. Configurar variáveis de ambiente
cp .env.example .env

# 3. Rodar em desenvolvimento
npm run dev

# 4. Build para produção
npm run build

# 5. Preview da build
npm run preview
```

### Variáveis de Ambiente

```env
# .env
VITE_API_URL=http://localhost:8000/api/v1
VITE_WS_URL=http://localhost:3001
```

## 📡 Integração com Backend

### HTTP Client (`httpClient.ts`)

Cliente Axios configurado com:
- **Base URL**: `VITE_API_URL` do .env
- **Request Interceptor**: Adiciona token Bearer automaticamente
- **Response Interceptor**: Redireciona para login em 401

```typescript
// Uso
const response = await httpClient.get<T>('/endpoint');
const data = await httpClient.post<T>('/endpoint', payload);
```

### APIs Disponíveis

#### **authApi.ts** - Autenticação
```typescript
authApi.login({ email, password })          // Login cliente
authApi.operatorLogin({ registration_number, password }) // Login operador
authApi.register(data)                      // Registro cliente
authApi.logout()                            // Logout
authApi.me()                                // Dados do usuário autenticado
authApi.validateCep(cep)                    // Validar CEP via ViaCEP
```

#### **parkingApi.ts** - Vagas e Reservas
```typescript
parkingApi.getAvailableSpots()              // Listar vagas disponíveis
parkingApi.getSpotById(id)                  // Buscar vaga específica
parkingApi.createReservation(data)          // Criar reserva
parkingApi.getMyReservations()              // Minhas reservas
parkingApi.cancelReservation(id)            // Cancelar reserva
parkingApi.checkoutReservation(id)          // Finalizar reserva
parkingApi.calculatePrice(spotId, hours)    // Calcular preço
```

#### **vehicleApi.ts** - Veículos
```typescript
vehicleApi.getMyVehicles()                  // Listar meus veículos
vehicleApi.getVehicleById(id)               // Buscar veículo
vehicleApi.createVehicle(data)              // Cadastrar veículo
vehicleApi.updateVehicle(id, data)          // Atualizar veículo
vehicleApi.deleteVehicle(id)                // Deletar veículo
```

### Tratamento de Erros

```typescript
try {
  const data = await parkingApi.getAvailableSpots();
} catch (err: any) {
  // err.response.status - Código HTTP
  // err.response.data.message - Mensagem de erro
  // err.response.data.errors - Erros de validação
}
```

## 🎨 Sistema de Design

### Tailwind CSS Classes Customizadas

```css
/* src/index.css */

.card {
  /* Card padrão com sombra e borda */
  @apply bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6;
}

.btn-primary {
  /* Botão primário laranja */
  @apply w-full px-4 py-3 bg-primary-600 text-white rounded-xl 
         font-semibold hover:bg-primary-700 transition-colors 
         disabled:bg-gray-300 disabled:cursor-not-allowed;
}

.input-field {
  /* Input padrão */
  @apply w-full px-4 py-3 border-2 border-gray-200 rounded-xl 
         focus:outline-none focus:border-primary-500 focus:ring-2 
         focus:ring-primary-100 transition-colors;
}
```

### Paleta de Cores

```javascript
// tailwind.config.js
colors: {
  primary: {
    50: '#fff7ed',
    100: '#ffedd5',
    200: '#fed7aa',
    // ... até 900
    600: '#ea580c', // Cor principal (laranja)
    700: '#c2410c',
  }
}
```

## 🔐 Autenticação e Autorização

### Auth Store (Zustand)

```typescript
// useAuthStore
{
  user: User | null,           // Dados do usuário
  token: string | null,        // Token JWT
  isAuthenticated: boolean,    // Status de autenticação
  setAuth(user, token),        // Salvar autenticação
  clearAuth(),                 // Limpar autenticação
  loadFromStorage()            // Carregar do localStorage
}
```

### Protected Routes

```tsx
<ProtectedRoute allowedTypes={['customer']}>
  <CustomerDashboard />
</ProtectedRoute>
```

### Persistência

- Token armazenado em `localStorage.auth_token`
- Dados do usuário em `localStorage.user` (JSON)
- Carregamento automático no mount do App

## 📄 Páginas Implementadas

### ✅ LoginPage
- Login de clientes e operadores
- Validação de formulário
- Feedback de erros
- Redirecionamento baseado em tipo de usuário

### ✅ RegisterPage
- Cadastro de novos clientes
- Validação de CPF, RG, telefone
- Integração com ViaCEP para endereço
- Confirmação de senha

### ✅ CustomerDashboard
- **Stats Cards**: Vagas disponíveis, reservas ativas, veículos cadastrados
- **Tabs**: Vagas, Reservas, Veículos
- **Funcionalidades**:
  - Listar vagas disponíveis com filtros
  - Criar reservas (com validação de veículo)
  - Ver histórico de reservas
  - Cancelar/Finalizar reservas
  - Gerenciar veículos

### 🚧 OperatorDashboard (em desenvolvimento)
- CRUD de vagas
- Gestão de reservas
- Chat com clientes
- Relatórios

## 🧩 Componentes Principais

### ParkingSpotCard
Exibe informações de uma vaga de estacionamento:
- Número da vaga
- Tipo (carro, moto, caminhão)
- Status (disponível, ocupado, etc)
- Preço por hora
- Dimensões
- Botão de reserva

### ReservationCard
Exibe detalhes de uma reserva:
- ID da reserva
- Vaga reservada
- Horários (entrada/saída)
- Status (pendente, ativa, finalizada)
- Valor total
- Ações (cancelar, finalizar)

### ProtectedRoute
HOC para proteger rotas por tipo de usuário:
- Valida autenticação
- Verifica tipo de usuário permitido
- Redireciona não autorizados

## 🚀 Próximos Passos

### Funcionalidades Prioritárias

1. **Cadastro de Veículos** (CustomerDashboard)
   - Modal de criação
   - Formulário com validação
   - Lista de veículos com edição/exclusão

2. **Operator Dashboard**
   - Layout completo
   - CRUD de vagas
   - Listagem de reservas
   - Filtros e busca

3. **Chat em Tempo Real**
   - Integração Socket.io
   - Componente ChatBox
   - Notificações de mensagens
   - Histórico de conversas

4. **Sistema de Pagamentos**
   - Integração com gateway
   - Tela de checkout
   - Histórico de pagamentos
   - Emissão de recibos

5. **Notificações Push**
   - Toast notifications
   - Alertas em tempo real
   - Confirmações de ações

### Melhorias de UX

- [ ] Loading states em todas operações
- [ ] Skeleton loaders
- [ ] Animações de transição (Framer Motion)
- [ ] Confirmação de ações destrutivas
- [ ] Feedback visual (toast, snackbar)
- [ ] Modo escuro
- [ ] Responsividade mobile aprimorada

### Otimizações

- [ ] React Query para cache de dados
- [ ] Lazy loading de rotas
- [ ] Code splitting
- [ ] Service Worker / PWA
- [ ] Otimização de bundle

## 📝 Convenções de Código

### Nomenclatura

- **Componentes**: PascalCase (`CustomerDashboard.tsx`)
- **Funções**: camelCase (`handleSubmit`)
- **Constantes**: UPPER_SNAKE_CASE (`API_URL`)
- **Types/Interfaces**: PascalCase (`ParkingSpot`, `ApiResponse`)
- **Hooks**: camelCase com prefixo `use` (`useAuthStore`)

### Organização de Imports

```typescript
// 1. React
import { useState, useEffect } from 'react';

// 2. Bibliotecas externas
import { useNavigate } from 'react-router-dom';

// 3. Stores/Hooks internos
import { useAuthStore } from '../../application/stores/authStore';

// 4. APIs
import { parkingApi } from '../../infrastructure/api/parkingApi';

// 5. Componentes
import { ParkingSpotCard } from '../components/parking/ParkingSpotCard';

// 6. Tipos
import type { ParkingSpot, Reservation } from '../../domain/types';
```

### TypeScript

- Sempre tipar props de componentes
- Usar `type` para objetos complexos
- Usar `interface` para contratos/extensão
- Evitar `any` - usar `unknown` quando necessário
- Tipar retornos de funções async

```typescript
interface Props {
  spot: ParkingSpot;
  onReserve: (id: number) => void;
  isLoading?: boolean;
}

export function ParkingSpotCard({ spot, onReserve, isLoading = false }: Props) {
  // ...
}
```

## 🧪 Testing (futuro)

```bash
# Testes unitários (Vitest)
npm run test

# Testes E2E (Playwright)
npm run test:e2e

# Coverage
npm run test:coverage
```

## 📦 Build e Deploy

```bash
# Build de produção
npm run build
# Gera pasta dist/ otimizada

# Preview local da build
npm run preview

# Análise de bundle
npm run build -- --mode analyze
```

### Deploy

- **Vercel**: `vercel --prod`
- **Netlify**: Conectar repositório
- **Docker**: Build multi-stage (futuro)

## 🐛 Troubleshooting

### Erro: API retorna 404
- Verificar se backend está rodando
- Conferir `VITE_API_URL` no .env
- Checar rotas no Laravel (`php artisan route:list`)

### Erro: CORS
- Configurar CORS no Laravel (`config/cors.php`)
- Verificar domínio permitido

### Erro: Token inválido
- Limpar localStorage
- Fazer logout/login novamente

### Build falha
- Deletar `node_modules` e reinstalar
- Limpar cache do Vite: `rm -rf node_modules/.vite`

## 📚 Recursos

- [React Docs](https://react.dev)
- [TypeScript Handbook](https://www.typescriptlang.org/docs/)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Vite Guide](https://vitejs.dev/guide/)
- [React Router](https://reactrouter.com/en/main)
- [Zustand](https://github.com/pmndrs/zustand)

---

**Última atualização**: 20/11/2025
**Status**: 🚧 Em desenvolvimento ativo
**Cobertura**: ~45% das funcionalidades implementadas

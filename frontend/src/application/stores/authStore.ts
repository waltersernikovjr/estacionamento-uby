import { create } from 'zustand';
import type { User } from '../../domain/types/index';

interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  setAuth: (user: User, token: string) => void;
  clearAuth: () => void;
  loadFromStorage: () => void;
}

const loadStoredAuth = () => {
  try {
    const token = localStorage.getItem('auth_token');
    const userStr = localStorage.getItem('user');
    
    if (token && userStr) {
      const user = JSON.parse(userStr) as User;
      return { user, token, isAuthenticated: true };
    }
  } catch (error) {
    console.error('Erro ao carregar autenticação:', error);
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
  }
  
  return { user: null, token: null, isAuthenticated: false };
};

export const useAuthStore = create<AuthState>((set) => ({
  ...loadStoredAuth(),

  setAuth: (user, token) => {
    localStorage.setItem('auth_token', token);
    localStorage.setItem('user', JSON.stringify(user));
    set({ user, token, isAuthenticated: true });
  },

  clearAuth: () => {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    set({ user: null, token: null, isAuthenticated: false });
  },

  loadFromStorage: () => {
    const storedAuth = loadStoredAuth();
    set(storedAuth);
  },
}));

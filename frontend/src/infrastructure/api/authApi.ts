import { httpClient } from './httpClient';
import type { AuthResponse, ApiResponse, Customer, Operator } from '../../domain/types';

interface LoginCredentials {
  email: string;
  password: string;
}

interface RegisterData {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  cpf: string;
  phone: string;
  street: string;
  number: string;
  complement?: string;
  neighborhood: string;
  city: string;
  state: string;
  zip_code: string;
}

interface OperatorLoginCredentials {
  email: string;
  password: string;
}

export const authApi = {
  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    const response = await httpClient.post<AuthResponse>('/customers/login', credentials);
    return response.data;
  },

  async operatorLogin(credentials: OperatorLoginCredentials): Promise<AuthResponse> {
    const response = await httpClient.post<AuthResponse>('/operators/login', credentials);
    return response.data;
  },

  async register(data: RegisterData): Promise<AuthResponse> {
    const response = await httpClient.post<AuthResponse>('/customers/register', data);
    return response.data;
  },

  async logout(): Promise<void> {
    await httpClient.post('/customers/logout');
  },

  async me(): Promise<Customer | Operator> {
    const response = await httpClient.get<ApiResponse<Customer | Operator>>('/customers/me');
    return response.data.data;
  },

  async validateCep(cep: string): Promise<{
    street: string;
    neighborhood: string;
    city: string;
    state: string;
  }> {
    const cleanCep = cep.replace(/\D/g, '');
    const response = await fetch(`https://viacep.com.br/ws/${cleanCep}/json/`);
    const data = await response.json();
    
    if (data.erro) {
      throw new Error('CEP não encontrado');
    }

    return {
      street: data.logradouro,
      neighborhood: data.bairro,
      city: data.localidade,
      state: data.uf,
    };
  },
};

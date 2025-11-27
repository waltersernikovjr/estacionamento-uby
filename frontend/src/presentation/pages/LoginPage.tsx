import { useState, type FormEvent } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuthStore } from '../../application/stores/authStore';
import { authApi } from '../../infrastructure/api/authApi';

export function LoginPage() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState('');
  const navigate = useNavigate();
  
  const setAuth = useAuthStore((state) => state.setAuth);

  const handleSubmit = async (e: FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    setError('');
    setIsLoading(true);

    try {
      let user, token;
      let lastError: any = null;
      
      try {
        const customerResponse = await authApi.login({ email, password });
        user = customerResponse.user;
        token = customerResponse.token;
      } catch (customerError: any) {
        lastError = customerError;
        
        try {
          const operatorResponse = await authApi.operatorLogin({ email, password });
          user = operatorResponse.user;
          token = operatorResponse.token;
        } catch (operatorError: any) {
          const backendMessage = customerError?.response?.data?.message || 
                                customerError?.response?.data?.errors?.email?.[0];
          
          if (backendMessage) {
            throw new Error(backendMessage);
          }
          
          throw new Error('Email ou senha inválidos');
        }
      }

      setAuth(user, token);
      
      if (user.type === 'customer') {
        navigate('/customer/dashboard', { replace: true });
      } else if (user.type === 'operator') {
        navigate('/operator/dashboard', { replace: true });
      } else {
        throw new Error('Tipo de usuário desconhecido');
      }
    } catch (err: any) {
      const errorMessage = err.message || 'Erro ao fazer login';
      setError(errorMessage);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-500 to-primary-700 px-4">
      <div className="w-full max-w-md">
        <div className="card">
          <div className="text-center mb-8">
            <h1 className="text-3xl font-bold text-gray-900 mb-2">
              Estacionamento Uby
            </h1>
            <p className="text-gray-600">Entre com sua conta</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-4">
            {error && (
              <div className="bg-red-50 border-2 border-red-200 text-red-700 px-4 py-3 rounded-xl">
                <p>{error}</p>
                {error.toLowerCase().includes('verifique seu email') && (
                  <a 
                    href={`/verify-email?email=${encodeURIComponent(email)}`}
                    className="text-primary-600 hover:text-primary-700 font-medium underline block mt-2"
                  >
                    Reenviar email de verificação
                  </a>
                )}
              </div>
            )}

            <div>
              <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-2">
                Email
              </label>
              <input
                id="email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="input-field"
                placeholder="seu@email.com"
                required
              />
            </div>

            <div>
              <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-2">
                Senha
              </label>
              <input
                id="password"
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                className="input-field"
                placeholder="••••••••"
                required
              />
            </div>

            <button
              type="submit"
              className="btn-primary w-full"
              disabled={isLoading}
            >
              {isLoading ? 'Entrando...' : 'Entrar'}
            </button>

            <div className="text-center space-y-2 pt-4">
              <p className="text-sm text-gray-600">
                Não tem uma conta?{' '}
                <a href="/register" className="text-primary-600 hover:text-primary-700 font-medium">
                  Cadastre-se
                </a>
              </p>
              <p className="text-sm text-gray-600">
                É operador?{' '}
                <a href="/operator/login" className="text-primary-600 hover:text-primary-700 font-medium">
                  Clique aqui
                </a>
              </p>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}


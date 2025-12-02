import { useState, type FormEvent } from 'react';
import { useAuthStore } from '../../application/stores/authStore';
import { authApi } from '../../infrastructure/api/authApi';

export function RegisterPage() {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    cpf: '',
    phone: '',
    zip_code: '',
    street: '',
    number: '',
    complement: '',
    neighborhood: '',
    city: '',
    state: '',
  });
  
  const [isLoading, setIsLoading] = useState(false);
  const [isLoadingCep, setIsLoadingCep] = useState(false);
  const [error, setError] = useState('');
  const [successMessage, setSuccessMessage] = useState('');
  
  const setAuth = useAuthStore((state) => state.setAuth);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleCpfChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value.replace(/\D/g, '');
    if (value.length <= 11) {
      setFormData({ ...formData, cpf: value });
    }
  };

  const handlePhoneChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value.replace(/\D/g, '');
    if (value.length <= 11) {
      setFormData({ ...formData, phone: value });
    }
  };

  const handleCepChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value.replace(/\D/g, '');
    if (value.length <= 8) {
      setFormData({ ...formData, zip_code: value });
    }
  };

  const handleCepBlur = async () => {
    if (formData.zip_code.length === 8 || formData.zip_code.length === 9) {
      setIsLoadingCep(true);
      try {
        const address = await authApi.validateCep(formData.zip_code);
        setFormData({
          ...formData,
          street: address.street,
          neighborhood: address.neighborhood,
          city: address.city,
          state: address.state,
        });
      } catch (err) {
        setError('CEP não encontrado');
      } finally {
        setIsLoadingCep(false);
      }
    }
  };

  const handleSubmit = async (e: FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    setError('');
    setSuccessMessage('');

    if (formData.password !== formData.password_confirmation) {
      setError('As senhas não coincidem');
      return;
    }

    setIsLoading(true);

    try {
      const response = await authApi.register(formData);
      
      if (response.requires_verification) {
        setSuccessMessage(response.message || 'Cadastro realizado com sucesso! Verifique seu email.');
        setTimeout(() => {
          window.location.href = `/verify-email?email=${encodeURIComponent(formData.email)}`;
        }, 2000);
      } else {
        const { user, token } = response;
        if (user && token) {
          setAuth(user, token);
          window.location.href = '/customer/dashboard';
        }
      }
    } catch (err: any) {
      let errorMessage = 'Erro ao cadastrar';
      
      if (err.response?.data?.errors) {
        const errors = err.response.data.errors;
        const firstError = Object.values(errors)[0];
        errorMessage = Array.isArray(firstError) ? firstError[0] : String(firstError);
      } else if (err.response?.data?.message) {
        errorMessage = err.response.data.message;
      } else if (err.message) {
        errorMessage = err.message;
      }
      
      setError(errorMessage);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-primary-500 to-primary-700 py-12 px-4">
      <div className="max-w-2xl mx-auto">
        <div className="card">
          <div className="text-center mb-8">
            <h1 className="text-3xl font-bold text-gray-900 mb-2">
              Cadastro de Cliente
            </h1>
            <p className="text-gray-600">Preencha seus dados para criar uma conta</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-6">
            {error && (
              <div className="bg-red-50 border-2 border-red-200 text-red-700 px-4 py-3 rounded-xl">
                {error}
              </div>
            )}

            {successMessage && (
              <div className="bg-green-50 border-2 border-green-200 text-green-700 px-4 py-3 rounded-xl">
                <div className="flex items-center gap-2">
                  <svg className="h-5 w-5 text-green-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>{successMessage}</span>
                </div>
              </div>
            )}

            <div className="space-y-4">
              <h2 className="text-lg font-semibold text-gray-800">Dados Pessoais</h2>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Nome Completo
                </label>
                <input
                  type="text"
                  name="name"
                  value={formData.name}
                  onChange={handleChange}
                  className="input-field"
                  required
                />
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    CPF <span className="text-xs text-gray-500">(apenas números)</span>
                  </label>
                  <input
                    type="text"
                    name="cpf"
                    value={formData.cpf}
                    onChange={handleCpfChange}
                    className="input-field"
                    placeholder="12345678901"
                    maxLength={11}
                    required
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Telefone <span className="text-xs text-gray-500">(apenas números)</span>
                  </label>
                  <input
                    type="tel"
                    name="phone"
                    value={formData.phone}
                    onChange={handlePhoneChange}
                    className="input-field"
                    placeholder="11999999999"
                    maxLength={11}
                    required
                  />
                </div>
              </div>
            </div>

            {/* Dados de Acesso */}
            <div className="space-y-4">
              <h2 className="text-lg font-semibold text-gray-800">Dados de Acesso</h2>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  E-mail
                </label>
                <input
                  type="email"
                  name="email"
                  value={formData.email}
                  onChange={handleChange}
                  className="input-field"
                  required
                />
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Senha
                  </label>
                  <input
                    type="password"
                    name="password"
                    value={formData.password}
                    onChange={handleChange}
                    className="input-field"
                    required
                    minLength={8}
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Confirmar Senha
                  </label>
                  <input
                    type="password"
                    name="password_confirmation"
                    value={formData.password_confirmation}
                    onChange={handleChange}
                    className="input-field"
                    required
                  />
                </div>
              </div>
            </div>

            {/* Endereço */}
            <div className="space-y-4">
              <h2 className="text-lg font-semibold text-gray-800">Endereço</h2>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  CEP <span className="text-xs text-gray-500">(apenas números)</span>
                </label>
                <input
                  type="text"
                  name="zip_code"
                  value={formData.zip_code}
                  onChange={handleCepChange}
                  onBlur={handleCepBlur}
                  className="input-field"
                  placeholder="00000000"
                  maxLength={8}
                  required
                />
                {isLoadingCep && <p className="text-sm text-gray-500 mt-1">Buscando CEP...</p>}
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Rua
                </label>
                <input
                  type="text"
                  name="street"
                  value={formData.street}
                  onChange={handleChange}
                  className="input-field"
                  required
                />
              </div>

              <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Número
                  </label>
                  <input
                    type="text"
                    name="number"
                    value={formData.number}
                    onChange={handleChange}
                    className="input-field"
                    required
                  />
                </div>

                <div className="col-span-2">
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Complemento
                  </label>
                  <input
                    type="text"
                    name="complement"
                    value={formData.complement}
                    onChange={handleChange}
                    className="input-field"
                  />
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Bairro
                </label>
                <input
                  type="text"
                  name="neighborhood"
                  value={formData.neighborhood}
                  onChange={handleChange}
                  className="input-field"
                  required
                />
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Cidade
                  </label>
                  <input
                    type="text"
                    name="city"
                    value={formData.city}
                    onChange={handleChange}
                    className="input-field"
                    required
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Estado
                  </label>
                  <input
                    type="text"
                    name="state"
                    value={formData.state}
                    onChange={handleChange}
                    className="input-field"
                    maxLength={2}
                    required
                  />
                </div>
              </div>
            </div>

            <button
              type="submit"
              className="btn-primary w-full"
              disabled={isLoading}
            >
              {isLoading ? 'Cadastrando...' : 'Cadastrar'}
            </button>

            <div className="text-center pt-4">
              <p className="text-sm text-gray-600">
                Já tem uma conta?{' '}
                <a href="/login" className="text-primary-600 hover:text-primary-700 font-medium">
                  Faça login
                </a>
              </p>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}

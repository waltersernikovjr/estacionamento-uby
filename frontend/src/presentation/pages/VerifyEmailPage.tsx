import { useEffect, useState } from 'react';
import { useSearchParams, Link } from 'react-router-dom';

export function VerifyEmailPage() {
  const [searchParams] = useSearchParams();
  const email = searchParams.get('email') || '';
  const [isResending, setIsResending] = useState(false);
  const [resendSuccess, setResendSuccess] = useState(false);
  const [resendError, setResendError] = useState('');

  useEffect(() => {
    document.title = 'Verificar Email - Estacionamento Uby';
  }, []);

  const handleResendEmail = async () => {
    setIsResending(true);
    setResendError('');
    setResendSuccess(false);

    try {
      const response = await fetch('http://localhost:8000/api/v1/email/resend', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ 
          email,
          type: 'customer'
        }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Erro ao reenviar email');
      }

      setResendSuccess(true);
      setTimeout(() => setResendSuccess(false), 5000);
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Não foi possível reenviar o email. Tente novamente.';
      setResendError(errorMessage);
    } finally {
      setIsResending(false);
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center py-12 px-4">
      <div className="max-w-md w-full">
        <div className="card text-center">
          <div className="flex justify-center mb-6">
            <div className="bg-primary-100 rounded-full p-4">
              <svg className="h-16 w-16 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
          </div>

          <h1 className="text-3xl font-bold text-gray-900 mb-4">
            Verifique seu email
          </h1>

          <p className="text-gray-600 mb-2">
            Enviamos um link de verificação para:
          </p>
          
          <p className="text-lg font-semibold text-primary-600 mb-6">
            {email}
          </p>

          <div className="bg-blue-50 border-2 border-blue-200 rounded-xl p-4 mb-6">
            <p className="text-sm text-blue-800">
              <strong>Importante:</strong> Clique no link enviado para o seu email para ativar sua conta e fazer login.
            </p>
          </div>

          {resendSuccess && (
            <div className="bg-green-50 border-2 border-green-200 rounded-xl p-4 mb-4 flex items-center gap-2">
              <svg className="h-5 w-5 text-green-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <p className="text-sm text-green-800">
                Email reenviado com sucesso!
              </p>
            </div>
          )}

          {resendError && (
            <div className="bg-red-50 border-2 border-red-200 rounded-xl p-4 mb-4">
              <p className="text-sm text-red-800">{resendError}</p>
            </div>
          )}

          <div className="space-y-4">
            <button
              type="button"
              onClick={handleResendEmail}
              disabled={isResending}
              className="btn-secondary w-full disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {isResending ? 'Reenviando...' : 'Reenviar email de verificação'}
            </button>

            <Link
              to="/login"
              className="block text-primary-600 hover:text-primary-700 font-medium transition-colors"
            >
              Voltar para o login
            </Link>
          </div>

          <div className="mt-8 pt-6 border-t border-gray-200">
            <p className="text-sm text-gray-600">
              Não recebeu o email?
            </p>
            <ul className="text-xs text-gray-500 mt-2 space-y-1">
              <li>• Verifique sua caixa de spam ou lixo eletrônico</li>
              <li>• Aguarde alguns minutos</li>
              <li>• Certifique-se de que o email está correto</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  );
}

import { useEffect } from 'react';
import { Navigate } from 'react-router-dom';
import { useAuthStore } from '../../../application/stores/authStore';
import type { User } from '../../../domain/types';

interface ProtectedRouteProps {
  children: React.ReactNode;
  allowedTypes?: User['type'][];
}

export function ProtectedRoute({ children, allowedTypes }: ProtectedRouteProps) {
  const { isAuthenticated, user, loadFromStorage } = useAuthStore();

  useEffect(() => {
    loadFromStorage();
  }, [loadFromStorage]);

  // Se não autenticado ou sem usuário, redireciona
  if (!isAuthenticated || !user) {
    return <Navigate to="/login" replace />;
  }

  // Se tipo de usuário não permitido, redireciona
  if (allowedTypes && !allowedTypes.includes(user.type)) {
    return <Navigate to="/login" replace />;
  }

  return <>{children}</>;
}

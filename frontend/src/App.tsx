import { useEffect } from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { useAuthStore } from './application/stores/authStore';
import { LoginPage } from './presentation/pages/LoginPage';
import { RegisterPage } from './presentation/pages/RegisterPage';
import { VerifyEmailPage } from './presentation/pages/VerifyEmailPage';
import { CustomerDashboard } from './presentation/pages/CustomerDashboard';
import { OperatorDashboard } from './presentation/pages/OperatorDashboard';
import { ProtectedRoute } from './presentation/components/common/ProtectedRoute';

function App() {
  const { loadFromStorage } = useAuthStore();

  useEffect(() => {
    loadFromStorage();
  }, [loadFromStorage]);

  return (
    <BrowserRouter>
      <Routes>
        <Route path="/login" element={<LoginPage />} />
        <Route path="/register" element={<RegisterPage />} />
        <Route path="/verify-email" element={<VerifyEmailPage />} />
        <Route
          path="/customer/dashboard"
          element={
            <ProtectedRoute allowedTypes={['customer']}>
              <CustomerDashboard />
            </ProtectedRoute>
          }
        />
        <Route
          path="/operator/dashboard"
          element={
            <ProtectedRoute allowedTypes={['operator']}>
              <OperatorDashboard />
            </ProtectedRoute>
          }
        />
        <Route path="/" element={<Navigate to="/login" replace />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App;

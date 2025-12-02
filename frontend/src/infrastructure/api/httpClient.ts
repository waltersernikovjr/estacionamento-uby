import axios from 'axios';

const httpClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true,
  timeout: 15000,
});

httpClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

httpClient.interceptors.response.use(
  (response) => response,
  async (error) => {
    const config = error.config;

    if (
      error.code === 'ECONNREFUSED' || 
      error.code === 'ERR_NETWORK' ||
      error.code === 'ETIMEDOUT' ||
      error.message?.includes('Network Error') ||
      error.message?.includes('timeout')
    ) {
      config._retryCount = config._retryCount || 0;
      
      if (config._retryCount < 5) {
        config._retryCount += 1;
        const delay = config._retryCount * 1000;
        console.log(`[HTTP Client] Tentativa ${config._retryCount}/5 após ${delay}ms...`);
        
        await new Promise(resolve => setTimeout(resolve, delay));
        
        return httpClient(config);
      }
      
      console.error('[HTTP Client] Falha após 5 tentativas. Backend pode estar offline.');
    }

    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token');
      window.location.href = '/login';
    }

    if (error.response?.status === 422 && error.response?.data?.errors) {
      const validationErrors = error.response.data.errors;
      const firstErrorField = Object.keys(validationErrors)[0];
      const firstErrorMessage = validationErrors[firstErrorField][0];
      
      error.message = firstErrorMessage;
      error.validationErrors = validationErrors;
      
      return Promise.reject(error);
    }

    if (error.response?.data?.message) {
      error.message = error.response.data.message;
      return Promise.reject(error);
    }
    
    return Promise.reject(error);
  }
);

export { httpClient };

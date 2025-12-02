import dotenv from 'dotenv';

dotenv.config();

export const config = {
  env: process.env.NODE_ENV || 'development',
  port: parseInt(process.env.PORT || '3001', 10),
  host: process.env.HOST || '0.0.0.0',
  
  jwt: {
    secret: process.env.JWT_SECRET || 'your-jwt-secret-key',
  },
  
  database: {
    host: process.env.DB_HOST || 'mysql',
    port: parseInt(process.env.DB_PORT || '3306', 10),
    user: process.env.DB_USER || 'laravel',
    password: process.env.DB_PASSWORD || 'secret',
    database: process.env.DB_NAME || 'estacionamento_uby',
  },
  
  cors: {
    origin: process.env.CORS_ORIGIN?.split(',') || ['http://localhost:3000', 'http://localhost:8000', 'null'],
    credentials: true,
  },
  
  logging: {
    level: process.env.LOG_LEVEL || 'info',
  },
};

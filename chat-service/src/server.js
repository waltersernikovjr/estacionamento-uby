import express from 'express';
import { createServer } from 'http';
import { Server } from 'socket.io';
import cors from 'cors';
import { config } from './config/index.js';
import { testConnection } from './config/database.js';
import { authenticateSocket } from './middleware/auth.js';
import { setupSocketEvents } from './events/socketEvents.js';

const app = express();
const httpServer = createServer(app);

app.use(cors(config.cors));
app.use(express.json());

app.get('/health', (req, res) => {
  res.json({ 
    status: 'ok', 
    service: 'chat-service',
    timestamp: new Date().toISOString(),
  });
});

const io = new Server(httpServer, {
  cors: config.cors,
  transports: ['websocket', 'polling'],
});

io.use(authenticateSocket);

setupSocketEvents(io);

const startServer = async () => {
  try {
    const dbConnected = await testConnection();
    
    if (!dbConnected) {
      console.error('❌ Failed to connect to database. Exiting...');
      process.exit(1);
    }

    httpServer.listen(config.port, config.host, () => {
      console.log('🚀 Chat Service started');
      console.log(`📡 Server running on http://${config.host}:${config.port}`);
      console.log(`🔌 WebSocket ready for connections`);
      console.log(`🌍 Environment: ${config.env}`);
    });
  } catch (error) {
    console.error('❌ Failed to start server:', error);
    process.exit(1);
  }
};

startServer();

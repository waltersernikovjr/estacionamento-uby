import { ChatSessionModel } from '../models/ChatSession.js';
import { ChatMessageModel } from '../models/ChatMessage.js';

export const setupSocketEvents = (io) => {
  io.on('connection', (socket) => {
    const user = socket.user;
    const userType = user.type || 'customer';
    const userId = user.sub || user.id;

    socket.on('join-session', async ({ sessionId }) => {
      try {
        const session = await ChatSessionModel.findById(sessionId);
        
        if (!session) {
          socket.emit('error', { message: 'Session not found' });
          return;
        }

        if (userType === 'customer' && session.customer_id !== userId) {
          socket.emit('error', { message: 'Unauthorized access to session' });
          return;
        }

        if (userType === 'operator' && session.operator_id !== userId) {
          socket.emit('error', { message: 'Unauthorized access to session' });
          return;
        }

        socket.join(`session-${sessionId}`);
        socket.currentSession = sessionId;

        const messages = await ChatMessageModel.findBySessionId(sessionId);
        
        socket.emit('session-joined', {
          sessionId,
          messages,
        });
      } catch (error) {
        console.error('Error joining session:', error);
        socket.emit('error', { message: 'Failed to join session' });
      }
    });

    socket.on('send-message', async ({ sessionId, message }) => {
      try {
        if (!sessionId || !message?.trim()) {
          socket.emit('error', { message: 'Invalid message data' });
          return;
        }

        const session = await ChatSessionModel.findById(sessionId);
        
        if (!session || session.status !== 'active') {
          socket.emit('error', { message: 'Session is not active' });
          return;
        }

        const messageId = await ChatMessageModel.create(
          sessionId,
          userType,
          userId,
          message.trim()
        );

        const messageData = {
          id: messageId,
          sessionId,
          senderType: userType,
          senderId: userId,
          message: message.trim(),
          createdAt: new Date().toISOString(),
        };

        io.to(`session-${sessionId}`).emit('new-message', messageData);
        
        io.emit('session-updated', { sessionId });
      } catch (error) {
        console.error('Error sending message:', error);
        socket.emit('error', { message: 'Failed to send message' });
      }
    });

    socket.on('typing', ({ sessionId, isTyping }) => {
      if (sessionId) {
        socket.to(`session-${sessionId}`).emit('user-typing', {
          userType,
          userId,
          isTyping,
        });
      }
    });

    socket.on('create-session', async ({ operatorId }) => {
      try {
        if (userType !== 'customer') {
          socket.emit('error', { message: 'Only customers can create sessions' });
          return;
        }

        const existingSession = await ChatSessionModel.findActiveByCustomerId(userId);
        
        if (existingSession) {
          socket.emit('session-created', {
            sessionId: existingSession.id,
            existing: true,
          });
          return;
        }

        const sessionId = await ChatSessionModel.create(userId, operatorId);
        
        socket.emit('session-created', {
          sessionId,
          existing: false,
        });

        io.emit('session-created', {
          sessionId,
          customerId: userId,
          customerName: user.name || 'Cliente',
        });

        socket.join(`session-${sessionId}`);
        socket.currentSession = sessionId;
      } catch (error) {
        console.error('Error creating session:', error);
        socket.emit('error', { message: 'Failed to create session' });
      }
    });

    socket.on('close-session', async ({ sessionId }) => {
      try {
        const session = await ChatSessionModel.findById(sessionId);
        
        if (!session) {
          socket.emit('error', { message: 'Session not found' });
          return;
        }

        if (userType === 'operator' && session.operator_id !== userId) {
          socket.emit('error', { message: 'Unauthorized' });
          return;
        }

        await ChatSessionModel.close(sessionId);
        
        io.to(`session-${sessionId}`).emit('session-closed', { sessionId });
      } catch (error) {
        console.error('Error closing session:', error);
        socket.emit('error', { message: 'Failed to close session' });
      }
    });

    socket.on('get-active-sessions', async () => {
      try {
        if (userType !== 'operator') {
          socket.emit('error', { message: 'Only operators can list sessions' });
          return;
        }

        const sessions = await ChatSessionModel.findActiveByOperator(userId);
        
        socket.emit('active-sessions', { sessions });
      } catch (error) {
        console.error('Error getting active sessions:', error);
        socket.emit('error', { message: 'Failed to get sessions' });
      }
    });

    socket.on('mark-as-read', async ({ sessionId }) => {
      try {
        if (userType !== 'operator') {
          return;
        }

        await ChatMessageModel.markAsRead(sessionId);
        
        const sessions = await ChatSessionModel.findActiveByOperator(userId);
        socket.emit('active-sessions', { sessions });
      } catch (error) {
        console.error('Error marking messages as read:', error);
      }
    });

    socket.on('disconnect', () => {});
  });
};

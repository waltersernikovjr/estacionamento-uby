# Chat Service - Estacionamento Uby

Real-time chat service using WebSocket (Socket.io) for communication between operators and customers.

## Architecture

- **Node.js 18+** with ES Modules
- **Socket.io** for WebSocket connections
- **Express** for HTTP server and health checks
- **MySQL2** for database integration
- **JWT** authentication (shared with Laravel backend)

## Features

- ✅ Real-time bidirectional communication
- ✅ JWT authentication
- ✅ Multiple chat sessions support
- ✅ Message persistence in MySQL
- ✅ Typing indicators
- ✅ Session management (create, join, close)
- ✅ Authorization (customers and operators have different permissions)

## Environment Variables

```env
NODE_ENV=development
PORT=3001
HOST=0.0.0.0
JWT_SECRET=your-jwt-secret-key
DB_HOST=mysql
DB_PORT=3306
DB_USER=laravel
DB_PASSWORD=secret
DB_NAME=estacionamento_uby
CORS_ORIGIN=http://localhost:3000,http://localhost:8000
LOG_LEVEL=debug
```

## Installation

### Local Development

```bash
cd chat-service
npm install
cp .env.example .env
npm run dev
```

### Docker

```bash
docker-compose up -d chat
docker-compose logs -f chat
```

## API Endpoints

### Health Check

```http
GET /health
```

**Response:**
```json
{
  "status": "ok",
  "service": "chat-service",
  "timestamp": "2025-11-19T..."
}
```

## WebSocket Events

### Authentication

Connect with JWT token in `auth` object:

```javascript
const socket = io('http://localhost:3001', {
  auth: {
    token: 'your-jwt-token-here'
  }
});
```

### Events (Client → Server)

#### `create-session`
Create a new chat session (customer only)

```javascript
socket.emit('create-session', {
  operatorId: 1
});
```

#### `join-session`
Join an existing session

```javascript
socket.emit('join-session', {
  sessionId: 123
});
```

#### `send-message`
Send a message in current session

```javascript
socket.emit('send-message', {
  sessionId: 123,
  message: 'Hello, I need help!'
});
```

#### `typing`
Indicate typing status

```javascript
socket.emit('typing', {
  sessionId: 123,
  isTyping: true
});
```

#### `close-session`
Close a session (operator only)

```javascript
socket.emit('close-session', {
  sessionId: 123
});
```

### Events (Server → Client)

#### `session-created`
Emitted when session is created

```javascript
socket.on('session-created', ({ sessionId, existing }) => {
  console.log('Session ID:', sessionId);
});
```

#### `session-joined`
Emitted when successfully joined a session

```javascript
socket.on('session-joined', ({ sessionId, messages }) => {
  console.log('Joined session:', sessionId);
  console.log('Message history:', messages);
});
```

#### `new-message`
Emitted when new message arrives

```javascript
socket.on('new-message', (messageData) => {
  console.log('New message:', messageData);
});
```

#### `user-typing`
Emitted when other user is typing

```javascript
socket.on('user-typing', ({ userType, userId, isTyping }) => {
  console.log(`${userType} #${userId} is typing:`, isTyping);
});
```

#### `session-closed`
Emitted when session is closed

```javascript
socket.on('session-closed', ({ sessionId }) => {
  console.log('Session closed:', sessionId);
});
```

#### `error`
Emitted on errors

```javascript
socket.on('error', ({ message }) => {
  console.error('Error:', message);
});
```

## Database Schema

Uses existing Laravel tables:

### `chat_sessions`
```sql
id, customer_id, operator_id, status, started_at, ended_at, created_at, updated_at
```

### `chat_messages`
```sql
id, session_id, sender_type, sender_id, message, read_at, created_at
```

## Testing Connection

```javascript
const io = require('socket.io-client');

const socket = io('http://localhost:3001', {
  auth: {
    token: 'YOUR_JWT_TOKEN'
  }
});

socket.on('connect', () => {
  console.log('Connected!');
});

socket.on('error', (error) => {
  console.error('Error:', error);
});
```

## Security

- ✅ JWT verification on connection
- ✅ Authorization checks per event
- ✅ Session ownership validation
- ✅ SQL injection protection (parameterized queries)
- ✅ CORS configuration

## Logging

Logs include:
- User connections/disconnections
- Session creation
- Messages sent
- Errors

Format: `[TYPE] Message with context`

Example:
```
✅ User connected: customer #42
📩 customer #42 joined session #10
💬 Message sent in session #10 by customer #42
❌ User disconnected: customer #42
```

## Performance

- Connection pooling for MySQL (max 10 connections)
- Automatic reconnection on database failures
- Room-based message broadcasting (only session participants receive messages)

## Troubleshooting

### Cannot connect to database
Check MySQL container is running and credentials are correct:
```bash
docker-compose ps mysql
docker-compose logs mysql
```

### JWT verification fails
Ensure `JWT_SECRET` matches Laravel's `APP_KEY` (base64 decoded).

### CORS errors
Add your frontend URL to `CORS_ORIGIN` environment variable.

## Production Considerations

- [ ] Use PM2 or similar process manager
- [ ] Configure proper logging (Winston, Pino)
- [ ] Add rate limiting
- [ ] Implement Redis adapter for Socket.io (horizontal scaling)
- [ ] Add monitoring (health checks, metrics)
- [ ] Use HTTPS/WSS in production

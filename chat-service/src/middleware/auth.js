import jwt from 'jsonwebtoken';
import { config } from '../config/index.js';
import { getDatabase } from '../config/database.js';

export const authenticateSocket = async (socket, next) => {
  const token = socket.handshake.auth.token || socket.handshake.headers.authorization?.split(' ')[1];

  if (!token) {
    return next(new Error('Authentication token missing'));
  }

  try {
    try {
      const decoded = jwt.verify(token, config.jwt.secret);
      socket.user = decoded;
      return next();
    } catch (jwtError) {
      const [tokenId, tokenValue] = token.split('|');
      
      if (!tokenId || !tokenValue) {
        return next(new Error('Invalid token format'));
      }

      const db = getDatabase();
      
      const query = 'SELECT pat.*, COALESCE(c.id, o.id) as user_id, COALESCE(c.name, o.name) as name, COALESCE(c.email, o.email) as email, IF(c.id IS NOT NULL, "customer", "operator") as type FROM personal_access_tokens pat LEFT JOIN customers c ON pat.tokenable_type = "App\\\\Infrastructure\\\\Persistence\\\\Models\\\\Customer" AND pat.tokenable_id = c.id LEFT JOIN operators o ON pat.tokenable_type = "App\\\\Infrastructure\\\\Persistence\\\\Models\\\\Operator" AND pat.tokenable_id = o.id WHERE pat.id = ? LIMIT 1';
      
      const [rows] = await db.execute(query, [tokenId]);

      if (rows.length === 0) {
        return next(new Error('Token not found'));
      }

      const tokenData = rows[0];

      const crypto = await import('crypto');
      const hash = crypto.createHash('sha256').update(tokenValue).digest('hex');
      
      if (tokenData.token !== hash) {
        return next(new Error('Invalid token'));
      }

      socket.user = {
        id: tokenData.user_id,
        name: tokenData.name,
        email: tokenData.email,
        type: tokenData.type
      };
      
      next();
    }
  } catch (error) {
    console.error('Auth error:', error);
    return next(new Error('Authentication failed'));
  }
};

export const verifyToken = (token) => {
  try {
    return jwt.verify(token, config.jwt.secret);
  } catch (error) {
    throw new Error('Invalid or expired token');
  }
};

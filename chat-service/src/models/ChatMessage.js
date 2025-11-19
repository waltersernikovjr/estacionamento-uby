import { getDatabase } from '../config/database.js';

export class ChatMessageModel {
  static async create(sessionId, senderType, senderId, message) {
    const db = getDatabase();
    const [result] = await db.execute(
      `INSERT INTO chat_messages (chat_session_id, sender_type, sender_id, message, created_at) 
       VALUES (?, ?, ?, ?, NOW())`,
      [sessionId, senderType, senderId, message]
    );
    return result.insertId;
  }

  static async findBySessionId(sessionId, limit = 50) {
    const db = getDatabase();
    const [rows] = await db.execute(
      `SELECT * FROM chat_messages 
       WHERE chat_session_id = ? 
       ORDER BY created_at DESC 
       LIMIT ${limit}`,
      [sessionId]
    );
    return rows.reverse();
  }

  static async markAsRead(sessionId) {
    const db = getDatabase();
    
    // Marca todas as mensagens não lidas de clientes na sessão
    const [result] = await db.execute(
      `UPDATE chat_messages 
       SET read_at = NOW() 
       WHERE chat_session_id = ? 
       AND sender_type = 'customer' 
       AND read_at IS NULL`,
      [sessionId]
    );
    
    console.log(`📖 Marked ${result.affectedRows} messages as read in session ${sessionId}`);
    return result.affectedRows;
  }

  static async countUnreadBySession(sessionId, senderType) {
    const db = getDatabase();
    const [rows] = await db.execute(
      'SELECT COUNT(*) as count FROM chat_messages WHERE chat_session_id = ? AND sender_type != ? AND read_at IS NULL',
      [sessionId, senderType]
    );
    return rows[0].count;
  }
}

import { getDatabase } from '../config/database.js';

export class ChatSessionModel {
  static async create(customerId, operatorId) {
    const db = getDatabase();
    const [result] = await db.execute(
      `INSERT INTO chat_sessions (customer_id, operator_id, status, started_at, created_at, updated_at) 
       VALUES (?, ?, 'active', NOW(), NOW(), NOW())`,
      [customerId, operatorId]
    );
    return result.insertId;
  }

  static async findById(sessionId) {
    const db = getDatabase();
    const [rows] = await db.execute(
      'SELECT * FROM chat_sessions WHERE id = ?',
      [sessionId]
    );
    return rows[0] || null;
  }

  static async findActiveByCustomerId(customerId) {
    const db = getDatabase();
    const [rows] = await db.execute(
      "SELECT * FROM chat_sessions WHERE customer_id = ? AND status = 'active' ORDER BY started_at DESC LIMIT 1",
      [customerId]
    );
    return rows[0] || null;
  }

  static async findActiveByOperatorId(operatorId) {
    const db = getDatabase();
    const query = `
      SELECT 
        cs.*, 
        c.name as customer_name, 
        c.email as customer_email,
        (SELECT message FROM chat_messages WHERE chat_session_id = cs.id ORDER BY created_at DESC LIMIT 1) as last_message,
        (SELECT COUNT(*) FROM chat_messages WHERE chat_session_id = cs.id AND sender_type = 'customer' AND read_at IS NULL) as unread_count
      FROM chat_sessions cs 
      JOIN customers c ON cs.customer_id = c.id 
      WHERE cs.operator_id = ? AND cs.status = 'active' 
      ORDER BY cs.updated_at DESC
    `;
    const [rows] = await db.execute(query, [operatorId]);
    return rows;
  }

  // Alias para compatibilidade
  static async findActiveByOperator(operatorId) {
    return this.findActiveByOperatorId(operatorId);
  }

  static async close(sessionId) {
    const db = getDatabase();
    await db.execute(
      "UPDATE chat_sessions SET status = 'closed', ended_at = NOW(), updated_at = NOW() WHERE id = ?",
      [sessionId]
    );
  }
}

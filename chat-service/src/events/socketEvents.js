import { ChatSessionModel } from "../models/ChatSession.js";
import { ChatMessageModel } from "../models/ChatMessage.js";

export const setupSocketEvents = (io) => {
  io.on("connection", (socket) => {
    const user = socket.user;
    const userType = user.type || "customer";
    const userId = user.sub || user.id;

    socket.on("join-session", async ({ sessionId }) => {
      try {
        const session = await ChatSessionModel.findById(sessionId);

        if (!session) {
          socket.emit("error", { message: "Session not found" });
          return;
        }

        if (userType === "customer" && session.customer_id !== userId) {
          socket.emit("error", { message: "Unauthorized access to session" });
          return;
        }

        if (userType === "operator" && session.operator_id !== userId) {
          socket.emit("error", { message: "Unauthorized access to session" });
          return;
        }

        socket.join(`session-${sessionId}`);
        socket.currentSession = sessionId;

        const messages = await ChatMessageModel.findBySessionId(sessionId);

        socket.emit("session-joined", {
          sessionId,
          messages,
        });
      } catch (error) {
        console.error("Error joining session:", error);
        socket.emit("error", { message: "Failed to join session" });
      }
    });

    socket.on("send-message", async ({ sessionId, message }) => {
      try {
        if (!sessionId || !message?.trim()) {
          socket.emit("error", { message: "Invalid message data" });
          return;
        }

        const session = await ChatSessionModel.findById(sessionId);

        if (!session || session.status !== "active") {
          socket.emit("error", { message: "Session is not active" });
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

        io.to(`session-${sessionId}`).emit("new-message", messageData);

        io.emit("session-updated", { sessionId });
      } catch (error) {
        console.error("Error sending message:", error);
        socket.emit("error", { message: "Failed to send message" });
      }
    });

    socket.on("typing", ({ sessionId, isTyping }) => {
      if (sessionId) {
        socket.to(`session-${sessionId}`).emit("user-typing", {
          userType,
          userId,
          isTyping,
        });
      }
    });

    socket.on("create-session", async ({ operatorId }) => {
      try {
        if (userType !== "customer") {
          socket.emit("error", {
            message: "Only customers can create sessions",
          });
          return;
        }

        const existingSession = await ChatSessionModel.findActiveByCustomerId(
          userId
        );

        if (existingSession) {
          socket.emit("session-created", {
            sessionId: existingSession.id,
            existing: true,
          });
          return;
        }

        const sessionId = await ChatSessionModel.create(userId, operatorId);

        socket.emit("session-created", {
          sessionId,
          existing: false,
        });

        io.emit("session-created", {
          sessionId,
          customerId: userId,
          customerName: user.name || "Cliente",
        });

        socket.join(`session-${sessionId}`);
        socket.currentSession = sessionId;
      } catch (error) {
        console.error("Error creating session:", error);
        socket.emit("error", { message: "Failed to create session" });
      }
    });

    socket.on("close-session", async ({ sessionId }) => {
      try {
        const session = await ChatSessionModel.findById(sessionId);

        if (!session) {
          socket.emit("error", { message: "Session not found" });
          return;
        }

        if (userType === "operator" && session.operator_id !== userId) {
          socket.emit("error", { message: "Unauthorized" });
          return;
        }

        await ChatSessionModel.close(sessionId);

        io.to(`session-${sessionId}`).emit("session-closed", { sessionId });
      } catch (error) {
        console.error("Error closing session:", error);
        socket.emit("error", { message: "Failed to close session" });
      }
    });

    socket.on("get-active-sessions", async () => {
      try {
        if (userType !== "operator") {
          socket.emit("error", { message: "Only operators can list sessions" });
          return;
        }

        const sessions = await ChatSessionModel.findActiveByOperator(userId);

        socket.emit("active-sessions", { sessions });
      } catch (error) {
        console.error("Error getting active sessions:", error);
        socket.emit("error", { message: "Failed to get sessions" });
      }
    });

    socket.on("mark-as-read", async ({ sessionId }) => {
      try {
        if (userType !== "operator") {
          return;
        }

        await ChatMessageModel.markAsRead(sessionId);

        const sessions = await ChatSessionModel.findActiveByOperator(userId);
        socket.emit("active-sessions", { sessions });
      } catch (error) {
        console.error("Error marking messages as read:", error);
      }
    });

    socket.on("disconnect", () => {});

    socket.on("sendMessage", async (data) => {
      try {
        const { message, recipientId, recipientType } = data;

        if (!message?.trim()) {
          socket.emit("error", { message: "Message is required" });
          return;
        }

        const { getDatabase } = await import("../config/database.js");
        const db = getDatabase();

        const [result] = await db.execute(
          "INSERT INTO chat_messages (sender_id, sender_type, recipient_id, recipient_type, message, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
          [userId, userType, recipientId || 0, recipientType, message.trim()]
        );

        const messageData = {
          id: result.insertId.toString(),
          senderId: userId,
          senderName: user.name || "Unknown",
          senderType: userType,
          recipientId: recipientId || 0,
          recipientType: recipientType,
          message: message.trim(),
          timestamp: new Date().toISOString(),
        };

        socket.emit("message", messageData);

        if (recipientType) {
          if (recipientId && recipientId > 0) {
            const recipientSocketId = Array.from(
              io.sockets.sockets.values()
            ).find(
              (s) =>
                s.user?.id === recipientId && s.user?.type === recipientType
            )?.id;

            if (recipientSocketId) {
              io.to(recipientSocketId).emit("message", messageData);
            }
          } else {
            Array.from(io.sockets.sockets.values())
              .filter((s) => s.user?.type === recipientType)
              .forEach((s) => {
                s.emit("message", messageData);
              });
          }
        }
      } catch (error) {
        console.error("❌ Error sending message:", error);
        socket.emit("error", { message: "Failed to send message" });
      }
    });

    socket.on("loadHistory", async (data) => {
      try {
        const { customerId } = data || {};

        const { getDatabase } = await import("../config/database.js");
        const db = getDatabase();

        let query, params;

        if (userType === "operator" && customerId) {
          query = `
            SELECT id, sender_id as senderId, sender_type as senderType, 
                   recipient_id as recipientId, recipient_type as recipientType,
                   message, created_at as timestamp
            FROM chat_messages 
            WHERE (sender_id = ? AND sender_type = 'operator' AND recipient_id = ? AND recipient_type = 'customer')
               OR (sender_id = ? AND sender_type = 'customer' AND recipient_id = ? AND recipient_type = 'operator')
            ORDER BY created_at ASC
            LIMIT 100
          `;
          params = [userId, customerId, customerId, userId];
        } else if (userType === "operator" && !customerId) {
          query = `
            SELECT id, sender_id as senderId, sender_type as senderType,
                   recipient_id as recipientId, recipient_type as recipientType,
                   message, created_at as timestamp
            FROM chat_messages
            WHERE (sender_id = ? AND sender_type = 'operator')
               OR (recipient_type = 'operator' AND (recipient_id = ? OR recipient_id = 0))
            ORDER BY created_at ASC
          `;
          params = [userId, userId];
        } else if (userType === "customer") {
          query = `
            SELECT id, sender_id as senderId, sender_type as senderType,
                   recipient_id as recipientId, recipient_type as recipientType,
                   message, created_at as timestamp
            FROM chat_messages 
            WHERE (sender_id = ? AND sender_type = 'customer')
               OR (recipient_id = ? AND recipient_type = 'customer')
            ORDER BY created_at ASC
            LIMIT 100
          `;
          params = [userId, userId];
        } else {
          socket.emit("messageHistory", []);
          return;
        }

        const [rows] = await db.execute(query, params);

        const userIds = [...new Set(rows.map((r) => r.senderId))];
        const userNamesMap = {};

        if (userIds.length > 0) {
          const placeholders = userIds.map(() => "?").join(",");
          const [users] = await db.execute(
            `SELECT id, name, 'customer' as type FROM customers WHERE id IN (${placeholders})
             UNION
             SELECT id, name, 'operator' as type FROM users WHERE id IN (${placeholders})`,
            [...userIds, ...userIds]
          );

          users.forEach((u) => {
            userNamesMap[`${u.type}-${u.id}`] = u.name;
          });
        }

        const messages = rows.map((row) => {
          let customerId = null;
          if (userType === "operator") {
            customerId =
              row.senderType === "customer" ? row.senderId : row.recipientId;
          }

          const senderKey = `${row.senderType}-${row.senderId}`;
          const senderName = userNamesMap[senderKey] || user.name || "Usuário";

          return {
            id: row.id.toString(),
            senderId: row.senderId,
            senderName,
            senderType: row.senderType,
            recipientId: row.recipientId,
            recipientType: row.recipientType,
            message: row.message,
            timestamp: row.timestamp,
            customerId,
          };
        });

        socket.emit("messageHistory", messages);
      } catch (error) {
        console.error("❌ Error loading history:", error);
        socket.emit("messageHistory", []);
      }
    });
  });
};

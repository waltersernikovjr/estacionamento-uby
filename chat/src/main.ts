/* import { RedisAdapter } from "@socket.io/redis-adapter";
 */
import { Server } from "socket.io";
import SocketServer from "./SocketServer.js";
import { InmemoryChatDao } from "./data/ChatDao.js";
import Chat from "./application/Chat.js";
import GetMessages from "./application/GetMessages.js";

// Caso queria colocar um adapter para escalar a aplicação horizontalmente
const io = new SocketServer(new Server({ /* adapter: RedisAdapter */
    cors: {
        origin: ["http://127.0.0.1:5173", "http://localhost:5173", "*"]
    }

}))

const chatDao = new InmemoryChatDao();

io.addListener("join", (payload: { userId: number }, socket) => {
    socket.join(`${payload.userId}`);

    return { conected: true }
});

io.addListener('get-messages', async (payload: { userId: number }, socket) => {
    const chats = await new GetMessages(chatDao).execute(payload.userId);

    return chats
})

io.addListener("chat", async (payload: any, socket) => {
    const chat = await new Chat(chatDao).execute(payload)

    socket.to(`${payload.to.userId}`).emit('message-receive', chat);
});

io.init();
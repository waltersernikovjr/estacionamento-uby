/* import { RedisAdapter } from "@socket.io/redis-adapter";
 */
import { Server } from "socket.io";

// Caso queria colocar um adapter para escalar a aplicação horizontalmente
const io = new Server({ /* adapter: RedisAdapter */

    cors: {
        origin: ["http://localhost:5173"]
    }

});

type User = {
    userId: number;
    nome: string
}

const AddListener = (handlerFn: (payload: any) => any) => {
    return (...agrs: any[]) => {
        const [ack, payload] = agrs.reverse();

        if (!ack) return;


        const response = handlerFn(payload);

        ack(response);
    }
}


io.on("connection", (socket) => {
    socket.on("join", AddListener((payload: { userId: number }) => {
        socket.join(`${payload.userId}`);

        return { conected: true }
    }));

    socket.on("chat", AddListener((payload: { content: string, to: User, from: User }) => {
        socket.to(`${payload.to}`).emit('message-receive', { message: payload.content, from: payload.from })
    }));
});

io.listen(3000);
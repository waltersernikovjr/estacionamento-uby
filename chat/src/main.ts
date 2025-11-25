/* import { RedisAdapter } from "@socket.io/redis-adapter";
 */import { Server } from "socket.io"

// Caso queria colocar um adapter para escalar a aplicação horizontalmente
const io = new Server({ /* adapter: RedisAdapter */

    cors: {
        origin: ["http://localhost:5173"]
    }

});

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
        console.log(payload);

        socket.join(`user-${payload.userId}`);

        return { conected: true }
    }));


    socket.on("chat", AddListener((payload: { content: string, for: number }) => {


        socket.to(`user-${payload.for}`).emit('message-receive', { message: payload.content })
    }));
});

io.listen(3000);
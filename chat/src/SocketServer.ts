import { Server, Socket } from "socket.io";

export default class SocketServer {
    constructor(private readonly _server: Server,
        private readonly _listeners: Map<string, (payload: any, socket: Socket) => Promise<any>> = new Map(),
    ) { }

    public addListener(eventName: string, callback: (payload: any, socket: Socket) => any) {
        this._listeners.set(eventName, callback);
    }


    public init() {
        this._server.on('connection', (socket) => {
            for (const [eventName, callback] of this._listeners.entries()) {
                socket.on(eventName, (...agrs: any[]) => {
                    const [ack, payload] = agrs.reverse();

                    if (!ack) return;

                    const response = callback(payload, socket);

                    ack(response);
                })
            }
        })
        this._server.listen(3000)
    }
}
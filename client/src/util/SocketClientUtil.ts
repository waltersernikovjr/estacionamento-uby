import type { Socket } from "socket.io-client";

export default class SocketClient {
    constructor(private readonly _socket: Socket) { }

    public addListener(eventName: string, fn: (data: any) => void) {
        this._socket.on(eventName, fn);
    }

    public async send<T>(eventName: string, data: any): Promise<T> {
        return new Promise((res) => {
            this._socket.emit(eventName, data, (data: any) => {
                res(data)
            })
        })
    }
}
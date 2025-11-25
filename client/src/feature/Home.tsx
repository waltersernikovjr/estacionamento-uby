import LocalStorageUtil from "../util/LocalStorageUtil";
import { useEffect, useState } from "react"
import { Singin } from "./Singin";

import type { UserProps } from "../model/User";
import { Vagas } from "./Vagas";
import { io } from "socket.io-client";
import { useDI } from "../di/DIContext";
import type SocketClient from "../util/SocketClientUtil";

type Messages = {
    from: {
        userId: number;
        nome: string;
    };
    content: string;
}


export const NavBar = ({ user }: { user: UserProps | null }) => {
    const [messages, setMessages] = useState<Messages[]>([]);
    const [dropdown, setDropdown] = useState<boolean>(false);

    const socketClient = useDI<SocketClient>('socketClient')

    socketClient.addListener('message-receive', (data: { message: string, from: { userId: number, nome: string } }) => {
        setMessages((prev) => ([...prev, {
            from: data.from,
            content: data.message,
        }]))
    });

    if (user === null) return <div className="h-20 bg-blue-950 flex flex-row-reverse"></div>
    return <div className="h-20 bg-blue-950 flex flex-row-reverse justify-between">
        <button className="m-2 cursor-pointer" onClick={() => LocalStorageUtil.set("user", null)}>Logout</button>
        <button className="m-2 cursor-pointer" onClick={() => setDropdown(true)}>Messages {messages.length}</button>
    </div>

}

export const Home = () => {
    const [user, setUser] = useState<UserProps | null>(null);
    const socketClient = useDI<SocketClient>('socketClient')

    useEffect(() => {
        const saved = LocalStorageUtil.get<UserProps>("user");
        if (saved) setUser(saved);

        const handler = (e: any) => {
            if (e.detail.key === "user") {
                if (e.detail.value === null) setUser(null);
                setUser(e.detail.value);
            }
        };

        window.addEventListener("localstorage-update", handler);
        return () => window.removeEventListener("localstorage-update", handler);
    }, []);

    useEffect(() => {
        if (!user) return;

        socketClient.send('join', { userId: user.id }).then((res) => {
            console.log(res);
        });

    }, [user])

    return (
        <div>
            <NavBar user={user} />
            {!user ? <div className="flex justify-evenly p-4"><Singin /></div> : <Vagas />}
        </div>
    )
}

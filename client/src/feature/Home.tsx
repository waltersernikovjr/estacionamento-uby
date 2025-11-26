import LocalStorageUtil from "../util/LocalStorageUtil";
import { useEffect, useState } from "react"
import { Singin } from "./Singin";

import type { UserProps } from "../model/User";
import { Vagas } from "./Vagas";
import { io } from "socket.io-client";
import { useDI } from "../di/DIContext";
import type SocketClient from "../util/SocketClientUtil";
import type { OperadorGateway } from "../gateway/OperadorGateway";
import { Role } from "../enum/Role";

type Messages = {
    from: {
        userId: number;
        nome: string;
    };
    to: {
        userId: number;
        nome: string;
    }
    message: string;
}


export const NavBar = ({ user }: { user: UserProps | null }) => {
    const [messages, setMessages] = useState<Messages[]>([]);
    const [dropdown, setDropdown] = useState<boolean>(false);
    const [activeReplyUser, setActiveReplyUser] = useState<{ userId: number; nome: string } | null>(null);
    const [replyText, setReplyText] = useState<string>("");
    const [operators, setOperators] = useState<Array<{ id: number; nome: string }>>([]);

    const socketClient = useDI<SocketClient>('socketClient');
    const operadorGateway = useDI<OperadorGateway>('operadorGateway');

    useEffect(() => {
        socketClient.send<Messages[]>('get-messages', { userId: user?.id }).then(result => {
            if (!result || Object.keys(result).length === 0) return;

            setMessages(result || []);
        });


        if (user?.role === Role.CLIENTE) {
            operadorGateway.get().then((opList: any[]) => {
                console.log(opList);

                setOperators(opList); // deve vir [{id, nome}]
            });
        }
    }, [user]);

    socketClient.addListener('message-receive', (data: { messages: Array<Messages> }) => {
        console.log(data);

        if (!data) return;

        setMessages((prev) => ([...prev, ...data.messages]));
    });

    const sendMessage = async () => {
        if (!activeReplyUser || replyText.trim() === "") return;

        await socketClient.send("chat", {
            from: {
                userId: user!.id,
                nome: user!.nome
            },
            to: {
                userId: activeReplyUser.userId,
                nome: activeReplyUser.nome
            },
            content: replyText
        });

        setMessages(prev => [
            ...prev,
            {
                from: { userId: user!.id, nome: user!.nome },
                to: activeReplyUser,
                message: replyText
            } as unknown as Messages
        ]);

        setReplyText("");
    };


    if (user === null) return <div className="h-20 bg-blue-950 flex flex-row-reverse"></div>
    return <div className="h-20 bg-blue-950 flex flex-row-reverse justify-between">
        <button className="m-2 cursor-pointer" onClick={() => {
            LocalStorageUtil.set("user", null)
            LocalStorageUtil.set("token_type", null)
            LocalStorageUtil.set("operador_token", null)
            LocalStorageUtil.set("token", null)
        }}>Logout</button>
        <button className="m-2 cursor-pointer" onClick={() => setDropdown(true)}>Messages {messages.length}</button>
        {dropdown && (
            <div className="absolute right-0 top-20 w-80 bg-white shadow-lg rounded p-3 max-h-96 overflow-y-auto">

                {messages.length === 0 && (
                    <p className="text-gray-600 text-sm">Nenhuma mensagem</p>
                )}

                {messages.map((msg, index) => (
                    <div
                        key={index}
                        className="border-b border-gray-300 py-2 cursor-pointer hover:bg-gray-100"
                        onClick={() => {
                            if (user.role !== Role.CLIENTE) {
                                setActiveReplyUser({
                                    userId: msg.from.userId,
                                    nome: msg.from.nome
                                });
                            }
                        }}
                    >
                        <div className="text-xs text-gray-500">
                            <strong>{msg.from.nome}</strong> → {msg.to.nome}
                        </div>
                        <div className="text-sm">{msg.message}</div>
                    </div>
                ))}


                {/* Campo de responder */}
                <div className="mt-3">
                    <p className="text-xs text-gray-700 mb-1">
                        Enviar mensagem para:
                    </p>

                    {user.role === Role.CLIENTE ? (

                        <select
                            className="w-full text-gray-950 border rounded px-2 py-1 text-sm mb-2"
                            onChange={(e) => {
                                const opId = Number(e.target.value);
                                const op = operators.find(o => o.id === opId);
                                if (op) setActiveReplyUser({ userId: op.id, nome: op.nome });
                            }}
                        >
                            <option className="text-gray-950" value="">Selecione um operador</option>
                            {operators.map(op => (
                                <option className="text-gray-950" key={op.id} value={op.id}>
                                    {op.nome}
                                </option>
                            ))}
                        </select>

                    ) : (

                        activeReplyUser && (
                            <p className="text-xs text-gray-700 mb-2">
                                Respondendo para: <strong>{activeReplyUser.nome}</strong>
                            </p>
                        )
                    )}

                    <input
                        type="text"
                        value={replyText}
                        onChange={e => setReplyText(e.target.value)}
                        className="w-full text-gray-900 border rounded px-2 py-1 text-sm"
                        placeholder="Digite sua mensagem..."
                    />

                    <button
                        onClick={sendMessage}
                        className="mt-2 w-full bg-blue-600 text-gray py-1 rounded text-sm"
                    >
                        Enviar
                    </button>
                </div>

            </div>
        )}
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

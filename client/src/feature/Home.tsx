import LocalStorageUtil from "../util/LocalStorageUtil";
import { useEffect, useState } from "react"
import { Singin } from "./Singin";

import type { UserProps } from "../model/User";
import { Vagas } from "./Vagas";
import { io } from "socket.io-client";

export const NavBar = ({ user }: { user: UserProps | null }) => {
    if (user === null) return <div className="h-20 bg-blue-950 flex flex-row-reverse"></div>

    return <div className="h-20 bg-blue-950 flex flex-row-reverse">
        {user ? <button className="m-2 cursor-pointer" onClick={() => LocalStorageUtil.set("user", null)}>Logout</button> : <></>}
    </div>

}

export const Home = () => {
    const [user, setUser] = useState<UserProps | null>(null);

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

        const socket = io('ws://localhost:3000');

        socket.emit('join', { userId: user.id }, (response: { conected: boolean }) => {
            console.log(response);
        });

    }, [user])

    return (
        <div>
            <NavBar user={user} />
            {!user ? <div className="flex justify-evenly p-4"><Singin /></div> : <Vagas />}
        </div>
    )
}

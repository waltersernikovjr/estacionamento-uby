import LocalStorageUtil from "../util/LocalStorageUtil";
import { useEffect, useState } from "react"
import { Singin } from "./Singin";

import type { UserProps } from "../model/User";
import { Vagas } from "./Vagas";

export const Home = () => {
    const [user, setUser] = useState<UserProps | null>(null);

    useEffect(() => {
        const saved = LocalStorageUtil.get<UserProps>("user");
        if (saved) setUser(saved);

        const handler = (e: any) => {
            if (e.detail.key === "user") {
                setUser(e.detail.value);
            }
        };

        window.addEventListener("localstorage-update", handler);
        return () => window.removeEventListener("localstorage-update", handler);
    }, []);

    return (
        <div>
            <div className="h-20 bg-blue-950">

            </div>
            {!user ? <div className="flex justify-evenly p-4"><Singin /></div> : <Vagas />}
        </div>
    )
}

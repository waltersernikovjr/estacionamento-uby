import React, { createContext, useContext } from "react";

type Container = Map<string, any>;

export const DIContext = createContext<Container | null>(null);

export function useDI<T>(key: string): T {
    const container = useContext(DIContext);

    if (!container) throw new Error("DIProvider não encontrado!");

    const dependency = container.get(key);

    if (!dependency) throw new Error(`Dependência '${key}' não registrada no DI container.`);

    return dependency as T;
}


export const DIProvider = ({ children, container }: { children: React.ReactNode, container: Map<string, any> }) => {


    return <DIContext.Provider value={container}>{children}</DIContext.Provider>;
};
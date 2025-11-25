import axios, { type AxiosInstance } from "axios";

const API_BASE_URL = "http://127.0.0.1:8000/api";

export abstract class BaseHttpGateway {
    protected api: AxiosInstance;

    constructor() {
        this.api = axios.create({
            baseURL: API_BASE_URL,
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
        });

        this.api.interceptors.request.use((config) => {
            const token = localStorage.getItem("token") || localStorage.getItem("operador_token") || localStorage.getItem("cliente_token");
            if (token) {
                config.headers.Authorization = `Bearer ${token}`;
            }
            return config;
        });
    }

    protected handleError(error: any, operation: string): never {
        if (error.response) {
            const data = error.response.data;
            const message = data.message || "Erro no servidor";
            const details = data.errors
                ? Object.values(data.errors).flat().join(", ")
                : "";
            throw new Error(`${message}${details ? ` - ${details}` : ""}`);
        }

        if (error.request) {
            throw new Error("Não foi possível conectar ao servidor");
        }

        throw new Error(error.message || "Erro desconhecido");
    }
}
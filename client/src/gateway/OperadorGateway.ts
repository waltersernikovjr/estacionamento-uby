import type { Operador } from "../model/Operador";
import { BaseHttpGateway } from "./BaseHttpGateway";

export interface OperadorGateway {
    create(operador: Partial<Operador>): Promise<Operador>;
    login(operador: Partial<Operador>): Promise<Operador>;
    get(): Promise<Operador[]>
}

export class InmemoryOperadorGateway implements OperadorGateway {
    async create(operador: Partial<Operador>): Promise<Operador> {
        return operador as Operador;
    }

    async login(operador: Partial<Operador>): Promise<Operador> {
        return {
            id: 1,
            nome: 'foo',
            email: 'foo@gamil.com',
            cpf: operador.cpf as string
        }
    }

    async get(): Promise<Operador[]> {
        return [];
    }
}

export class HttpOperadorGateway extends BaseHttpGateway implements OperadorGateway {
    async create(operador: Partial<Operador>): Promise<Operador> {
        try {
            const response = await this.api.post("/operador/register", operador);
            const { user: novoOperador, access_token } = response.data;

            if (access_token) {
                localStorage.setItem("token", access_token);
                localStorage.setItem("token_type", "operador");
            }

            return novoOperador;
        } catch (error: any) {
            this.handleError(error, "registro de operador");
        }
    }

    async get(): Promise<Operador[]> {
        try {
            const response = await this.api.get("/operador",);

            return response.data;
        } catch (error: any) {
            this.handleError(error, "registro de operador");
        }
    }

    async login(credenciais: { email: string; password: string }): Promise<Operador> {
        try {
            const response = await this.api.post("/operador/login", credenciais);
            const { user: operador, access_token } = response.data;

            if (access_token) {
                localStorage.setItem("token", access_token);
                localStorage.setItem("token_type", "operador");
            }

            return operador;
        } catch (error: any) {
            console.log(error);

            this.handleError(error, "login de operador");
        }
    }
}

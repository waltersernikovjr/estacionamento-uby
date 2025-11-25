import type { Operador } from "../model/Operador";
import { BaseHttpGateway } from "./BaseHttpGateway";

export interface OperadorGateway {
    create(operador: Partial<Operador>): Promise<Operador>;
    login(operador: Partial<Operador>): Promise<Operador>;
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
}

export class HttpOperadorGateway extends BaseHttpGateway implements OperadorGateway {
    async create(operador: Partial<Operador>): Promise<Operador> {
        try {
            const response = await this.api.post("/operador/register", operador);
            const { operador: novoOperador, token } = response.data;

            if (token) {
                localStorage.setItem("operador_token", token);
                localStorage.setItem("token_type", "operador");
            }

            return novoOperador;
        } catch (error: any) {
            this.handleError(error, "registro de operador");
        }
    }

    async login(credenciais: { email: string; password: string }): Promise<Operador> {
        try {
            const response = await this.api.post("/operador/login", credenciais);
            const { operador, token } = response.data;

            if (token) {
                localStorage.setItem("operador_token", token);
                localStorage.setItem("token_type", "operador");
            }

            return operador;
        } catch (error: any) {
            this.handleError(error, "login de operador");
        }
    }
}

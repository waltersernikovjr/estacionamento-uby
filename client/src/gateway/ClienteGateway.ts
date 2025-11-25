import type { Cliente } from "../model/Cliente";
import { BaseHttpGateway } from "./BaseHttpGateway";

export interface ClienteGateway {
    create(cliente: Partial<Cliente>): Promise<Cliente>;
    login(cliente: Partial<Cliente>): Promise<Cliente>;
}

export class InmemoryClienteGateway implements ClienteGateway {
    async create(cliente: Partial<Cliente>): Promise<Cliente> {
        return cliente as Cliente;
    }

    async login(cliente: Partial<Cliente>): Promise<Cliente> {
        return {
            id: 1,
            nomeCompleto: 'foo',
        } as Cliente
    }
}


export class HttpClienteGateway extends BaseHttpGateway implements ClienteGateway {
    async create(cliente: Partial<Cliente>): Promise<Cliente> {
        try {
            const response = await this.api.post("/cliente/register", cliente);
            const { cliente: novoCliente, token } = response.data;

            if (token) {
                localStorage.setItem("cliente_token", token);
                localStorage.setItem("token_type", "cliente"); // opcional: saber quem está logado
            }

            return novoCliente;
        } catch (error: any) {
            this.handleError(error, "registro de cliente");
        }
    }

    async login(credenciais: { email: string; password: string }): Promise<Cliente> {
        try {
            const response = await this.api.post("/cliente/login", credenciais);
            const { cliente, token } = response.data;

            if (token) {
                localStorage.setItem("cliente_token", token);
                localStorage.setItem("token_type", "cliente");
            }

            return cliente;
        } catch (error: any) {
            this.handleError(error, "login de cliente");
        }
    }
}

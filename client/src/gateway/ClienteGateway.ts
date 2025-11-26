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
            const response = await this.api.post("/cliente/register", {
                nome: cliente.nomeCompleto,
                cpf: cliente.cpf,
                rg: cliente.rg,
                email: cliente.email,
                endereco: cliente.endereco,
                password: cliente.password,
                veiculo: {
                    placa: cliente.placa,
                    modelo: cliente.modelo,
                    cor: cliente.cor,
                    ano: cliente.ano,
                }
            });
            const { user: novoCliente, access_token } = response.data;

            if (access_token) {
                localStorage.setItem("token", access_token);
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
            const { user: cliente, access_token } = response.data;

            if (access_token) {
                localStorage.setItem("token", access_token);
                localStorage.setItem("token_type", "cliente");
            }

            return cliente;
        } catch (error: any) {
            this.handleError(error, "login de cliente");
        }
    }
}

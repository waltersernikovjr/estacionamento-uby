import type { Cliente } from "../model/Cliente";


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
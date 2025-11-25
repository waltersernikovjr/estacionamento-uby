import type { Operador } from "../model/Operador";

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
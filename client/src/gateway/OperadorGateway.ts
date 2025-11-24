import type { Operador } from "../model/Operador";

export interface OperadorGateway {
    create(operador: Partial<Operador>): Promise<Operador>;
}

export class InmemoryOperadorGateway implements OperadorGateway {
    async create(operador: Partial<Operador>): Promise<Operador> {
        return operador as Operador;
    }
}
import { VagasStatus } from "../enum/VagaStatus";
import type { Vaga } from "../model/Vaga";

export interface VagaGateway {
    get(): Promise<Array<Vaga>>;
    create(vaga: Vaga): Promise<Vaga>;
}

export class InmemoryVagaGateway implements VagaGateway {
    constructor(private readonly _vagas: Array<Vaga> = []) { }

    async get(): Promise<Array<Vaga>> {
        return this._vagas;
    }

    async create(vaga: Vaga): Promise<Vaga> {
        vaga.id = this._vagas.length + 1;
        vaga.status = VagasStatus.LIVRE;
        this._vagas.push(vaga);

        return vaga;
    }
}
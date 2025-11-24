import type { VagasStatus } from "../enum/VagaStatus";

export type Vaga = {
    id?: number;
    numeroDaVaga: number;
    preco: number;
    dimensao: string;
    status: VagasStatus
};

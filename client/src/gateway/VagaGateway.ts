import { VagasStatus } from "../enum/VagaStatus";
import type { Vaga } from "../model/Vaga";
import { BaseHttpGateway } from "./BaseHttpGateway";

type ServerVaga = {
    numero: number;
    preco_por_hora: number
    largura: number
    comprimento: number
}

export interface VagaGateway {
    get(): Promise<Array<Vaga>>;
    create(vaga: ServerVaga): Promise<Vaga>;
    update(vaga: Vaga): Promise<Vaga>;
}

export class InmemoryVagaGateway implements VagaGateway {
    constructor(private readonly _vagas: Array<Vaga> = []) { }

    async update(vaga: Vaga): Promise<Vaga> {
        if (vaga.status === VagasStatus.LIVRE) {
            vaga.status = VagasStatus.OCUPADA;
        } else {
            vaga.status = VagasStatus.LIVRE;
        }

        return vaga;
    }

    async get(): Promise<Array<Vaga>> {
        return this._vagas;
    }

    async create(vaga: ServerVaga): Promise<Vaga> {
        const created = {
            id: this._vagas.length + 1,
            numeroDaVaga: vaga.numero,
            preco: vaga.preco_por_hora,
            dimensao: `${vaga.largura}x${vaga.comprimento}`,
            status: VagasStatus.OCUPADA
        }
        this._vagas.push(created);

        return created;
    }
}

export class HttpVagaGateway extends BaseHttpGateway implements VagaGateway {
    private get authHeader() {
        const token = localStorage.getItem("token");
        return token ? { Authorization: `Bearer ${token}` } : {};
    }

    async get(): Promise<Array<Vaga>> {
        try {
            const response = await this.api.get<Array<ServerVaga & { id: number, disponivel: boolean }>>("/vagas");
            return response.data.map(item => ({
                id: item.id,
                numeroDaVaga: item.numero,
                preco: item.preco_por_hora,
                dimensao: `${item.largura}x${item.comprimento}`,
                status: item.disponivel ? VagasStatus.LIVRE : VagasStatus.OCUPADA
            }));
        } catch (error: any) {
            this.handleError(error, "busca de vagas");
            throw error;
        }
    }

    async create(vaga: ServerVaga): Promise<Vaga> {
        try {
            const response = await this.api.post("/vagas", vaga, {
                headers: this.authHeader,
            });
            return response.data;
        } catch (error: any) {
            this.handleError(error, "criação de vaga");
            throw error;
        }
    }

    async update(vaga: Vaga): Promise<Vaga> {
        if (!vaga.id) {
            throw new Error("ID da vaga é obrigatório para atualização");
        }

        try {
            const response = await this.api.patch(`/vagas/${vaga.id}/ocupar`, vaga, {
                headers: this.authHeader,
            });
            return response.data;
        } catch (error: any) {
            this.handleError(error, "atualização de vaga");
            throw error;
        }
    }
}
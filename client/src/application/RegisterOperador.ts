
export class RegisterOperador {
    constructor(private readonly _operadorGateway: any) { }

    async excute(data: any) {
        const operador = await this._operadorGateway.create(data);

        return operador;
    }
}
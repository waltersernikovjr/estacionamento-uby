import type { VagaGateway } from "../gateway/VagaGateway";
import type { Vaga } from "../model/Vaga";
import Result from "../util/Result";

export default class UpdateVaga {
    constructor(private readonly _vagaGateway: VagaGateway) { }

    public async execute(input: Input) {
        try {

            return await this._vagaGateway.update(input).then(Result.Ok);
        } catch (err) {
            console.log(err);

            return Result.Error(err as Error);
        }
    }
}

type Input = Vaga
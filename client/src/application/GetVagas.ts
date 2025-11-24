import type { VagaGateway } from "../gateway/VagaGateway";
import Result from "../util/Result";

export default class GetVagas {
    constructor(private readonly _vagasGateway: VagaGateway) { }

    async execute() {
        try {
            return this._vagasGateway.get().then(Result.Ok);
        } catch (err) {
            return Result.Error(err);
        }
    }
}
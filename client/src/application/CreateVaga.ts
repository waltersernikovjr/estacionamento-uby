import { InputError } from "../error/InputError";
import type { VagaGateway } from "../gateway/VagaGateway";
import type { Vaga } from "../model/Vaga";
import Result from "../util/Result";
import { CreateVagaValidation } from "../validation/VagaValidation";

export default class CreateVagas {
    constructor(private readonly _vagasGateway: VagaGateway) { }

    public async execute(input: Input) {
        const { error } = CreateVagaValidation.validate(input, { abortEarly: false });

        if (error) return Result.Error(InputError.create(error));

        try {
            return this._vagasGateway.create(input as Vaga).then(Result.Ok);
        } catch (err) {
            return Result.Error(err as Error);
        }

    }
}

type Input = {
    numeroDaVaga: number;
    preco: number;
    dimensao: string;
}
import { InputError } from "../error/InputError";
import type { VagaGateway } from "../gateway/VagaGateway";
import Result from "../util/Result";
import { CreateVagaValidation } from "../validation/VagaValidation";

export default class CreateVagas {
    constructor(private readonly _vagasGateway: VagaGateway) { }

    public async execute(input: Input) {
        const { error } = CreateVagaValidation.validate(input, { abortEarly: false });

        if (error) return Result.Error(InputError.create(error));

        try {
            const [largura, comprimento] = input.dimensao.split('x');
            return this._vagasGateway.create({
                numero: input.numeroDaVaga,
                preco_por_hora: input.preco,
                largura: Number(largura),
                comprimento: Number(comprimento),
            }).then(Result.Ok);
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
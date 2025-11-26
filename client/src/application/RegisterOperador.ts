import { Role } from "../enum/Role";
import { InputError } from "../error/InputError";
import type { OperadorGateway } from "../gateway/OperadorGateway";
import LocalStorageUtil from "../util/LocalStorageUtil";
import Result from "../util/Result";
import { RegisterOperadorValidator } from "../validation/OperadorValidation";

export class RegisterOperador {
    constructor(private readonly _operadorGateway: OperadorGateway) { }

    async execute(data: Partial<Input>): Promise<Result<any, Error | InputError>> {
        const { error } = RegisterOperadorValidator.validate(data, { abortEarly: false });

        if (error) {
            return Result.Error(InputError.create(error));
        }

        try {
            const operador = await this._operadorGateway.create(data);

            LocalStorageUtil.set("user", {
                id: operador.id,
                nome: operador.nome,
                role: Role.OPERADOR,
            })

            return Result.Ok(operador);
        } catch (err) {
            if (err instanceof Error) return Result.Error(err);

            return Result.Error(new Error("Error raise in server"));
        }
    }
}

type Input = {
    nome: string;
    email: string;
    cpf: string;
    password: string;
    confirmPassword: string;
}
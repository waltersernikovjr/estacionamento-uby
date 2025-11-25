import { Role } from "../enum/Role";
import { InputError } from "../error/InputError";
import type { OperadorGateway } from "../gateway/OperadorGateway";
import type { Operador } from "../model/Operador";
import LocalStorageUtil from "../util/LocalStorageUtil";
import Result from "../util/Result";
import { LoginOperadorValidation } from "../validation/OperadorValidation";

export default class LoginOperador {
    constructor(private readonly _operadorGateway: OperadorGateway) { }

    public async execute(input: Input): Promise<Result<Operador, Error>> {
        const { error } = LoginOperadorValidation.validate(input, { abortEarly: false });

        if (error) return Result.Error(InputError.create(error));

        try {
            const operador = await this._operadorGateway.login(input);

            LocalStorageUtil.set("user", {
                id: operador.id,
                nome: operador.nome,
                role: Role.OPERADOR,
            });

            return Result.Ok(operador);
        } catch (err) {
            return Result.Error(err as Error);
        }
    }
}

type Input = {
    cpf: string;
    password: string;
}
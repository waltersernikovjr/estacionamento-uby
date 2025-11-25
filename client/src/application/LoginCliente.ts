import { Role } from "../enum/Role";
import { InputError } from "../error/InputError";
import type { ClienteGateway } from "../gateway/ClienteGateway";
import type { Cliente } from "../model/Cliente";
import LocalStorageUtil from "../util/LocalStorageUtil";
import Result from "../util/Result";
import { LoginClienteValidation } from "../validation/ClienteValidation";

export default class LoginCliente {
    constructor(private readonly _clienteGateway: ClienteGateway) { }

    public async execute(input: Input): Promise<Result<Cliente, Error>> {
        const { error } = LoginClienteValidation.validate(input, { abortEarly: false });

        if (error) return Result.Error(InputError.create(error));

        try {
            const cliente = await this._clienteGateway.login(input);

            LocalStorageUtil.set("user", {
                id: cliente.id,
                nome: cliente.nomeCompleto,
                role: Role.CLIENTE,
            });

            return Result.Ok(cliente);
        } catch (err) {
            return Result.Error(err as Error);
        }
    }
}

type Input = {
    email: string;
    password: string;
}
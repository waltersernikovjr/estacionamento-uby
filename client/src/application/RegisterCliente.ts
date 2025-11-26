import { Role } from "../enum/Role";
import { InputError } from "../error/InputError";
import type { ClienteGateway } from "../gateway/ClienteGateway";
import type { Cliente } from "../model/Cliente";
import LocalStorageUtil from "../util/LocalStorageUtil";
import Result from "../util/Result";
import { RegisterClienteValidator } from "../validation/ClienteValidation";

export default class RegisterCliente {
    constructor(private readonly _clienteGateway: ClienteGateway) { }

    public async execute(input: Input): Promise<Result<Cliente, Error>> {
        const { error } = RegisterClienteValidator.validate(input, { abortEarly: false });

        if (error) return Result.Error(InputError.create(error));

        try {
            const cliente = await this._clienteGateway.create(input);

            LocalStorageUtil.set("user", {
                id: cliente.id,
                nome: cliente.nomeCompleto,
                role: Role.CLIENTE,
            })

            return Result.Ok(cliente);
        } catch (err) {
            if (err instanceof Error) return Result.Error(err);

            return Result.Error(new Error("Error raise in server"));
        }

    }
}


type Input = {
    nomeCompleto: string;
    cpf: string;
    rg: string;
    email: string;
    password: string;
    confirmPassword: string;
    endereco: string;
    placa: string;
    modelo: string;
    cor: string;
    ano: string;
};
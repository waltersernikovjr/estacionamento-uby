import { InputField } from "../input/InputField";
import { useForm } from "../../hooks/FormHook";
import RegisterCliente from "../../application/RegisterCliente"; // ajuste o caminho conforme sua estrutura
import { useDI } from "../../di/DIContext";

type RegisterClienteFormType = {
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

export const RegisterClienteForm = () => {
    const { data, errors, handleChange, handleError, reset } =
        useForm<RegisterClienteFormType>({
            nomeCompleto: "",
            cpf: "",
            rg: "",
            email: "",
            password: "",
            confirmPassword: "",
            endereco: "",
            placa: "",
            modelo: "",
            cor: "",
            ano: "",
        });

    const registerCliente = useDI<RegisterCliente>("registerCliente");

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        const result = await registerCliente.execute(data);

        if (result.isError()) return handleError(result);

        reset();
    };

    return (
        <div className="w-full max-w-2xl mx-auto p-6">
            <form onSubmit={handleSubmit} className="space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <InputField
                        label="Nome Completo"
                        name="nomeCompleto"
                        placeholder="João Silva"
                        value={data.nomeCompleto}
                        error={errors.nomeCompleto}
                        onChange={handleChange}
                    />
                    <InputField
                        label="CPF"
                        name="cpf"
                        placeholder="000.111.222-33"
                        value={data.cpf}
                        error={errors.cpf}
                        onChange={handleChange}
                    />
                    <InputField
                        label="RG"
                        name="rg"
                        placeholder="12.345.678-9"
                        value={data.rg}
                        error={errors.rg}
                        onChange={handleChange}
                    />
                    <InputField
                        label="E-mail"
                        name="email"
                        type="email"
                        placeholder="joao@email.com"
                        value={data.email}
                        error={errors.email}
                        onChange={handleChange}
                    />
                    <InputField
                        label="Senha"
                        name="password"
                        type="password"
                        placeholder="••••••••"
                        value={data.password}
                        error={errors.password}
                        onChange={handleChange}
                    />
                    <InputField
                        label="Confirme a Senha"
                        name="confirmPassword"
                        type="password"
                        placeholder="••••••••"
                        value={data.confirmPassword}
                        error={errors.confirmPassword}
                        onChange={handleChange}
                    />
                    <InputField
                        label="Endereço"
                        name="endereco"
                        placeholder="Rua A, Bairro B"
                        value={data.endereco}
                        error={errors.endereco}
                        onChange={handleChange}
                    />
                </div>

                <div className="space-y-5 pt-6 border-t border-gray-700">
                    <h3 className="text-lg font-semibold text-gray-300">
                        Dados do Veículo
                    </h3>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <InputField
                            label="Placa"
                            name="placa"
                            placeholder="ABC-1234"
                            className="uppercase"
                            value={data.placa}
                            error={errors.placa}
                            onChange={handleChange}
                        />
                        <InputField
                            label="Modelo"
                            name="modelo"
                            placeholder="Civic EXL"
                            value={data.modelo}
                            error={errors.modelo}
                            onChange={handleChange}
                        />
                        <InputField
                            label="Cor"
                            name="cor"
                            placeholder="Prata"
                            value={data.cor}
                            error={errors.cor}
                            onChange={handleChange}
                        />
                        <InputField
                            label="Ano"
                            name="ano"
                            placeholder="2023"
                            maxLength={4}
                            value={data.ano}
                            error={errors.ano}
                            onChange={handleChange}
                        />
                    </div>
                </div>

                {/* Mensagem de erro geral */}
                {errors.general && (
                    <div className="text-red-500 text-sm text-center">
                        {errors.general}
                    </div>
                )}

                <button
                    type="submit"
                    className="w-full my-5 bg-gray-800 hover:bg-gray-900 text-white font-bold py-4 rounded-xl transition duration-200 shadow-lg transform hover:scale-105"
                >
                    Finalizar Cadastro
                </button>
            </form>
        </div>
    );
};
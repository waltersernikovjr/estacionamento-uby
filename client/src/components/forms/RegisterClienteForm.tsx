import { InputField } from "../input/InputField";

export const RegisterCliente = () => {
    return (
        <div className="w-full max-w-2xl mx-auto p-6">

            <form className="space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <InputField label="Nome Completo" placeholder="João Silva" />
                    <InputField label="CPF" placeholder="000.111.222-33" />
                    <InputField label="RG" placeholder="12.345.678-9" />
                    <InputField label="E-mail" type="email" placeholder="joao@email.com" />
                    <InputField label="Senha" type="password" placeholder="••••••••" />
                    <InputField label="Confirme a Senha" type="password" placeholder="••••••••" />
                    <InputField label="Endereço" placeholder="Rua A, Bairro B" />
                </div>
                <div className="space-y-5 pt-6 border-t border-gray-700">
                    <h3 className="text-lg font-semibold text-gray-300">Dados do Veículo</h3>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <InputField label="Placa" placeholder="ABC-1234" className="uppercase" />
                        <InputField label="Modelo" placeholder="Civic EXL" />
                        <InputField label="Cor" placeholder="Prata" />
                        <InputField label="Ano" placeholder="2023" maxLength={4} />
                    </div>
                </div>

                <button
                    type="submit"
                    className="w-full bg-gray-800 hover:bg-gray-900 text-white font-bold py-4 rounded-xl transition duration-200 shadow-lg transform hover:scale-105"
                >
                    Finalizar Cadastro
                </button>
            </form>
        </div>
    );
};

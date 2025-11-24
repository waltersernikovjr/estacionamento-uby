import { InputField } from "../input/InputField"

export const RegisterOperador = () => {
    return <form action="" className="flex flex-col">
        <InputField label="CPF" placeholder="000.111.222-33" />
        <InputField label="E-mail" type="email" placeholder="joao@email.com" />
        <InputField label="Senha" type="password" placeholder="••••••••" />
        <button className="w-full my-5 bg-gray-800 hover:bg-gray-900 text-white font-bold py-4 rounded-xl transition duration-200 shadow-lg transform hover:scale-105">Cadastrar</button>
    </form>
}
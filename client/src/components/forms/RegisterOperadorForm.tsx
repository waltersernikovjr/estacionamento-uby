import { InputField } from "../input/InputField"
import { RegisterOperador } from "../../application/RegisterOperador";
import { InmemoryOperadorGateway } from "../../gateway/OperadorGateway";
import { FormHook } from "../../hooks/FormHook";


export const RegisterOperadorForm = () => {
    const {
        formHook: [formData, handleChange, setFormData],
        errorHook: [errors, handleError, setErrors]
    } = FormHook();

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setErrors({});

        const result = await new RegisterOperador(new InmemoryOperadorGateway()).execute(formData);

        if (result.isError()) return handleError(result);

        setFormData({
            nome: "",
            email: "",
            cpf: "",
            password: "",
            confirmPassword: "",
        });
    }

    return <form onSubmit={handleSubmit} className="flex flex-col">
        <InputField label="Nome" name="nome" error={errors.nome} onChange={handleChange} />
        <InputField label="CPF" name="cpf" error={errors.cpf} onChange={handleChange} />
        <InputField label="E-mail" name="email" type="email" error={errors.email} onChange={handleChange} />
        <InputField label="Senha" name="password" type="password" error={errors.password} onChange={handleChange} />
        <InputField label="Confirme a senha" name="confirmPassword" type="password" error={errors.confirmPassword} onChange={handleChange} />
        {errors.general && (
            <div className="text-red-500 text-sm text-center">{errors.general}</div>
        )}
        <button className="w-full my-5 bg-gray-800 hover:bg-gray-900 text-white font-bold py-4 rounded-xl transition duration-200 shadow-lg transform hover:scale-105">Cadastrar</button>
    </form>
}
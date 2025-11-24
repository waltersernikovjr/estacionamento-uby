import { useState } from "react";
import { InputField } from "../input/InputField"
import { RegisterOperador } from "../../application/RegisterOperador";
import { InmemoryOperadorGateway } from "../../gateway/OperadorGateway";
import { InputError } from "../../error/InputError";

type FormData = {
    nome: string;
    email: string;
    cpf: string;
    password: string;
    confirmPassword: string;
};

type Errors = Record<string, string | undefined>

export const RegisterOperadorForm = () => {
    const [formData, setFormData] = useState<Partial<FormData>>({});
    const [errors, setErrors] = useState<Errors>({});


    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        console.log(name);
        console.log(value);

        setFormData((prev) => ({ ...prev, [name]: value }));

        if (errors[name as keyof Errors]) {
            setErrors((prev) => ({ ...prev, [name]: undefined }));
        }
    };
    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setErrors({});

        const result = await new RegisterOperador(new InmemoryOperadorGateway()).execute(formData);

        if (result.isError()) {
            const err = result.error;

            if (err instanceof InputError) {
                console.log(err.getErrors());

                err.getErrors().forEach(({ key, message }) => setErrors((prev) => ({ ...prev, [key as string]: message })));
            } else {
                setErrors({ general: err?.message });
            }

            return;
        }

        // Sucesso!
        alert("Operador cadastrado com sucesso!");
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
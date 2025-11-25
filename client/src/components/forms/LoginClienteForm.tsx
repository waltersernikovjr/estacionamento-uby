import { InputField } from "../input/InputField";
import LoginCliente from "../../application/LoginCliente"; // caso de uso exclusivo
import { useForm } from "../../hooks/FormHook";
import { useDI } from "../../di/DIContext";

type ClienteLoginData = {
    email: string; // pode ser e-mail ou CPF
    password: string;
};

export const LoginClienteForm = () => {

    const { data, errors, handleChange, handleError, reset } =
        useForm<ClienteLoginData>({
            email: "",
            password: "",
        });

    const loginCliente = useDI<LoginCliente>("loginCliente");

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        const payload = { email: data.email.toLowerCase(), password: data.password };

        const result = await loginCliente.execute(payload);

        if (result.isError()) return handleError(result);

        reset();
        // ex: redirecionar para área do cliente
        // router.push("/cliente/perfil");
    };


    return (
        <form onSubmit={handleSubmit} className="flex flex-col space-y-6 max-w-sm mx-auto">
            <h2 className="text-2xl font-bold text-center text-white mb-6">
                Login Cliente
            </h2>

            <InputField
                label={"E-mail"}
                name="email"
                type={"email"}
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

            {errors.general && (
                <div className="text-red-500 text-sm text-center">{errors.general}</div>
            )}

            <button
                type="submit"
                className="w-full my-5 bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl transition duration-200 shadow-lg transform hover:scale-105"
            >
                Entrar como Cliente
            </button>
        </form>
    );
};
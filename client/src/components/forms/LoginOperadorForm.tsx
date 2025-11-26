import LoginOperador from "../../application/LoginOperador"; // caso de uso exclusivo
import { InputField } from "../input/InputField";
import { useForm } from "../../hooks/FormHook";
import { useDI } from "../../di/DIContext";

type OperadorLoginData = {
    cpf: string;
    password: string;
};

export const LoginOperadorForm = () => {
    const { data, errors, handleChange, handleError, reset } = useForm<OperadorLoginData>({
        cpf: "",
        password: "",
    });

    const loginOperador = useDI<LoginOperador>("loginOperador");

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        const result = await loginOperador.execute({
            cpf: data.cpf,
            password: data.password,
        });

        if (result.isError()) return handleError(result);

        reset();
    };

    return (
        <form onSubmit={handleSubmit} className="flex flex-col space-y-6 max-w-sm mx-auto">
            <h2 className="text-2xl font-bold text-center text-white mb-6">
                Login Operador
            </h2>

            <InputField
                label="CPF"
                name="cpf"
                placeholder="000.111.222-33"
                value={data.cpf}
                error={errors.cpf}
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
                className="w-full my-5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl transition duration-200 shadow-lg transform hover:scale-105"
            >
                Entrar como Operador
            </button>
        </form>
    );
};
import { useEffect, useState } from "react";
import type GetVagas from "../application/GetVagas";
import { useDI } from "../di/DIContext";
import type { Vaga } from "../model/Vaga";
import { VagasStatus } from "../enum/VagaStatus";
import { AddSpotForm, type AddSpotFormData } from "../components/forms/CreateVaga";
import type CreateVagas from "../application/CreateVaga";
import LocalStorageUtil from "../util/LocalStorageUtil";
import { Role } from "../enum/Role";
import type { UserProps } from "../model/User";
import type Result from "../util/Result";
import type UpdateVaga from "../application/UpdateVaga";
import { ErrorAlert } from "../components/error/Error";

type Props = {
    vagas: Vaga[];
    onAdd: () => void;
    onUpdate: (vaga: Vaga) => Promise<Result>;
};

export default function ParkingSpotList({ vagas, onAdd, onUpdate }: Props) {
    const [error, setError] = useState<Error>()

    const user = LocalStorageUtil.get('user') as UserProps;

    const handlerVagaUpdate = async (vaga: Vaga) => {
        await onUpdate(vaga);
    }


    return (
        <div className="w-full p-4">
            <div className="flex flex-wrap gap-4">
                {vagas.map((vaga) => (
                    <button
                        onClick={() => handlerVagaUpdate(vaga)}
                        key={vaga.id}
                        className={`
              w-24 h-24 rounded-xl flex items-center justify-center text-xl font-bold
              shadow-md border
              ${vaga.status === VagasStatus.LIVRE
                                ? "bg-green-500 border-green-600 cursor-pointer hover:scale-105"
                                : "bg-red-500 border-red-600 cursor-pointer hover:scale-105"
                            }
            `}
                    >
                        {vaga.numeroDaVaga}
                    </button>
                ))}

                {user.role === Role.OPERADOR ?
                    <button
                        onClick={onAdd}
                        className="
            w-24 h-24 rounded-xl flex items-center justify-center
            text-4xl font-bold text-gray-600
            border border-gray-400 shadow-md
            hover:bg-gray-200 transition
          "
                    >
                        +
                    </button> : <></>}
            </div>
        </div>


    );
}


export const Vagas = () => {
    const getVagas = useDI<GetVagas>('getVagas');
    const createVaga = useDI<CreateVagas>('createVaga');
    const updateVaga = useDI<UpdateVaga>('updateVaga');

    const [vagas, setVagas] = useState<Array<Vaga>>([]);
    const [showForm, setShowForm] = useState<boolean>(false);
    const [loading, setLoading] = useState(true);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const handleError = (error: any) => {
        const msg = error?.message || "Ocorreu um erro inesperado. Tente novamente.";
        setErrorMessage(msg);
        setTimeout(() => setErrorMessage(null), 8000);
    };

    const CreateVaga = async (data: AddSpotFormData) => {
        const result = await createVaga.execute(data);

        if (result.isOk()) await GetVagas();

        return result;
    }

    const UpdateVaga = async (vaga: Vaga) => {
        const result = await updateVaga.execute(vaga);

        if (result.isError()) {
            handleError(result.error);
            console.log(errorMessage);

        } else {
            await GetVagas();
        }

        return result;
    }

    const GetVagas = async () => {
        setLoading(true);

        const list = await getVagas.execute().then(result => result.unwrapOrElse(() => console.log("Error on fetch vagas")));
        setVagas(list);
        setLoading(false);
    }

    useEffect(() => {
        GetVagas();
    }, []);

    if (loading) return <div className="p-10">Carregando...</div>;

    return (
        <>
            {errorMessage && (
                <ErrorAlert message={errorMessage} onClose={() => setErrorMessage(null)} />
            )}
            <div className="flex p-10 flex-col items-center justify-center">
                <h1 className="text-3xl p-10">Vagas</h1>
                <ParkingSpotList vagas={vagas} onAdd={() => setShowForm(true)} onUpdate={UpdateVaga} />
                {showForm && (
                    <AddSpotForm
                        onSubmit={CreateVaga}
                        onClose={() => setShowForm(false)}
                    />
                )}
            </div>
        </>)
}
import { useEffect, useState } from "react";
import type GetVagas from "../application/GetVagas";
import { useDI } from "../di/DIContext";
import type { Vaga } from "../model/Vaga";
import { VagasStatus } from "../enum/VagaStatus";
import { AddSpotForm, type AddSpotFormData } from "../components/forms/CreateVaga";
import type CreateVagas from "../application/CreateVaga";

type Props = {
    vagas: Vaga[];
    onAdd: () => void
};

export default function ParkingSpotList({ vagas, onAdd }: Props) {

    return (
        <div className="w-full p-4">
            <div className="flex flex-wrap gap-4">
                {vagas.map((vaga) => (
                    <div
                        key={vaga.id}
                        className={`
              w-24 h-24 rounded-xl flex items-center justify-center text-xl font-bold
              shadow-md border
              ${vaga.status === VagasStatus.LIVRE
                                ? "bg-green-500 border-green-600"
                                : "bg-red-500 border-red-600"
                            }
            `}
                    >
                        {vaga.numeroDaVaga}
                    </div>
                ))}

                { }
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
                </button>
            </div>
        </div>


    );
}


export const Vagas = () => {
    const getVagas = useDI<GetVagas>('getVagas');
    const createVaga = useDI<CreateVagas>('createVaga');

    const [vagas, setVagas] = useState<Array<Vaga>>([]);
    const [showForm, setShowForm] = useState<boolean>(false);
    const [loading, setLoading] = useState(true);

    const CreateVaga = async (data: AddSpotFormData) => {
        const result = await createVaga.execute(data);

        if (result.isOk()) await GetVagas();

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

    return <div className="flex p-10 flex-col items-center justify-center">
        <h1 className="text-3xl p-10">Vagas</h1>
        <ParkingSpotList vagas={vagas} onAdd={() => setShowForm(true)} />
        {showForm && (
            <AddSpotForm
                onSubmit={CreateVaga}
                onClose={() => setShowForm(false)}
            />
        )}
    </div>
}
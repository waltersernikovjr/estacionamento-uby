import { useState } from "react";
import { useForm } from "../../hooks/FormHook";
import { InputField } from "../input/InputField";
import type Result from "../../util/Result";

export type AddSpotFormData = {
    numeroDaVaga: number;
    preco: number;
    dimensao: string;
};

type Props = {
    onSubmit: (data: AddSpotFormData) => Promise<Result>;
    onClose: () => void;
};

export const AddSpotForm = ({ onSubmit, onClose }: Props) => {
    const { data, errors, handleChange, handleError, reset } = useForm<AddSpotFormData>({
        numeroDaVaga: 0,
        preco: 0,
        dimensao: "",
    });


    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();

        const result = await onSubmit(data);

        if (result.isError()) return handleError(result);

        reset();
    }

    return (
        <div className="fixed inset-0 bg-black/40 flex items-center justify-center">
            <div className="bg-gray-950 p-6 rounded-xl shadow-xl w-80">
                <h2 className="text-xl font-bold mb-4">Adicionar Vaga</h2>

                <form onSubmit={handleSubmit} className="flex flex-col gap-3">

                    <InputField
                        type="text"
                        name="numeroDaVaga"
                        className="border p-2 rounded"
                        value={data.numeroDaVaga}
                        label="Número da vaga"
                        onChange={handleChange}
                        error={errors.numeroDaVaga}
                    />

                    <InputField
                        name="preco"
                        label="Preço"
                        type="text"
                        className="border p-2 rounded"
                        value={data.preco}
                        onChange={handleChange}
                        error={errors.preco}

                    />

                    <InputField
                        name="dimensao"
                        label="Dimensões"
                        type="text"
                        placeholder="ex: 2x4m"
                        className="border p-2 rounded"
                        value={data.dimensao}
                        onChange={handleChange}
                        error={errors.dimensao}

                    />

                    {errors.general && (
                        <div className="text-red-500 text-sm text-center">{errors.general}</div>
                    )}
                    <button className="w-full my-5 bg-gray-800 hover:bg-gray-900 text-white font-bold py-4 rounded-xl transition duration-200 shadow-lg transform hover:scale-105">Salvar</button>


                    <button
                        type="button"
                        onClick={onClose}
                        className="w-full my-5 bg-gray-800 hover:bg-gray-900 text-white font-bold py-4 rounded-xl transition duration-200 shadow-lg transform hover:scale-105"
                    >
                        Cancelar
                    </button>
                </form>
            </div>
        </div>
    );
}

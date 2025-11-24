import React, { useState } from "react";
import { InputError } from "../error/InputError";
import type Result from "../util/Result";

type FormData = Record<string, string | undefined>;

type Errors = Record<string, string | undefined>

type Hook = {
    formHook: [FormData, React.ChangeEventHandler<HTMLInputElement>, React.Dispatch<React.SetStateAction<FormData>>],
    errorHook: [Errors, (result: Result) => void, React.Dispatch<React.SetStateAction<Errors>>],
}

export const FormHook = (): Hook => {
    const [formData, setFormData] = useState<Partial<FormData>>({});
    const [errors, setErrors] = useState<Errors>({});


    const handleChange: React.ChangeEventHandler<HTMLInputElement> = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;

        setFormData((prev) => ({ ...prev, [name]: value }));

        if (errors[name as keyof Errors]) {
            setErrors((prev) => ({ ...prev, [name]: undefined }));
        }
    };

    const handleError = (result: Result) => {
        if (result.isOk()) return;

        const err = result.error;

        if (err instanceof InputError) {

            err.getErrors().forEach(({ key, message }) => setErrors((prev) => ({ ...prev, [key as string]: message })));
        } else {
            setErrors({ general: err?.message });
        }
    }

    return {
        formHook: [formData, handleChange, setFormData],
        errorHook: [errors, handleError, setErrors]
    }
}
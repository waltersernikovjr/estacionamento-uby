import { useState, useCallback } from "react";
import { InputError } from "../error/InputError";
import type Result from "../util/Result";

type ErrorType<T> = Partial<Record<keyof T, string>> & { general?: string };

export function useForm<T extends Record<string, any>>(initialData: T) {
    const [data, setData] = useState<T>(initialData);
    const [errors, setErrors] = useState<ErrorType<T>>({});

    const handleChange = useCallback((e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;

        setData(prev => ({ ...prev, [name]: value }));

        setErrors(prev => {
            if (!prev[name as keyof T]) return prev;

            const copy = { ...prev };
            delete copy[name as keyof T];
            return copy;
        });
    }, []);

    const handleError = useCallback((result: Result) => {
        if (result.isOk()) return;

        const err = result.error;

        if (err instanceof InputError) {
            const map: Record<string, string> = {};
            err.getErrors().forEach(({ key, message }) => {
                if (!key) return;
                map[key] = message;
            });
            setErrors(map as ErrorType<T>);
        } else {
            setErrors({ general: err?.message } as ErrorType<T>);
        }
    }, []);

    const reset = useCallback(() => {
        setData(initialData);
        setErrors({});
    }, [initialData]);

    return {
        data,
        errors,
        handleChange,
        handleError,
        reset,
        setField: (field: keyof T, value: any) =>
            setData(prev => ({ ...prev, [field]: value }))
    };
}

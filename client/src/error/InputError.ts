import type { ValidationError } from "joi";

export class InputError extends Error {
    constructor(readonly message: string, private readonly validationError: ValidationError) {
        super(message);
    }

    static create(validationError: ValidationError) {
        return new InputError("Input Error", validationError);
    }

    public getErrors() {
        return this.validationError.details.map(detail => ({
            key: detail.context?.key,
            label: detail.context?.label,
            message: detail.message
        }))
    }
}
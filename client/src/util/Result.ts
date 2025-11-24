export default class Result<T = any, E = any> {
    private readonly _data?: T;
    private readonly _error?: E;

    constructor(data?: T, err?: E) {
        this._data = data;
        this._error = err
    }

    static Ok<T = any, E = any>(data?: T) {
        return new Result<T, E>(data);
    }

    static Error<T = never, E = any>(error: E) {
        return new Result<T, E>(undefined, error);
    }
    get error() {
        return this._error
    }

    isError() {
        return !!this._error
    }

    unwrapOr(fn: (err?: any) => T) {
        if (this._data) return this._data;

        return fn(this._error);
    }

    unwrapOrThrow(error?: Error) {
        if (this._data) return this._data;

        if (!this._error) return {} as T;

        if (error) throw error;

        throw this._error;
    }

    unwrapOrElse<R>(fn: (err: E) => R): T {
        if (this._data) return this._data;

        fn(this._error as E);

        return {} as T
    }

    unwrap() {
        if (this._data) return this._data;

        if (!this._error) return {} as T;

        throw this._error;
    }
}

export const UnwrapOr = <T, E>(fn: (err?: any) => any) => (res: Result<T, E>) => res.unwrapOr(fn);
export const UnwrapOrThrow = <T, E>(error?: any) => (res: Result<T, E>) => res.unwrapOrThrow(error);
export const UnwrapOrElse = <T, E>(fn: (err: E) => any) => (res: Result<T, E>) => res.unwrapOrElse(fn);
export const Unwrap = <T, E>() => (res: Result<T, E>) => res.unwrap();
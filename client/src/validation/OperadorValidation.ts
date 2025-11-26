import Joi from 'joi';

export const RegisterOperadorValidator = Joi.object({
    nome: Joi.string().required().messages({
        'any.required': 'O nome é obrigatório.',
        'string.empty': 'O nome não pode estar vazio.'
    }),

    email: Joi.string().email().required().messages({
        'string.email': 'O e-mail deve ser válido.',
        'any.required': 'O e-mail é obrigatório.',
        'string.empty': 'O e-mail não pode estar vazio.'
    }),

    cpf: Joi.string().required().messages({
        'any.required': 'O CPF é obrigatório.',
        'string.empty': 'O CPF não pode estar vazio.'
    }),
    password: Joi.string().min(6).required().messages({
        'string.min': 'A senha deve ter no mínimo {#limit} caracteres.',
        'any.required': 'A senha é obrigatória.',
        'string.empty': 'A senha não pode estar vazia.'
    }),

    confirmPassword: Joi.string().required().valid(Joi.ref('password')).messages({
        'any.only': 'A confirmação de senha não coincide com a senha.',
        'any.required': 'A confirmação de senha é obrigatória.',
        'string.empty': 'A confirmação de senha não pode estar vazia.'
    })
});

export const LoginOperadorValidation = Joi.object({
    cpf: Joi.string().required(),
    password: Joi.string().required(),
});
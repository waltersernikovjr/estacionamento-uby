// validators/RegisterClienteValidator.ts
import Joi from 'joi';

export const RegisterClienteValidator = Joi.object({
    nomeCompleto: Joi.string()
        .trim()
        .min(3)
        .required()
        .messages({
            'any.required': 'O nome completo é obrigatório.',
            'string.empty': 'O nome completo não pode estar vazio.',
            'string.min': 'O nome completo deve ter pelo menos {#limit} caracteres.',
        }),

    cpf: Joi.string()
        .trim()
        .pattern(/^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$/)
        .required()
        .messages({
            'any.required': 'O CPF é obrigatório.',
            'string.empty': 'O CPF não pode estar vazio.',
            'string.pattern.base': 'O CPF deve estar no formato válido (ex: 000.111.222-33 ou 00011122233).',
        }),

    rg: Joi.string()
        .trim()
        .min(5)
        .required()
        .messages({
            'any.required': 'O RG é obrigatório.',
            'string.empty': 'O RG não pode estar vazio.',
            'string.min': 'O RG parece estar muito curto.',
        }),

    email: Joi.string()
        .email({ tlds: { allow: false } })
        .lowercase()
        .required()
        .messages({
            'string.email': 'O e-mail deve ser válido.',
            'any.required': 'O e-mail é obrigatório.',
            'string.empty': 'O e-mail não pode estar vazio.',
        }),

    password: Joi.string()
        .min(6)
        .required()
        .messages({
            'string.min': 'A senha deve ter no mínimo {#limit} caracteres.',
            'any.required': 'A senha é obrigatória.',
            'string.empty': 'A senha não pode estar vazia.',
        }),

    confirmPassword: Joi.string()
        .required()
        .valid(Joi.ref('password'))
        .messages({
            'any.only': 'A confirmação de senha não coincide com a senha.',
            'any.required': 'A confirmação de senha é obrigatória.',
            'string.empty': 'A confirmação de senha não pode estar vazia.',
        }),

    endereco: Joi.string()
        .trim()
        .min(5)
        .required()
        .messages({
            'any.required': 'O endereço é obrigatório.',
            'string.empty': 'O endereço não pode estar vazio.',
            'string.min': 'O endereço deve ter pelo menos {#limit} caracteres.',
        }),

    // Dados do veículo
    placa: Joi.string()
        .trim()
        .uppercase()
        .pattern(/^[A-Z]{3}-\d[A-Z0-9]\d{2}$|^[A-Z]{3}\d{4}$/)
        .required()
        .messages({
            'any.required': 'A placa do veículo é obrigatória.',
            'string.empty': 'A placa não pode estar vazia.',
            'string.pattern.base':
                'A placa deve seguir o padrão brasileiro (ex: ABC-1234 ou ABC1D23 - Mercosul).',
        }),

    modelo: Joi.string()
        .trim()
        .min(2)
        .required()
        .messages({
            'any.required': 'O modelo do veículo é obrigatório.',
            'string.empty': 'O modelo não pode estar vazio.',
        }),

    cor: Joi.string()
        .trim()
        .min(3)
        .required()
        .messages({
            'any.required': 'A cor do veículo é obrigatória.',
            'string.empty': 'A cor não pode estar vazia.',
        }),

    ano: Joi.string()
        .trim()
        .pattern(/^\d{4}$/)
        .custom((value, helpers) => {
            const ano = parseInt(value, 10);
            const anoAtual = new Date().getFullYear();
            if (ano < 1886 || ano > anoAtual + 1) {
                return helpers.message({ custom: 'O ano do veículo parece inválido.' });
            }
            return value;
        })
        .required()
        .messages({
            'any.required': 'O ano do veículo é obrigatório.',
            'string.empty': 'O ano não pode estar vazio.',
            'string.pattern.base': 'O ano deve ter 4 dígitos (ex: 2023).',
        }),
});

export const LoginClienteValidation = Joi.object({
    email: Joi.string().required(),
    password: Joi.string().required(),
});
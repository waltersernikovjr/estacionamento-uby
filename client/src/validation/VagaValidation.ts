import Joi from "joi";

export const CreateVagaValidation = Joi.object({
    numeroDaVaga: Joi.number().required(),
    preco: Joi.number().required(),
    dimensao: Joi.string().required()
});
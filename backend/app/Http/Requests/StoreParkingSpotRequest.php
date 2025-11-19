<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreParkingSpotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number' => ['required', 'string', 'max:10', 'unique:parking_spots,number'],
            'type' => ['required', 'string', Rule::in(['regular', 'vip', 'disabled'])],
            'status' => ['sometimes', 'string', Rule::in(['available', 'occupied', 'maintenance', 'reserved'])],
            'hourly_price' => ['sometimes', 'numeric', 'min:0'],
            'width' => ['sometimes', 'numeric', 'min:0'],
            'length' => ['sometimes', 'numeric', 'min:0'],
            'operator_id' => ['sometimes', 'integer', 'exists:operators,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'number.required' => 'O número da vaga é obrigatório',
            'number.unique' => 'Este número de vaga já existe',
            'type.required' => 'O tipo da vaga é obrigatório',
            'type.in' => 'O tipo deve ser: regular, vip ou disabled',
            'status.in' => 'O status deve ser: available, occupied, maintenance ou reserved',
            'hourly_price.numeric' => 'O preço por hora deve ser numérico',
            'hourly_price.min' => 'O preço por hora deve ser maior ou igual a 0',
            'width.numeric' => 'A largura deve ser numérica',
            'width.min' => 'A largura deve ser maior ou igual a 0',
            'length.numeric' => 'O comprimento deve ser numérico',
            'length.min' => 'O comprimento deve ser maior ou igual a 0',
            'operator_id.exists' => 'Operador não encontrado',
        ];
    }

    protected function prepareForValidation()
    {
        // Adicionar valores padrão se não fornecidos
        $this->merge([
            'hourly_price' => $this->input('hourly_price', 5.00), // R$ 5/hora padrão
            'width' => $this->input('width', 2.50), // 2.5 metros padrão
            'length' => $this->input('length', 5.00), // 5 metros padrão
            'status' => $this->input('status', 'available'),
        ]);
    }
}

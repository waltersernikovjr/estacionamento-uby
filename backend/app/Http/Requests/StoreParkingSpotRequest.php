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
            'type' => ['required', 'string', Rule::in(['regular', 'motorcycle', 'disabled', 'electric'])],
            'status' => ['sometimes', 'string', Rule::in(['available', 'occupied', 'maintenance'])],
        ];
    }

    public function messages(): array
    {
        return [
            'number.required' => 'O número da vaga é obrigatório',
            'number.unique' => 'Este número de vaga já existe',
            'type.required' => 'O tipo da vaga é obrigatório',
            'type.in' => 'O tipo deve ser: regular, motorcycle, disabled ou electric',
            'status.in' => 'O status deve ser: available, occupied ou maintenance',
        ];
    }
}

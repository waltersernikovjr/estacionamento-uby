<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'license_plate' => ['required', 'string', 'max:10', 'unique:vehicles,license_plate'],
            'brand' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:30'],
            'type' => ['required', 'string', Rule::in(['car', 'motorcycle', 'truck', 'van'])],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'O cliente é obrigatório',
            'customer_id.exists' => 'Cliente não encontrado',
            'license_plate.required' => 'A placa é obrigatória',
            'license_plate.unique' => 'Esta placa já está cadastrada',
            'brand.required' => 'A marca é obrigatória',
            'model.required' => 'O modelo é obrigatório',
            'color.required' => 'A cor é obrigatória',
            'type.required' => 'O tipo é obrigatório',
            'type.in' => 'O tipo deve ser: car, motorcycle, truck ou van',
        ];
    }
}

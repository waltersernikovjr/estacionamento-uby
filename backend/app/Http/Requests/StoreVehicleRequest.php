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

    protected function prepareForValidation(): void
    {
        if ($this->has('license_plate')) {
            $this->merge([
                'license_plate' => strtoupper(preg_replace('/[^A-Z0-9]/i', '', $this->license_plate)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'license_plate' => [
                'required',
                'string',
                'size:7',
                'regex:/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/',
                Rule::unique('vehicles', 'license_plate')->whereNull('deleted_at'),
            ],
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
            'license_plate.size' => 'A placa deve ter exatamente 7 caracteres',
            'license_plate.regex' => 'Formato de placa inválido. Use o padrão brasileiro (ex: ABC1D23)',
            'license_plate.unique' => 'Esta placa já está cadastrada',
            'brand.required' => 'A marca é obrigatória',
            'model.required' => 'O modelo é obrigatório',
            'color.required' => 'A cor é obrigatória',
            'type.required' => 'O tipo é obrigatório',
            'type.in' => 'O tipo deve ser: car, motorcycle, truck ou van',
        ];
    }
}

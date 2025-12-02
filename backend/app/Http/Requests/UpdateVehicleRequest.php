<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
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
        $vehicleId = $this->route('vehicle');

        return [
            'license_plate' => [
                'sometimes',
                'string',
                'size:7',
                'regex:/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/',
                Rule::unique('vehicles', 'license_plate')
                    ->ignore($vehicleId)
                    ->whereNull('deleted_at'),
            ],
            'brand' => ['sometimes', 'string', 'max:50'],
            'model' => ['sometimes', 'string', 'max:50'],
            'color' => ['sometimes', 'string', 'max:30'],
            'type' => ['sometimes', 'string', Rule::in(['car', 'motorcycle', 'truck', 'van'])],
        ];
    }

    public function messages(): array
    {
        return [
            'license_plate.size' => 'A placa deve ter exatamente 7 caracteres',
            'license_plate.regex' => 'Formato de placa inválido. Use o padrão brasileiro (ex: ABC1D23)',
            'license_plate.unique' => 'Esta placa já está cadastrada',
            'type.in' => 'O tipo deve ser: car, motorcycle, truck ou van',
        ];
    }
}

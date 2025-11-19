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

    public function rules(): array
    {
        $vehicleId = $this->route('vehicle');

        return [
            'license_plate' => [
                'sometimes',
                'string',
                'max:10',
                Rule::unique('vehicles', 'license_plate')->ignore($vehicleId),
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
            'license_plate.unique' => 'Esta placa já está cadastrada',
            'type.in' => 'O tipo deve ser: car, motorcycle, truck ou van',
        ];
    }
}

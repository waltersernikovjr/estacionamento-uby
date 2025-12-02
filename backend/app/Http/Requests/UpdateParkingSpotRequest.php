<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateParkingSpotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $parkingSpotId = $this->route('parking_spot');

        return [
            'number' => [
                'sometimes',
                'string',
                'max:10',
                Rule::unique('parking_spots', 'number')->ignore($parkingSpotId),
            ],
            'type' => ['sometimes', 'string', Rule::in(['regular', 'motorcycle', 'disabled', 'electric'])],
            'status' => ['sometimes', 'string', Rule::in(['available', 'occupied', 'maintenance'])],
        ];
    }

    public function messages(): array
    {
        return [
            'number.unique' => 'Este número de vaga já existe',
            'type.in' => 'O tipo deve ser: regular, motorcycle, disabled ou electric',
            'status.in' => 'O status deve ser: available, occupied ou maintenance',
        ];
    }
}

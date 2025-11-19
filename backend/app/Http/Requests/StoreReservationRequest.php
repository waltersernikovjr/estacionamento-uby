<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'parking_spot_id' => ['required', 'integer', 'exists:parking_spots,id'],
            'entry_time' => ['required', 'date'],
            'expected_exit_time' => ['nullable', 'date', 'after:entry_time'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'O cliente é obrigatório',
            'customer_id.exists' => 'Cliente não encontrado',
            'vehicle_id.required' => 'O veículo é obrigatório',
            'vehicle_id.exists' => 'Veículo não encontrado',
            'parking_spot_id.required' => 'A vaga é obrigatória',
            'parking_spot_id.exists' => 'Vaga não encontrada',
            'entry_time.required' => 'A hora de entrada é obrigatória',
            'entry_time.date' => 'Formato de data inválido',
            'expected_exit_time.date' => 'Formato de data inválido',
            'expected_exit_time.after' => 'A saída esperada deve ser após a entrada',
        ];
    }
}

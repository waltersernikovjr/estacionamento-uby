<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exit_time' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'exit_time.required' => 'A hora de saída é obrigatória',
            'exit_time.date' => 'Formato de data inválido',
        ];
    }
}


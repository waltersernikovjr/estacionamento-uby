<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reservation_id' => ['required', 'integer', 'exists:reservations,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', Rule::in(['credit_card', 'debit_card', 'pix', 'cash'])],
            'status' => ['sometimes', 'string', Rule::in(['pending', 'paid', 'failed', 'refunded'])],
        ];
    }

    public function messages(): array
    {
        return [
            'reservation_id.required' => 'A reserva é obrigatória',
            'reservation_id.exists' => 'Reserva não encontrada',
            'amount.required' => 'O valor é obrigatório',
            'amount.min' => 'O valor deve ser maior que zero',
            'payment_method.required' => 'O método de pagamento é obrigatório',
            'payment_method.in' => 'Método inválido. Use: credit_card, debit_card, pix ou cash',
            'status.in' => 'Status inválido. Use: pending, paid, failed ou refunded',
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['sometimes', 'string', Rule::in(['credit_card', 'debit_card', 'pix', 'cash'])],
            'status' => ['sometimes', 'string', Rule::in(['pending', 'paid', 'failed', 'refunded'])],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.in' => 'Método inválido. Use: credit_card, debit_card, pix ou cash',
            'status.in' => 'Status inválido. Use: pending, paid, failed ou refunded',
        ];
    }
}

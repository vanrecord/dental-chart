<?php

namespace Modules\Payment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ability = $this->isMethod('POST') ? 'payment:payments:create' : 'payment:payments:update';

        return auth()->check() && auth()->user()->can($ability);
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'exists:invoices,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'in:cash,credit_card,bank_transfer,insurance,check'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'in:completed,pending,refunded,failed'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

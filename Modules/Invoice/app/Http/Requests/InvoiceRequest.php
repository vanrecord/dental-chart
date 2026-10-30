<?php

namespace Modules\Invoice\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ability = $this->isMethod('POST') ? 'invoice:invoices:create' : 'invoice:invoices:update';

        return auth()->check() && auth()->user()->can($ability);
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'invoice_date' => ['required', 'date'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'subtotal' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'in:unpaid,partial,paid,cancelled,closed,refunded'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'redirect_to_dental_chart' => ['nullable', 'boolean'],

            // Invoice Items Validation
            'items' => ['nullable', 'array'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.type' => ['required_with:items', 'string', 'in:service,product'],
            'items.*.service_id' => ['required_with:items', 'integer', 'exists:services,id'],
            'items.*.reception_id' => ['nullable', 'integer', 'exists:receptions,id'],
            'items.*.operate_date' => ['required_with:items', 'date'],
            'items.*.tooth_numbers' => ['nullable', 'string', 'max:100'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax' => ['nullable', 'numeric', 'min:0'],
            'items.*.note' => ['nullable', 'string', 'max:500'],
        ];
    }
}

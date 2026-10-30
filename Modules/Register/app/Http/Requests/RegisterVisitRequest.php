<?php

namespace Modules\Register\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('register:register:create');
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'workflow' => ['required', 'in:new_invoice,follow_up'],
            // Required if workflow is follow_up
            'invoice_id' => ['required_if:workflow,follow_up', 'nullable', 'exists:invoices,id'],
            // Optional inputs
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'invoice_date' => ['nullable', 'date'],
            'reception_date' => ['nullable', 'date'],
            'visit_type' => ['nullable', 'string', 'max:50'],
            'chief_complaint' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

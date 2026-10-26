<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DentalChartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'chart_date' => ['required', 'date'],
            'dentition_type' => ['nullable', 'string', 'in:permanent,primary,mixed'],
            'examination_notes' => ['nullable', 'string'],
        ];
    }
}


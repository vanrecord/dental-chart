<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToothFindingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'chart_tooth_id' => ['required', 'exists:chart_teeth,id'],
            'dental_condition_id' => ['nullable', 'exists:dental_conditions,id'],
            'surface' => ['required', 'string', 'in:top,bottom,left,right,center,whole'],
            'custom_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'note' => ['nullable', 'string'],
            'treatment_status' => ['nullable', 'string', 'in:observed,planned,in_progress,completed,referred'],
        ];
    }
}


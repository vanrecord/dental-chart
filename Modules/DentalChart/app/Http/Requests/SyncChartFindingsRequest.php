<?php

namespace Modules\DentalChart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncChartFindingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'examination_notes' => ['nullable', 'string'],
            'dentition_type' => ['nullable', 'string', 'in:permanent,primary,mixed'],
            'findings' => ['present', 'array'],
            'findings.*.tooth' => ['required', 'integer'],
            'findings.*.surface' => ['required', 'string', 'in:top,bottom,left,right,center,whole'],
            'findings.*.condition_id' => ['nullable', 'exists:dental_conditions,id'],
            'findings.*.condition_code' => ['nullable', 'string'],
            'findings.*.color' => ['nullable', 'string'],
            'findings.*.status' => ['nullable', 'string', 'in:observed,planned,in_progress,completed,referred'],
            'findings.*.note' => ['nullable', 'string'],
        ];
    }
}


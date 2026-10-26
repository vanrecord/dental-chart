<?php

namespace Modules\DentalChart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChartToothRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $chartId = $this->input('dental_chart_id');
        $toothId = $this->route('chart_tooth')?->id ?? $this->route('chart_tooth');

        return [
            'dental_chart_id' => ['required', 'exists:dental_charts,id'],
            'tooth_number' => [
                'required',
                'integer',
                'min:11',
                'max:85',
                Rule::unique('chart_teeth', 'tooth_number')
                    ->where(fn ($q) => $q->where('dental_chart_id', $chartId))
                    ->ignore($toothId),
            ],
            'tooth_type' => ['required', 'string', 'in:permanent,primary'],
            'tooth_status' => ['nullable', 'string', 'max:50'],
            'general_notes' => ['nullable', 'string'],
        ];
    }
}

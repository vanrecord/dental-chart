<?php

namespace Modules\DentalCondition\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DentalConditionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $conditionId = $this->route('dental_condition')?->id ?? $this->route('dental_condition');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('dental_conditions', 'code')->ignore($conditionId),
            ],
            'description' => ['nullable', 'string'],
            'default_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'category' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

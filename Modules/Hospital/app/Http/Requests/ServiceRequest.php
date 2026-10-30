<?php

namespace Modules\Hospital\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ability = $this->isMethod('POST')
            ? 'hospital:services:create'
            : 'hospital:services:update';

        return auth()->check() && auth()->user()->can($ability);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->where('type', 'service')->whereNull('deleted_at');
                }),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'commission' => ['nullable', 'numeric', 'min:0'],
            'commission_type' => ['nullable', 'string', Rule::in(['fixed', 'percentage'])],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'The selected category is invalid or is not a service category.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'price' => $this->input('price', 0),
            'cost' => $this->input('cost', 0),
            'discount' => $this->input('discount', 0),
            'commission' => $this->input('commission', 0),
            'commission_type' => $this->input('commission_type', 'fixed'),
            'status' => $this->input('status', 'active'),
        ]);
    }
}


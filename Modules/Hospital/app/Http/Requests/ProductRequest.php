<?php

namespace Modules\Hospital\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ability = $this->isMethod('POST')
            ? 'hospital:products:create'
            : 'hospital:products:update';

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
                    $query->where('type', 'product')->whereNull('deleted_at');
                }),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'qty' => ['nullable', 'integer', 'min:0'],
            'supplier_id' => ['nullable', 'integer'],
            'commission' => ['nullable', 'numeric', 'min:0'],
            'commission_type' => ['nullable', 'string', Rule::in(['fixed', 'percentage'])],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'The selected category is invalid or is not a product category.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'price' => $this->input('price', 0),
            'cost' => $this->input('cost', 0),
            'discount' => $this->input('discount', 0),
            'qty' => $this->input('qty', 0),
            'commission' => $this->input('commission', 0),
            'commission_type' => $this->input('commission_type', 'fixed'),
            'status' => $this->input('status', 'active'),
        ]);
    }
}


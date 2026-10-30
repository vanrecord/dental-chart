<?php

namespace Modules\Setting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClinicSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (
            auth()->user()->is_super_admin || auth()->user()->can('settings:general:edit')
        );
    }

    public function rules(): array
    {
        return [
            'logo' => ['nullable', 'file', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
            'khmer_title' => ['nullable', 'string', 'max:255'],
            'khmer_title_color' => ['nullable', 'string', 'max:20', 'regex:/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/'],
            'english_title' => ['nullable', 'string', 'max:255'],
            'english_title_color' => ['nullable', 'string', 'max:20', 'regex:/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/'],
            'description' => ['nullable', 'string', 'max:1000'],
            'description_color' => ['nullable', 'string', 'max:20', 'regex:/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/'],
            'facebook_account' => ['nullable', 'string', 'max:255'],
            'facebook_page' => ['nullable', 'string', 'max:255', 'url'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:5000'],
            'except_doctor_ids' => ['nullable', 'array'],
            'except_doctor_ids.*' => ['integer', 'exists:users,id'],
            'active_tab' => ['nullable', 'string', 'in:clinic_info,except_doctor'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.image' => 'The clinic logo must be an image file.',
            'logo.mimes' => 'The logo must be a file of type: png, jpg, jpeg, webp.',
            'logo.max' => 'The logo image may not be greater than 2MB.',
            'facebook_page.url' => 'The Facebook page must be a valid URL (e.g. https://facebook.com/yourclinic).',
            'email.email' => 'Please provide a valid email address.',
            'khmer_title_color.regex' => 'The Khmer title color must be a valid hex color code (e.g. #000000).',
            'english_title_color.regex' => 'The English title color must be a valid hex color code (e.g. #000000).',
            'description_color.regex' => 'The description color must be a valid hex color code (e.g. #555555).',
        ];
    }
}


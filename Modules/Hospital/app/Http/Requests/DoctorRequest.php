<?php

namespace Modules\Hospital\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $doctor = $this->route('doctor');
        $doctorId = $doctor?->id;
        $userId = $doctor?->user_id;

        $rules = [
            // User Account Information
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'nullable',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($userId),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'is_active' => ['nullable', 'boolean'],

            // Profile Information
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'remove_photo' => ['nullable', 'boolean'],

            // Doctor Information
            'doctor_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('doctors', 'doctor_code')->ignore($doctorId),
            ],
            'specialization' => ['nullable', 'string', 'max:255'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:100'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:80'],
            'consultation_fee' => ['nullable', 'numeric', 'min:0'],
            'bio' => ['nullable', 'string'],
            'status' => ['nullable', 'boolean'],
        ];

        if ($this->isMethod('POST')) {
            $rules['password'] = ['required', 'string', 'min:6', 'confirmed'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:6', 'confirmed'];
        }

        return $rules;
    }

    /**
     * Custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'display name',
            'username' => 'username',
            'email' => 'email address',
            'password' => 'password',
            'first_name' => 'first name',
            'last_name' => 'last name',
            'doctor_code' => 'doctor code',
            'specialization' => 'specialization',
            'qualification' => 'medical qualification',
            'license_number' => 'license number',
            'experience_years' => 'years of experience',
            'consultation_fee' => 'consultation fee',
        ];
    }
}


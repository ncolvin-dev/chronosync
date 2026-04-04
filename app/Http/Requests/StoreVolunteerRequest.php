<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreVolunteerRequest extends FormRequest
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
        $isUpdate = $this->route('volunteer') !== null;
        $volunteerId = $isUpdate ? $this->route('volunteer')?->id : null;

        return [
            // User account fields
            'email' => [
                'required',
                'email:rfc,dns',
                $isUpdate
                    ? 'unique:users,email,' . ($this->user()->id ?? 'NULL')
                    : 'unique:users,email',
            ],
            'password' => $isUpdate
                ? [
                    'nullable',
                    'confirmed',
                    Password::min(10)
                        ->mixedCase()
                        ->numbers(),
                ]
                : [
                    'required',
                    'confirmed',
                    Password::min(10)
                        ->mixedCase()
                        ->numbers(),
                ],
            'password_confirmation' => $isUpdate ? 'nullable' : 'required',

            // Personal information
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|regex:/^\(\d{3}\)\s?\d{3}-\d{4}$/',
            'date_of_birth' => 'required|date|before_or_equal:today',
            'gender' => 'required|in:male,female,non-binary,prefer_not_to_say,other',

            // Address
            'address_street' => 'required|string|max:255',
            'address_city' => 'required|string|max:100',
            'address_state' => 'required|string|size:2',
            'address_zip' => 'required|regex:/^\d{5}(-\d{4})?$/',

            // Availability
            'availability' => 'nullable|array',
            'availability.*' => 'boolean',

            // Treatment facility (conditional - required if not self-recovery)
            'treatment_facility_id' => 'nullable|exists:facilities,id',
            'is_self_recovery' => 'boolean',
            'clean_date' => 'nullable|date|before_or_equal:today',

            // Skills and preferences
            'certifications' => 'nullable|array',
            'certifications.*' => 'string|max:255',
            'languages' => 'nullable|array',
            'languages.*' => 'string|max:100',
            'bio' => 'nullable|string|max:1000',

            // Emergency contact
            'emergency_contact_name' => 'required|string|max:100',
            'emergency_contact_phone' => 'required|regex:/^\(\d{3}\)\s?\d{3}-\d{4}$/',

            // Consent
            'accepts_terms' => 'required|accepted',
            'accepts_privacy' => 'required|accepted',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.min' => 'Password must be at least 10 characters.',
            'password.mixed_case' => 'Password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Password must contain at least one number.',
            'phone.regex' => 'Phone must be in format (XXX) XXX-XXXX.',
            'date_of_birth.before_or_equal' => 'Date of birth cannot be in the future.',
            'clean_date.before_or_equal' => 'Clean date cannot be in the future.',
            'address_zip.regex' => 'Zip code must be in format XXXXX or XXXXX-XXXX.',
            'emergency_contact_phone.regex' => 'Emergency contact phone must be in format (XXX) XXX-XXXX.',
            'accepts_terms.accepted' => 'You must accept the terms of service.',
            'accepts_privacy.accepted' => 'You must accept the privacy policy.',
        ];
    }
}

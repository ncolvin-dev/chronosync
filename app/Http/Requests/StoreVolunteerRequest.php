<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreVolunteerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->route('volunteer') !== null;

        $emailRule = $isUpdate
            ? 'required|email|unique:users,email,' . ($this->user()?->user_id ?? 'NULL') . ',user_id'
            : 'required|email|unique:users,email';

        $passwordRules = $isUpdate
            ? ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()]
            : ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()];

        return [
            'email'                   => $emailRule,
            'password'                => $passwordRules,
            'password_confirmation'   => $isUpdate ? 'nullable' : 'required',

            'first_name'              => 'required|string|max:100',
            'last_name'               => 'required|string|max:100',
            'phone'                   => ['required', 'regex:/^\(\d{3}\)\s?\d{3}-\d{4}$/'],
            'dob'                     => 'required|date|before_or_equal:today',
            'gender'                  => 'required|in:male,female,non-binary,prefer_not_to_say,other',

            'on_probation'            => 'required|boolean',
            'clean_date'              => 'nullable|date|before_or_equal:today',
            'has_treatment_facility'  => 'nullable|boolean',
            'treatment_facility_name' => 'nullable|string|max:255',
            'discharge_date'          => 'nullable|date',
            'neighborhood'            => 'nullable|string|max:255',
            'bus_line'                => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'            => 'This email address is already registered.',
            'password.min'            => 'Password must be at least 8 characters.',
            'password.mixed_case'     => 'Password must include uppercase and lowercase letters.',
            'password.numbers'        => 'Password must include at least one number.',
            'phone.regex'             => 'Phone must be in format (XXX) XXX-XXXX.',
            'dob.before_or_equal'     => 'Date of birth cannot be in the future.',
            'clean_date.before_or_equal' => 'Clean date cannot be in the future.',
        ];
    }
}

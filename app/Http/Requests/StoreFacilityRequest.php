<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        $roles = is_array($user->roles) ? $user->roles : json_decode($user->roles, true) ?? [];

        return in_array('coordinator', $roles) || in_array('admin', $roles);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $isUpdate = $this->route('facility') !== null;
        $facilityId = $isUpdate ? $this->route('facility')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                $isUpdate
                    ? 'unique:facilities,name,' . $facilityId
                    : 'unique:facilities,name',
            ],
            'facility_type' => 'required|in:treatment,halfway,community_center,hospital,other',
            'description' => 'nullable|string|max:1000',

            // Address (complete)
            'address_street' => 'required|string|max:255',
            'address_city' => 'required|string|max:100',
            'address_state' => 'required|string|size:2',
            'address_zip' => 'required|regex:/^\d{5}(-\d{4})?$/',

            // Contact
            'phone' => 'required|regex:/^\(\d{3}\)\s?\d{3}-\d{4}$/',
            'email' => 'nullable|email:rfc,dns',
            'contact_person' => 'nullable|string|max:255',

            // Hours of operation
            'hours_monday_open' => 'nullable|date_format:H:i',
            'hours_monday_close' => 'nullable|date_format:H:i',
            'hours_tuesday_open' => 'nullable|date_format:H:i',
            'hours_tuesday_close' => 'nullable|date_format:H:i',
            'hours_wednesday_open' => 'nullable|date_format:H:i',
            'hours_wednesday_close' => 'nullable|date_format:H:i',
            'hours_thursday_open' => 'nullable|date_format:H:i',
            'hours_thursday_close' => 'nullable|date_format:H:i',
            'hours_friday_open' => 'nullable|date_format:H:i',
            'hours_friday_close' => 'nullable|date_format:H:i',
            'hours_saturday_open' => 'nullable|date_format:H:i',
            'hours_saturday_close' => 'nullable|date_format:H:i',
            'hours_sunday_open' => 'nullable|date_format:H:i',
            'hours_sunday_close' => 'nullable|date_format:H:i',

            // Capacity
            'capacity' => 'nullable|integer|min:1|max:999',

            // Notes
            'notes' => 'nullable|string|max:2000',

            // Status
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'A facility with this name already exists.',
            'phone.regex' => 'Phone must be in format (XXX) XXX-XXXX.',
            'email.email' => 'Please provide a valid email address.',
            'address_zip.regex' => 'Zip code must be in format XXXXX or XXXXX-XXXX.',
            'hours_*.date_format' => 'Hours must be in HH:MM format.',
            'capacity.integer' => 'Capacity must be a whole number.',
            'capacity.min' => 'Capacity must be at least 1.',
        ];
    }
}

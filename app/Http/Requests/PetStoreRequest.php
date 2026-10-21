<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PetStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        $isPatientRegistrationForm = $this->exists('age_is_approximate');

        $rules = [
            'pet_name' => ['required', 'string', 'max:255'],
            'species' => ['required', 'string', 'max:120'],
            'breed' => ['nullable', 'string', 'max:150'],
            'sex' => ['required', 'in:male,female,unknown'],
            'owner_user_id' => [
                $isPatientRegistrationForm && $this->user()?->isStaffOrAdmin() ? 'required' : 'nullable',
                'integer',
                'exists:users,id',
            ],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'color' => ['nullable', 'string', 'max:150'],
            'is_neutered' => ['sometimes', 'boolean'],
            'microchip_number' => ['nullable', 'string', 'max:100'],
            'allergies' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];

        if ($isPatientRegistrationForm) {
            $rules += [
                'age_is_approximate' => ['required', 'boolean'],
                'date_of_birth' => ['nullable', 'required_unless:age_is_approximate,1', 'date', 'before_or_equal:today'],
                'age_value' => ['nullable', 'required_if:age_is_approximate,1', 'integer', 'min:0', 'max:240'],
                'age_type' => ['nullable', 'required_if:age_is_approximate,1', 'in:month,year'],
            ];
        } else {
            $rules += [
                'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
                'age_value' => ['required', 'integer', 'min:0', 'max:240'],
                'age_type' => ['required', 'in:month,year'],
            ];
        }

        return $rules;
    }
}

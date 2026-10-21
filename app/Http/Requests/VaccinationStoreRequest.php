<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VaccinationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isStaffOrAdmin();
    }

    public function rules(): array
    {
        return [
            'pet_id' => ['nullable', 'integer', 'exists:pets,id'],
            'pet_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'vaccine_name' => ['required', 'string', 'max:255'],
            'date_given' => ['required', 'date', 'before_or_equal:today'],
            'next_due_date' => ['nullable', 'date', 'after_or_equal:date_given'],
        ];
    }
}

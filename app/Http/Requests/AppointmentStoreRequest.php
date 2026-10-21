<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppointmentStoreRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $type = (string) $this->input('type');
        $purpose = (string) $this->input('purpose', '');

        if ($type === Appointment::TYPE_VACCINATION) {
            $selectedVaccine = (string) $this->input('vaccination_purpose', '');
            $purpose = $selectedVaccine === 'Others'
                ? (string) $this->input('other_vaccine', '')
                : $selectedVaccine;
        } elseif ($type === Appointment::TYPE_CHECKUP) {
            $purpose = (string) $this->input('checkup_purpose', '');
        } elseif ($type === Appointment::TYPE_GROOMING) {
            $serviceType = (string) $this->input('grooming_service_type', '');
            $purpose = Appointment::GROOMING_SERVICE_LABELS[$serviceType] ?? $purpose;
        }

        $this->merge(['purpose' => trim($purpose)]);
    }

    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        $isStaffOrAdmin = (bool) $this->user()?->isStaffOrAdmin();
        $appointmentDateRules = ['required', 'date'];

        if ($this->user()?->isClient() && ! $this->isUnchangedPastAppointmentDate()) {
            $appointmentDateRules[] = 'after_or_equal:today';
        }

        return [
            'pet_id' => [
                Rule::requiredIf(fn (): bool => ! $isStaffOrAdmin),
                'nullable',
                'integer',
                'exists:pets,id',
            ],
            'pet_name' => [
                Rule::requiredIf(fn (): bool => $isStaffOrAdmin && ! $this->filled('pet_id')),
                'nullable',
                'string',
                'max:255',
            ],
            'owner_name' => [
                Rule::requiredIf(fn (): bool => $isStaffOrAdmin && ! $this->filled('pet_id')),
                'nullable',
                'string',
                'max:255',
            ],
            'contact_number' => [
                Rule::requiredIf(fn (): bool => $isStaffOrAdmin && ! $this->filled('pet_id')),
                'nullable',
                'string',
                'max:50',
            ],
            'type' => ['required', Rule::in([
                Appointment::TYPE_VACCINATION,
                Appointment::TYPE_CHECKUP,
                Appointment::TYPE_GROOMING,
            ])],
            'appointment_date' => $appointmentDateRules,
            'preferred_time' => [
                Rule::requiredIf(fn (): bool => $this->input('type') === Appointment::TYPE_GROOMING),
                'nullable',
                'date_format:H:i',
            ],
            'purpose' => ['required', 'string', 'max:255'],
            'vaccination_purpose' => [
                Rule::requiredIf(fn (): bool => $this->input('type') === Appointment::TYPE_VACCINATION),
                'nullable',
                Rule::in([...Appointment::VACCINE_PURPOSE_OPTIONS, 'Others']),
            ],
            'other_vaccine' => [
                Rule::requiredIf(fn (): bool => $this->input('type') === Appointment::TYPE_VACCINATION
                    && $this->input('vaccination_purpose') === 'Others'),
                'nullable',
                'string',
                'max:255',
            ],
            'checkup_purpose' => [
                Rule::requiredIf(fn (): bool => $this->input('type') === Appointment::TYPE_CHECKUP),
                'nullable',
                'string',
                'max:255',
            ],
            'grooming_service_type' => [
                Rule::requiredIf(fn (): bool => $this->input('type') === Appointment::TYPE_GROOMING),
                'nullable',
                Rule::in(array_keys(Appointment::GROOMING_SERVICE_LABELS)),
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_emergency' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in([
                Appointment::STATUS_PENDING,
                Appointment::STATUS_APPROVED,
                Appointment::STATUS_COMPLETED,
                Appointment::STATUS_REJECTED,
                Appointment::STATUS_CANCELLED,
            ])],
        ];
    }

    private function isUnchangedPastAppointmentDate(): bool
    {
        $appointment = $this->route('appointment');

        return $appointment instanceof Appointment
            && $appointment->appointment_date?->isBefore(today())
            && $this->input('appointment_date') === $appointment->appointment_date->toDateString();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    public const TYPE_VACCINATION = 'vaccination';
    public const TYPE_CHECKUP = 'checkup';
    public const TYPE_GROOMING = 'grooming';
    // Backward compatibility for older code paths.
    public const TYPE_MEDICAL = self::TYPE_VACCINATION;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    public const GROOMING_SERVICE_BASIC = 'basic_grooming';
    public const GROOMING_SERVICE_FULL = 'full_grooming';
    public const GROOMING_SERVICE_NAIL_TRIM = 'nail_trim';
    public const GROOMING_SERVICE_EAR_CLEANING = 'ear_cleaning';

    public const GROOMING_SERVICE_LABELS = [
        self::GROOMING_SERVICE_BASIC => 'Basic Grooming',
        self::GROOMING_SERVICE_FULL => 'Full Grooming',
        self::GROOMING_SERVICE_NAIL_TRIM => 'Nail Trim',
        self::GROOMING_SERVICE_EAR_CLEANING => 'Ear Cleaning',
    ];

    public const VACCINE_PURPOSE_OPTIONS = [
        'Anti-Rabies',
        '5-in-1 Vaccine',
        '6-in-1 Vaccine',
        'Deworming',
    ];

    protected $fillable = [
        'user_id',
        'pet_id',
        'staff_id',
        'type',
        'appointment_date',
        'preferred_time',
        'purpose',
        'grooming_service_type',
        'notes',
        'status',
        'accepted_at',
        'is_emergency',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'accepted_at' => 'datetime',
            'is_emergency' => 'boolean',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        if ($this->type === self::TYPE_GROOMING) {
            return 'Grooming';
        }

        if ($this->type === self::TYPE_CHECKUP) {
            return 'Checkup';
        }

        return 'Vaccination';
    }

    public function getGroomingServiceLabelAttribute(): ?string
    {
        if (! $this->grooming_service_type) {
            return null;
        }

        return self::GROOMING_SERVICE_LABELS[$this->grooming_service_type] ?? null;
    }

    public function getDisplayPurposeAttribute(): string
    {
        if ($this->type === self::TYPE_GROOMING) {
            return $this->grooming_service_label ?? 'Grooming Service';
        }

        return $this->purpose;
    }

    public function getDisplayPetNameAttribute(): string
    {
        $petName = $this->pet?->pet_name;

        if (! $petName) {
            return 'N/A';
        }

        if ($this->pet?->user_id === null) {
            return $petName.' (Walk-in)';
        }

        return $petName;
    }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        if ($user->isStaffOrAdmin()) {
            return $query;
        }

        return $query->where('user_id', $user->id);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}

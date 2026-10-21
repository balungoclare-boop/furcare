<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Hospitalization extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_DISCHARGED = 'discharged';

    protected $fillable = [
        'pet_id',
        'user_id',
        'admitted_date',
        'discharge_date',
        'notes',
        'status',
        'medication_schedule',
        'discharge_summary',
    ];

    protected function casts(): array
    {
        return [
            'admitted_date' => 'date',
            'discharge_date' => 'date',
        ];
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        if ($user->isStaffOrAdmin()) {
            return $query;
        }

        return $query->where('user_id', $user->id);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function progressNotes(): HasMany
    {
        return $this->hasMany(HospitalizationProgressNote::class)->latest('note_date');
    }
}

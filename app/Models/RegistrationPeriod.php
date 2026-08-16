<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationPeriod extends Model
{
    protected $fillable = [
        'school_level_id',
        'name',
        'start_date',
        'end_date',
        'is_active',
        'max_applicants',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function schoolLevel()
    {
        return $this->belongsTo(SchoolLevel::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function isOpen(?string $date = null): bool
    {
        if (!$this->is_active || !$this->schoolLevel || !$this->schoolLevel->is_active) {
            return false;
        }

        $today = $date ?? now()->toDateString();
        $start = $this->start_date instanceof \Carbon\CarbonInterface ? $this->start_date->toDateString() : (string) $this->start_date;
        $end = $this->end_date instanceof \Carbon\CarbonInterface ? $this->end_date->toDateString() : (string) $this->end_date;

        return $start <= $today && $today <= $end;
    }

    public function registrationStatus(?string $date = null): string
    {
        if (!$this->is_active || !$this->schoolLevel || !$this->schoolLevel->is_active) {
            return 'inactive';
        }

        $today = $date ?? now()->toDateString();
        $start = $this->start_date instanceof \Carbon\CarbonInterface ? $this->start_date->toDateString() : (string) $this->start_date;
        $end = $this->end_date instanceof \Carbon\CarbonInterface ? $this->end_date->toDateString() : (string) $this->end_date;

        if ($today < $start) {
            return 'not_started';
        }
        if ($today > $end) {
            return 'closed';
        }

        return 'open';
    }

    public function scopeOpen($query, ?string $date = null)
    {
        $today = $date ?? now()->toDateString();

        return $query->where('is_active', true)
            ->whereHas('schoolLevel', fn ($q) => $q->where('is_active', true))
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today);
    }
}

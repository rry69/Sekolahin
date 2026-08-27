<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationPeriod extends Model
{
    protected $fillable = [
        'school_level_id',
        'name',
        'academic_year',
        'wave',
        'start_date',
        'end_date',
        'is_active',
        'max_applicants',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
            'wave' => 'integer',
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

    // ── Status otomatis 4 keadaan untuk UI daftar periode ──
    // nonaktif → Belum Dibuka → Sedang Berlangsung → Selesai
    public function computedStatus(?string $date = null): string
    {
        if (!$this->is_active) {
            return 'nonaktif';
        }
        $today = $date ?? now()->toDateString();
        $start = $this->start_date instanceof \Carbon\CarbonInterface ? $this->start_date->toDateString() : (string) $this->start_date;
        $end = $this->end_date instanceof \Carbon\CarbonInterface ? $this->end_date->toDateString() : (string) $this->end_date;

        if ($today < $start) {
            return 'belum_dibuka';
        }
        if ($today > $end) {
            return 'selesai';
        }

        return 'berlangsung';
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'nonaktif' => 'Nonaktif',
            'belum_dibuka' => 'Belum Dibuka',
            'berlangsung' => 'Sedang Berlangsung',
            'selesai' => 'Selesai',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    public static function statusBadgeClass(string $status): string
    {
        return match ($status) {
            'nonaktif' => 'badge-nonaktif',
            'belum_dibuka' => 'badge-belum',
            'berlangsung' => 'badge-berlangsung',
            'selesai' => 'badge-selesai',
            default => 'badge-nonaktif',
        };
    }

    public function remainingQuota(): ?int
    {
        if ($this->max_applicants === null) {
            return null;
        }
        $count = $this->registrations_count ?? $this->registrations()->count();

        return max(0, (int) $this->max_applicants - (int) $count);
    }

    public function quotaLabel(): string
    {
        if ($this->max_applicants === null) {
            $count = $this->registrations_count ?? $this->registrations()->count();

            return 'Tak terbatas · ' . $count . ' pendaftar';
        }
        $count = $this->registrations_count ?? $this->registrations()->count();
        $sisa = $this->remainingQuota();

        return $this->max_applicants . ' / sisa ' . $sisa . ' · ' . $count . ' pendaftar';
    }

    public function durationDays(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }
        $start = $this->start_date instanceof \Carbon\CarbonInterface ? $this->start_date : \Illuminate\Support\Carbon::parse($this->start_date);
        $end = $this->end_date instanceof \Carbon\CarbonInterface ? $this->end_date : \Illuminate\Support\Carbon::parse($this->end_date);

        return (int) $start->diffInDays($end) + 1;
    }

    public function durationLabel(): string
    {
        $days = $this->durationDays();
        if ($days <= 0) {
            return '-';
        }

        return 'Berlangsung selama ' . $days . ' hari';
    }

    public function isCurrentlyRunning(?string $date = null): bool
    {
        return $this->computedStatus($date) === 'berlangsung';
    }

    /**
     * Cek overlap dengan periode aktif lain di jenjang yang sama.
     * Nonaktif tidak dihitung overlap (arsip).
     */
    public static function hasOverlap(int $levelId, string $startDate, string $endDate, ?int $excludeId = null): ?self
    {
        $q = static::where('school_level_id', $levelId)
            ->where('is_active', true)
            ->where(function ($w) use ($startDate, $endDate) {
                $w->where('start_date', '<=', $endDate)
                    ->where('end_date', '>=', $startDate);
            });
        if ($excludeId !== null) {
            $q->where('id', '!=', $excludeId);
        }

        return $q->first();
    }

    public function scopeFilter($query, array $filters = [])
    {
        if (!empty($filters['level'])) {
            $query->where('school_level_id', $filters['level']);
        }
        if (!empty($filters['status'])) {
            $today = now()->toDateString();
            $status = $filters['status'];
            if ($status === 'nonaktif') {
                $query->where('is_active', false);
            } elseif ($status === 'belum_dibuka') {
                $query->where('is_active', true)->whereDate('start_date', '>', $today);
            } elseif ($status === 'berlangsung') {
                $query->where('is_active', true)
                    ->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today);
            } elseif ($status === 'selesai') {
                $query->where('is_active', true)->whereDate('end_date', '<', $today);
            }
        }
        if (!empty($filters['academic_year'])) {
            $query->where('academic_year', $filters['academic_year']);
        }
        if (!empty($filters['q'])) {
            $q = trim($filters['q']);
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', '%' . $q . '%')
                    ->orWhere('academic_year', 'like', '%' . $q . '%')
                    ->orWhere('description', 'like', '%' . $q . '%');
            });
        }

        return $query;
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

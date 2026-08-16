<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $fillable = [
        'school_id',
        'school_level_id',
        'name',
        'code',
        'quota',
        'description',
        'requires_health_test',
        'requires_interview',
        'requires_skill_test',
    ];

    protected function casts(): array
    {
        return [
            'requires_health_test' => 'boolean',
            'requires_interview' => 'boolean',
            'requires_skill_test' => 'boolean',
        ];
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function schoolLevel()
    {
        return $this->belongsTo(SchoolLevel::class);
    }

    public function levelName(): string
    {
        return $this->schoolLevel?->name ?? ($this->school->schoolLevels->first()->name ?? '-');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function trackQuotas()
    {
        return $this->hasMany(MajorTrackQuota::class);
    }

    public function quotaForTrack(int $trackId): ?int
    {
        $specific = $this->trackQuotas->firstWhere('registration_track_id', $trackId);
        if ($specific) {
            return (int) $specific->quota;
        }
        // backfill fallback: jika relasi belum diload, query langsung
        $row = MajorTrackQuota::where('major_id', $this->id)->where('registration_track_id', $trackId)->first();
        return $row ? (int) $row->quota : null;
    }

    public function totalQuotaByTracks(): int
    {
        $loaded = $this->relationLoaded('trackQuotas') ? $this->trackQuotas->sum('quota') : null;
        if ($loaded !== null && $loaded > 0) {
            return (int) $loaded;
        }
        return (int) MajorTrackQuota::where('major_id', $this->id)->sum('quota');
    }
}

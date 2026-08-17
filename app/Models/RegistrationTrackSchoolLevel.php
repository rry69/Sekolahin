<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationTrackSchoolLevel extends Model
{
    protected $table = 'registration_track_school_level';

    protected $fillable = ['registration_track_id', 'school_level_id', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function track()
    {
        return $this->belongsTo(RegistrationTrack::class, 'registration_track_id');
    }

    public function level()
    {
        return $this->belongsTo(SchoolLevel::class, 'school_level_id');
    }

    /**
     * Check whether a track is active for a given school level.
     * Missing row is treated as active (backward-compatible).
     */
    public static function isActive(int $trackId, int $levelId): bool
    {
        $row = static::where('registration_track_id', $trackId)
            ->where('school_level_id', $levelId)
            ->first();

        return $row ? (bool) $row->is_active : true;
    }

    /**
     * Return nested map levelId => [trackId => is_active].
     */
    public static function statusMap(): array
    {
        $rows = static::all();
        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row->school_level_id][(int) $row->registration_track_id] = (bool) $row->is_active;
        }
        return $map;
    }
}

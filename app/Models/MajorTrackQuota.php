<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MajorTrackQuota extends Model
{
    protected $fillable = ['major_id', 'registration_track_id', 'quota'];

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function track()
    {
        return $this->belongsTo(RegistrationTrack::class, 'registration_track_id');
    }
}

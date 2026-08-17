<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationTrack extends Model
{
    protected $fillable = ['name', 'description'];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function levelStatuses()
    {
        return $this->hasMany(RegistrationTrackSchoolLevel::class);
    }
}

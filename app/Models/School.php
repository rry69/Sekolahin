<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = [
        'name',
        'npsn',
        'school_status',
        'accreditation',
        'address',
        'district',
        'city',
        'province',
        'maps_link',
        'phone',
        'whatsapp',
        'email',
        'website',
        'principal_name',
        'logo_path',
        'description',
    ];

    public function schoolLevels()
    {
        return $this->belongsToMany(SchoolLevel::class, 'school_level_school');
    }

    public function majors()
    {
        return $this->hasMany(Major::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function levelsName(): string
    {
        return $this->schoolLevels->pluck('name')->implode(', ');
    }
}

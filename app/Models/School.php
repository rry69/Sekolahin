<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'principal_name',
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

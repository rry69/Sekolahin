<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolLevel extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function registrationPeriods()
    {
        return $this->hasMany(RegistrationPeriod::class);
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, 'school_level_school');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

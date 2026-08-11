<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'nik',
        'nisn',
        'nisn_verification_status',
        'nisn_verified_at',
        'nisn_verified_name',
        'nisn_link',
        'student_number',
        'birth_place',
        'birth_date',
        'gender',
        'address',
        'phone',
        'parent_name',
        'parent_phone',
        'previous_school',
        'religion',
        'rt',
        'rw',
        'village',
        'district',
        'city',
        'province',
        'postal_code',
        'father_name',
        'father_occupation',
        'mother_name',
        'mother_occupation',
        'previous_school_npsn',
        'graduation_year',
    ];

    public function isProfileComplete(): bool
    {
        $required = ['full_name', 'nik', 'nisn', 'birth_place', 'birth_date', 'gender', 'religion', 'address', 'phone', 'father_name', 'mother_name'];
        foreach ($required as $field) {
            if (blank($this->{$field})) {
                return false;
            }
        }
        return true;
    }

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'nisn_verified_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}

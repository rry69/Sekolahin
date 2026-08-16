<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReRegistration extends Model
{
    protected $fillable = [
        'registration_id',
        'parent_name',
        'parent_phone',
        'parent_address',
        'parent_occupation',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'health_info',
        'previous_school_name',
        'previous_school_address',
        'uniform_shirt_size',
        'uniform_pants_size',
        'blood_type',
        'height_cm',
        'weight_kg',
        'status',
        'verification_code',
        'submitted_at',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'height_cm' => 'integer',
            'weight_kg' => 'integer',
        ];
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /** Data tambahan (seragam & fisik) sudah diisi lengkap? */
    public function additionalDataFilled(): bool
    {
        $keys = ['uniform_shirt_size', 'uniform_pants_size', 'blood_type', 'height_cm', 'weight_kg'];
        foreach ($keys as $k) {
            if (blank($this->{$k})) {
                return false;
            }
        }
        return true;
    }

    /**
     * Pastikan stub re-registration ada (kode verifikasi tersedia) untuk kartu offline.
     * Dikembalikan: ReRegistration (baru dibuat / yang sudah ada), atau null jika tabel belum punya baris.
     */
    public static function ensureStubFor(Registration $registration): ?self
    {
        $reReg = $registration->reRegistration;

        if (! $reReg) {
            do {
                $code = Str::upper(Str::random(8));
            } while (self::where('verification_code', $code)->exists());

            // Legacy columns (parent_*, emergency_*, previous_school_*, health_info) tetap NOT NULL di DB
            // jadi isi default agar insert tidak gagal — tidak ditampilkan di form lagi.
            $a = $registration->applicant;
            $reReg = self::create([
                'registration_id' => $registration->id,
                'parent_name' => $a->parent_name ?? $a->father_name ?? '-',
                'parent_phone' => $a->parent_phone ?? $a->phone ?? '-',
                'parent_address' => $a->address ?? '-',
                'parent_occupation' => null,
                'emergency_contact_name' => '-',
                'emergency_contact_phone' => '-',
                'emergency_contact_relation' => '-',
                'status' => 'pending',
                'verification_code' => $code,
                'submitted_at' => null,
                'verified_at' => null,
            ]);
            $registration->setRelation('reRegistration', $reReg);
        } elseif (blank($reReg->verification_code)) {
            do {
                $code = Str::upper(Str::random(8));
            } while (self::where('verification_code', $code)->exists());
            $reReg->update(['verification_code' => $code]);
        }

        return $reReg;
    }
}

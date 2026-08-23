<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = [
        'applicant_id',
        'registration_period_id',
        'registration_track_id',
        'school_id',
        'major_id',
        'final_major_id',
        'registration_number',
        'status',
        'payment_status',
        'payment_amount',
        'documents_verified_at',
        'verified_by',
        'verified_notes',
        'notes',
        'deadline_at',
        'canceled_at',
        'withdrawn_at',
    ];

    protected function casts(): array
    {
        return [
            'documents_verified_at' => 'datetime',
            'payment_amount' => 'decimal:2',
            'deadline_at' => 'datetime',
            'canceled_at' => 'datetime',
            'withdrawn_at' => 'datetime',
        ];
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function registrationPeriod()
    {
        return $this->belongsTo(RegistrationPeriod::class);
    }

    public function registrationTrack()
    {
        return $this->belongsTo(RegistrationTrack::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function finalMajor()
    {
        return $this->belongsTo(Major::class, 'final_major_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function documents()
    {
        return $this->hasMany(RegistrationDocument::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function reRegistration()
    {
        return $this->hasOne(ReRegistration::class);
    }

    public function isDeadlineExpired(): bool
    {
        return $this->deadline_at && now()->gt($this->deadline_at);
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }

    public function isWithdrawn(): bool
    {
        return $this->status === 'withdrawn';
    }

    /**
     * Apakah pendaftaran sudah diterima (termasuk yang sudah daftar ulang).
     */
    public function isAccepted(): bool
    {
        return in_array($this->status, ['accepted', 're_registration_complete']);
    }

    /**
     * Label status pendaftaran dalam Bahasa Indonesia.
     */
    public static function statusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
            'accepted' => 'Diterima',
            're_registration_complete' => 'Daftar Ulang Selesai',
            'canceled' => 'Dibatalkan',
            'withdrawn' => 'Mengundurkan Diri',
            default => $status ? ucfirst(str_replace('_', ' ', $status)) : '-',
        };
    }

    public function getDeadlineHoursRemaining(): ?int
    {
        if (!$this->deadline_at) {
            return null;
        }

        $diff = now()->diffInHours($this->deadline_at);

        return $diff > 0 ? $diff : 0;
    }

    public function getDeadlineLabel(): string
    {
        if (!$this->deadline_at) {
            return '-';
        }

        $hours = $this->getDeadlineHoursRemaining();

        if ($hours === null) {
            return '-';
        }

        if ($hours > 24) {
            $days = floor($hours / 24);
            return $days . ' hari';
        }

        return $hours . ' jam';
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['canceled', 'withdrawn', 'accepted', 're_registration_complete']);
    }

    /**
     * Jenis dokumen wajib sesuai jenjang & jalur (revisi.md).
     */
    public function requiredDocumentTypes(): array
    {
        $required = ['foto', 'kartu_keluarga', 'akta_lahir', 'rapor'];

        $levelName = $this->registrationPeriod?->schoolLevel?->name ?? '';
        if (in_array($levelName, ['SMA', 'SMK'])) {
            $required[] = 'ijazah_skl';
        }

        $trackName = $this->registrationTrack?->name ?? '';
        if (strtolower($trackName) === 'prestasi') {
            $required[] = 'sertifikat_prestasi';
        } elseif (strtolower($trackName) === 'beasiswa') {
            $required[] = 'surat_keterangan_tidak_mampu';
        }

        return $required;
    }

    /**
     * Semua dokumen wajib sudah diupload DAN diverifikasi panitia?
     */
    public function hasAllDocumentsVerified(): bool
    {
        $required = $this->requiredDocumentTypes();
        if (empty($required)) {
            return false;
        }

        $verifiedTypes = $this->documents()
            ->whereIn('document_type', $required)
            ->whereNotNull('verified_at')
            ->distinct()
            ->pluck('document_type')
            ->all();

        return count(array_diff($required, $verifiedTypes)) === 0;
    }
}

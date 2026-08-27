<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'ip_address',
        'user_agent',
        'properties',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Label aksi ramah manusia dalam Bahasa Indonesia.
     */
    public function label(): string
    {
        return static::$labels[$this->action] ?? $this->humanize($this->action);
    }

    /**
     * Kategori log untuk pengelompokan visual.
     */
    public function category(): string
    {
        $category = explode('.', $this->action)[0] ?? $this->action;
        $map = [
            'payment' => 'Pembayaran',
            'document' => 'Dokumen',
            'registration' => 'Pendaftaran',
            're_registration' => 'Daftar Ulang',
            'auth' => 'Autentikasi',
            'account' => 'Akun',
            'applicant' => 'Pendaftar',
            'track' => 'Jalur',
        ];

        return $map[$category] ?? ucfirst(str_replace('_', ' ', $category));
    }

    /**
     * Kelas CSS badge sesuai tingkat kepentingan aksi.
     */
    public function badgeClass(): string
    {
        if (in_array($this->action, static::$danger, true)) {
            return 'status-rejected';
        }

        if (in_array($this->action, static::$success, true)) {
            return 'status-accepted';
        }

        if (in_array($this->action, static::$warning, true)) {
            return 'status-pending';
        }

        return 'status-verified';
    }

    /**
     * Apakah baris ini perlu disorot (high-impact action).
     */
    public function isHighlight(): bool
    {
        return in_array($this->action, array_merge(static::$danger, static::$success), true);
    }

    /**
     * Ikon Font Awesome untuk badge.
     */
    public function icon(): string
    {
        $iconMap = [
            'payment' => 'fa-money-bill-transfer',
            'document' => 'fa-file-circle-check',
            'registration' => 'fa-user-check',
            're_registration' => 'fa-user-pen',
            'auth' => 'fa-right-to-bracket',
            'account' => 'fa-user-gear',
            'applicant' => 'fa-user',
            'track' => 'fa-route',
        ];
        $category = explode('.', $this->action)[0] ?? $this->action;

        return $iconMap[$category] ?? 'fa-circle-info';
    }

    /**
     * Nama tampilan user (fallback bila user sudah dihapus).
     */
    public function userName(): string
    {
        if ($this->user) {
            return $this->user->name;
        }

        $props = $this->properties;
        if (is_array($props) && isset($props['user_name'])) {
            return $props['user_name'];
        }

        return 'Sistem';
    }

    /**
     * Alamat IP yang aman untuk ditampilkan.
     */
    public function displayIp(): string
    {
        $ip = trim((string) $this->ip_address);

        if ($ip === '' || $ip === '127.0.0.1' || $ip === '::1') {
            return 'Lokal';
        }

        return $ip;
    }

    protected static function humanize(string $action): string
    {
        return ucfirst(str_replace(['_', '.'], ' ', $action));
    }

    /**
     * Daftar label Bahasa Indonesia per action.
     */
    protected static $labels = [
        'auth.login' => 'Login',
        'auth.logout' => 'Logout',
        'auth.register' => 'Daftar Akun',
        'account.delete' => 'Hapus Akun',
        'account.reset_password' => 'Reset Password',
        'applicant.profile_update' => 'Perbarui Profil',
        'document.upload' => 'Unggah Dokumen',
        'document.verify' => 'Verifikasi Dokumen',
        'document.unverify' => 'Batalkan Verifikasi Dokumen',
        'document.reject' => 'Tolak Dokumen',
        'payment.create_online' => 'Buat Pembayaran Online',
        'payment.upload_proof' => 'Unggah Bukti Bayar',
        'payment.verify' => 'Verifikasi Pembayaran',
        'payment.reset' => 'Reset Pembayaran',
        're_registration.submit' => 'Kirim Daftar Ulang',
        're_registration.verify_code' => 'Verifikasi Kode Daftar Ulang',
        'registration.create' => 'Buat Pendaftaran',
        'registration.verify' => 'Verifikasi Pendaftaran',
        'registration.accepted' => 'Diterima',
        'registration.payment_status' => 'Update Status Pembayaran',
        'registration.reset' => 'Reset Pendaftaran',
        'registration.withdraw' => 'Mengundurkan Diri',
        'track_toggle' => 'Ubah Status Jalur',
    ];

    /**
     * Aksi berdampak tinggi (merah).
     */
    protected static $danger = [
        'account.delete',
        'registration.reset',
        'document.reject',
        'registration.withdraw',
    ];

    /**
     * Aksi sukses/uang (hijau).
     */
    protected static $success = [
        'payment.create_online',
        'payment.upload_proof',
        'payment.verify',
        're_registration.verify_code',
        'registration.accepted',
        're_registration.submit',
    ];

    /**
     * Aksi perubahan status (kuning).
     */
    protected static $warning = [
        'registration.verify',
        'registration.payment_status',
        'document.verify',
        'document.unverify',
        'track_toggle',
        'applicant.profile_update',
        'account.reset_password',
        'payment.reset',
    ];
}

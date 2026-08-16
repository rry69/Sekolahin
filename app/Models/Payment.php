<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'registration_id',
        'payment_type',
        'amount',
        'payment_method',
        'proof_file',
        'status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'notes',
        'xendit_invoice_id',
        'xendit_invoice_url',
        'external_id',
        'xendit_payment_method',
        'xendit_paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'verified_at' => 'datetime',
            'xendit_paid_at' => 'datetime',
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

    /**
     * Pembayaran online yang belum lunas (halaman Xendit ditutup sebelum selesai).
     * Bukan tagihan yang mengikat: disembunyikan dari riwayat dan tidak memblokir
     * tombol bayar. Status pendaftaran tetap 'unpaid' sampai Xendit mengonfirmasi.
     */
    public static function isAbandonedOnline(?Payment $p): bool
    {
        if (! $p || $p->payment_method !== 'online') return false;
        if ($p->xendit_paid_at !== null) return false;
        if ($p->proof_file) return false;
        return in_array($p->status, ['pending', 'rejected'], true);
    }

    public function isPendingInvoice(): bool
    {
        return self::isAbandonedOnline($this) || ($this->status === 'pending' && $this->payment_method === 'online');
    }
}

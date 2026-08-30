<?php

namespace App\Support;

/**
 * Sumber tunggal untuk label & warna badge status pendaftaran/pembayaran.
 *
 * Dipakai di SEMUA view (siswa & admin) agar tampilan konsisten dan mudah
 * diubah di satu tempat. Jangan hardcode mapping status di view lagi.
 */
class StatusBadge
{
    /** Label status pendaftaran (Bahasa Indonesia). */
    public static function registrationStatusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
            'accepted' => 'Diterima',
            're_registration_complete' => 'Daftar Ulang Selesai',
            'canceled' => 'Dibatalkan',
            'withdrawn' => 'Mundur Diri',
            'completed' => 'Selesai',
            default => $status ? ucfirst(str_replace('_', ' ', $status)) : '-',
        };
    }

    /** Kelas Tailwind untuk badge status pendaftaran. */
    public static function registrationStatusClass(?string $status): string
    {
        return match ($status) {
            'pending' => 'bg-transparent border border-yellow-300 text-yellow-700',
            'verified' => 'bg-transparent border border-blue-300 text-blue-700',
            'rejected' => 'bg-transparent border border-red-300 text-red-700',
            'accepted' => 'bg-transparent border border-green-300 text-green-700',
            're_registration_complete' => 'bg-transparent border border-purple-300 text-purple-700',
            'canceled' => 'bg-transparent border border-gray-300 text-gray-600',
            'withdrawn' => 'bg-transparent border border-orange-300 text-orange-700',
            'completed' => 'bg-transparent border border-green-300 text-green-700',
            default => 'bg-transparent border border-gray-300 text-gray-600',
        };
    }

    /**
     * Label status pembayaran (Bahasa Indonesia).
     */
    public static function paymentStatusLabel(?string $status): string
    {
        return match ($status) {
            'unpaid' => 'Belum Dibayar',
            'pending' => 'Menunggu Konfirmasi',
            'paid' => 'Lunas',
            'failed' => 'Gagal',
            'verified' => 'Lunas',
            'rejected' => 'Ditolak',
            default => $status ? ucfirst(str_replace('_', ' ', $status)) : '-',
        };
    }

    /** Kelas Tailwind untuk badge status pembayaran. */
    public static function paymentStatusClass(?string $status): string
    {
        return match ($status) {
            'unpaid' => 'bg-transparent border border-gray-300 text-gray-600',
            'pending' => 'bg-transparent border border-yellow-300 text-yellow-700',
            'paid', 'verified' => 'bg-transparent border border-green-300 text-green-700',
            'failed', 'rejected' => 'bg-transparent border border-red-300 text-red-700',
            default => 'bg-transparent border border-gray-300 text-gray-600',
        };
    }

    /**
     * Kartu ringkas status (dipakai kartu statistik dashboard).
     * Mengembalikan ['label', 'icon', 'cls'].
     */
    public static function registrationStatusCard(?string $status): array
    {
        return match ($status) {
            'pending' => ['label' => 'Menunggu Verifikasi', 'icon' => 'clock-01', 'cls' => 'bg-transparent border border-blue-300 text-blue-600'],
            'verified' => ['label' => 'Terverifikasi', 'icon' => 'checkmark-circle-02', 'cls' => 'bg-transparent border border-indigo-300 text-indigo-600'],
            'accepted' => ['label' => 'Diterima', 'icon' => 'checkmark-circle-02', 'cls' => 'bg-transparent border border-emerald-300 text-emerald-600'],
            're_registration_complete' => ['label' => 'Terdaftar', 'icon' => 'checkmark-badge-01', 'cls' => 'bg-transparent border border-purple-300 text-purple-600'],
            'rejected' => ['label' => 'Ditolak', 'icon' => 'cancel-circle', 'cls' => 'bg-transparent border border-red-300 text-red-600'],
            'canceled' => ['label' => 'Dibatalkan', 'icon' => 'cancel-01', 'cls' => 'bg-transparent border border-gray-300 text-gray-500'],
            'withdrawn' => ['label' => 'Mundur Diri', 'icon' => 'walking', 'cls' => 'bg-transparent border border-orange-300 text-orange-600'],
            default => ['label' => ucfirst(str_replace('_', ' ', (string) $status)), 'icon' => 'help-circle', 'cls' => 'bg-transparent border border-gray-300 text-gray-600'],
        };
    }

    /** Kartu ringkas pembayaran (dipakai kartu statistik dashboard). */
    public static function paymentStatusCard(?string $status): array
    {
        return match ($status) {
            'unpaid' => ['label' => 'Belum Dibayar', 'icon' => 'credit-card', 'cls' => 'bg-transparent border border-gray-300 text-gray-500'],
            'pending' => ['label' => 'Menunggu Konfirmasi', 'icon' => 'clock-01', 'cls' => 'bg-transparent border border-yellow-300 text-yellow-600'],
            'paid' => ['label' => 'Lunas', 'icon' => 'checkmark-circle-02', 'cls' => 'bg-transparent border border-emerald-300 text-emerald-600'],
            'failed' => ['label' => 'Gagal', 'icon' => 'cancel-circle', 'cls' => 'bg-transparent border border-red-300 text-red-600'],
            default => ['label' => ucfirst(str_replace('_', ' ', (string) $status)), 'icon' => 'credit-card', 'cls' => 'bg-transparent border border-gray-300 text-gray-500'],
        };
    }
}

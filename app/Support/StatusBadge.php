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
            'pending' => 'bg-yellow-100 text-yellow-800',
            'verified' => 'bg-blue-100 text-blue-800',
            'rejected' => 'bg-red-100 text-red-800',
            'accepted' => 'bg-green-100 text-green-800',
            're_registration_complete' => 'bg-purple-100 text-purple-800',
            'canceled' => 'bg-gray-300 text-gray-700',
            'withdrawn' => 'bg-orange-100 text-orange-800',
            'completed' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
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
            'unpaid' => 'bg-gray-100 text-gray-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'paid', 'verified' => 'bg-green-100 text-green-800',
            'failed', 'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Kartu ringkas status (dipakai kartu statistik dashboard).
     * Mengembalikan ['label', 'icon', 'cls'].
     */
    public static function registrationStatusCard(?string $status): array
    {
        return match ($status) {
            'pending' => ['label' => 'Menunggu Verifikasi', 'icon' => 'fa-clock', 'cls' => 'bg-blue-50 text-blue-600'],
            'verified' => ['label' => 'Terverifikasi', 'icon' => 'fa-circle-check', 'cls' => 'bg-indigo-50 text-indigo-600'],
            'accepted' => ['label' => 'Diterima', 'icon' => 'fa-circle-check', 'cls' => 'bg-emerald-50 text-emerald-600'],
            're_registration_complete' => ['label' => 'Terdaftar', 'icon' => 'fa-flag-checkered', 'cls' => 'bg-purple-50 text-purple-600'],
            'rejected' => ['label' => 'Ditolak', 'icon' => 'fa-circle-xmark', 'cls' => 'bg-red-50 text-red-600'],
            'canceled' => ['label' => 'Dibatalkan', 'icon' => 'fa-ban', 'cls' => 'bg-gray-100 text-gray-500'],
            'withdrawn' => ['label' => 'Mundur Diri', 'icon' => 'fa-person-walking-arrow-right', 'cls' => 'bg-orange-50 text-orange-600'],
            default => ['label' => ucfirst(str_replace('_', ' ', (string) $status)), 'icon' => 'fa-circle-question', 'cls' => 'bg-gray-50 text-gray-600'],
        };
    }

    /** Kartu ringkas pembayaran (dipakai kartu statistik dashboard). */
    public static function paymentStatusCard(?string $status): array
    {
        return match ($status) {
            'unpaid' => ['label' => 'Belum Dibayar', 'icon' => 'fa-credit-card', 'cls' => 'bg-gray-50 text-gray-500'],
            'pending' => ['label' => 'Menunggu Konfirmasi', 'icon' => 'fa-clock', 'cls' => 'bg-yellow-50 text-yellow-600'],
            'paid' => ['label' => 'Lunas', 'icon' => 'fa-circle-check', 'cls' => 'bg-emerald-50 text-emerald-600'],
            'failed' => ['label' => 'Gagal', 'icon' => 'fa-circle-xmark', 'cls' => 'bg-red-50 text-red-600'],
            default => ['label' => ucfirst(str_replace('_', ' ', (string) $status)), 'icon' => 'fa-credit-card', 'cls' => 'bg-gray-50 text-gray-500'],
        };
    }
}

<?php

namespace App\Observers;

use App\Models\Registration;
use App\Notifications\StatusChanged;
use Illuminate\Support\Facades\Log;

class RegistrationObserver
{
    public function updated(Registration $registration): void
    {
        $message = $this->buildMessage($registration);

        if ($message === '') {
            return;
        }

        $user = $registration->applicant?->user;

        if (! $user) {
            return;
        }

        $user->notify(new StatusChanged($registration, $message));
    }

    private function buildMessage(Registration $registration): string
    {
        $status = $registration->wasChanged('status') ? $registration->status : null;
        $paymentStatus = $registration->wasChanged('payment_status') ? $registration->payment_status : null;

        $statusMessage = match ($status) {
            'verified' => $this->verifiedWithFeeMessage($registration),
            'rejected' => 'Berkas Anda ditolak. ' . ($registration->verified_notes ?? 'Silakan hubungi panitia.'),
            'accepted' => $this->acceptedMessage($registration),
            'canceled' => 'Pendaftaran Anda dibatalkan karena melewati batas waktu.',
            'withdrawn' => 'Pendaftaran Anda telah dibatalkan (mengundurkan diri). Jika ini sebuah kesalahan, silakan hubungi panitia.',
            're_registration_complete' => 'Daftar ulang Anda telah diselesaikan. Silakan cetak bukti daftar ulang di halaman pendaftaran.',
            default => '',
        };

        $paymentMessage = match ($paymentStatus) {
            'paid' => $this->paidThankYouMessage($registration),
            'failed' => 'Pembayaran Anda ditolak. Silakan hubungi panitia.',
            default => '',
        };

        if ($status === 'accepted' && $paymentStatus === 'paid') {
            return $this->acceptedMessage($registration);
        }

        return trim(implode(' ', array_filter([$statusMessage, $paymentMessage])));
    }

    private function verifiedWithFeeMessage(Registration $registration): string
    {
        $regNumber = $registration->registration_number;
        $trackName = $registration->registrationTrack?->name ?? 'pilihan Anda';
        $amount = $registration->payment_amount;

        // Satu-satunya patokan: payment_amount (null = belum ditetapkan, 0 = gratis, >0 = bayar).
        if ($amount === null) {
            return "Selamat! Seluruh dokumen pendaftaran Anda ({$regNumber}) telah berhasil diverifikasi oleh panitia untuk jalur {$trackName}. Besaran biaya pendaftaran akan segera ditentukan oleh panitia dan diinformasikan melalui halaman pendaftaran serta email selanjutnya. Silakan pantau akun Anda secara berkala.";
        }

        if ((float) $amount == 0.0) {
            return "Selamat! Seluruh dokumen pendaftaran Anda ({$regNumber}) telah berhasil diverifikasi oleh panitia. Kabar baik — biaya pendaftaran untuk jalur {$trackName} Anda GRATIS, tidak perlu melakukan pembayaran. Silakan menunggu informasi tahapan selanjutnya di halaman pendaftaran.";
        }

        $formatted = 'Rp ' . number_format((float) $amount, 0, ',', '.');

        return "Selamat! Seluruh dokumen pendaftaran Anda ({$regNumber}) telah berhasil diverifikasi oleh panitia. Silakan lakukan pembayaran biaya pendaftaran sebesar {$formatted} untuk jalur {$trackName} melalui halaman pendaftaran (unggah bukti transfer/cash atau pembayaran online via Xendit). Setelah pembayaran diverifikasi, status Anda akan diproses ke tahap selanjutnya.";
    }

    private function acceptedMessage(Registration $registration): string
    {
        $majorName = $registration->finalMajor?->name ?? $registration->major?->name ?? 'pilihan Anda';
        $regNumber = $registration->registration_number;

        return "Selamat, Anda DITERIMA di {$majorName}! Pendaftaran {$regNumber} telah memenuhi verifikasi berkas dan pembayaran lunas. Silakan lakukan Daftar Ulang di halaman pendaftaran (isi data orang tua/wali) dan cetak bukti daftar ulang setelahnya. Jika butuh bantuan, hubungi panitia.";
    }

    private function paidThankYouMessage(Registration $registration): string
    {
        $regNumber = $registration->registration_number;
        $amount = $registration->payment_amount;
        $formatted = $amount !== null && (float) $amount > 0
            ? 'Rp ' . number_format((float) $amount, 0, ',', '.')
            : 'biaya pendaftaran';

        $base = "Terima kasih! Pembayaran {$formatted} untuk pendaftaran {$regNumber} telah kami terima dan berhasil diverifikasi.";

        $registration->loadMissing('registrationTrack');
        $needsReReg = $registration->status === 'accepted' || ($registration->status === 'verified' && $registration->payment_status === 'paid');

        if ($needsReReg) {
            return $base . ' Seluruh persyaratan telah terpenuhi — silakan lakukan Daftar Ulang di halaman pendaftaran untuk menyelesaikan penerimaan.';
        }

        return $base . ' Pendaftaran Anda akan segera kami proses ke tahap selanjutnya. Silakan pantau halaman pendaftaran untuk perkembangan status.';
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Registration;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CancelExpiredRegistrations extends Command
{
    protected $signature = 'registrations:cancel-expired {--dry-run : Tampilkan yang akan dibatalkan tanpa menyimpan}';

    protected $description = 'Batalkan pendaftaran yang melebihi batas waktu upload berkas atau pembayaran';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $registrationsToCancel = Registration::where('status', 'pending')
            ->where('payment_status', 'unpaid')
            ->whereNotNull('deadline_at')
            ->where('deadline_at', '<', now())
            ->get();

        if ($registrationsToCancel->isEmpty()) {
            $this->info('Tidak ada pendaftaran yang melewati batas waktu.');
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$registrationsToCancel->count()} pendaftaran yang melewati batas waktu.");

        foreach ($registrationsToCancel as $registration) {
            if ($isDryRun) {
                $applicantName = $registration->applicant->full_name ?? '-';
                $this->line("  [DRY-RUN] {$registration->registration_number} - {$applicantName}");
                continue;
            }

            DB::transaction(function () use ($registration) {
                $registration->update([
                    'status' => 'canceled',
                    'canceled_at' => now(),
                ]);

                $applicantName = $registration->applicant->full_name ?? '-';
                $this->line("  Dibatalkan: {$registration->registration_number} - {$applicantName}");
            });
        }

        if (!$isDryRun) {
            $this->info('Pendaftaran yang melewati batas waktu berhasil dibatalkan.');
        }

        return self::SUCCESS;
    }
}

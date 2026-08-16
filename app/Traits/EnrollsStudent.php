<?php

namespace App\Traits;

use App\Models\Registration;
use Illuminate\Support\Facades\DB;

trait EnrollsStudent
{
    /**
     * Terima otomatis pendaftar begitu berkas diverifikasi (status=verified)
     * dan pembayaran lunas (payment_status=paid). Terbitkan NIS sekali.
     */
    protected function enrollIfReady(Registration $registration): void
    {
        if (in_array($registration->status, ['canceled', 'accepted', 're_registration_complete'])) {
            return;
        }

        if ($registration->status !== 'verified' || $registration->payment_status !== 'paid') {
            return;
        }

        DB::transaction(function () use ($registration) {
            $registration->update([
                'status' => 'accepted',
                'final_major_id' => $registration->final_major_id ?? $registration->major_id,
            ]);
            $this->issueNis($registration);
        });
    }

    protected function issueNis(Registration $registration): void
    {
        $applicant = $registration->applicant;

        if ($applicant->student_number) {
            return;
        }

        $year = date('Y');
        $maxRetries = 10;

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            try {
                $count = $applicant->where('student_number', 'like', "{$year}-%")->count() + $attempt + 1;
                $studentNumber = sprintf('%s-%04d', $year, $count);

                $applicant->update(['student_number' => $studentNumber]);
                return;
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                if ($attempt === $maxRetries - 1) {
                    throw $e;
                }
            }
        }
    }
}

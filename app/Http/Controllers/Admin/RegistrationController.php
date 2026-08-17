<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\RegistrationPeriod;
use App\Models\RegistrationTrack;
use App\Models\Major;
use App\Models\School;
use App\Services\ActivityLogger;
use App\Traits\EnrollsStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RegistrationController extends Controller
{
    use EnrollsStudent;

    public function index(Request $request)
    {
        $query = Registration::with([
            'applicant.user',
            'registrationPeriod',
            'registrationTrack',
            'school',
            'major'
        ]);

        if ($request->filled('status')) {
            $status = $request->status;
            // "Diterima" mencakup yang sudah daftar ulang (re_registration_complete adalah subset diterima)
            if ($status === 'accepted') {
                $query->whereIn('status', ['accepted', 're_registration_complete']);
            } else {
                $query->where('status', $status);
            }
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('period_id')) {
            $query->where('registration_period_id', $request->period_id);
        }

        if ($request->filled('track_id')) {
            $query->where('registration_track_id', $request->track_id);
        }

        if ($request->filled('major_id')) {
            $query->where('major_id', $request->major_id);
        }

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->filled('deadline')) {
            $query->whereNotNull('deadline_at');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('applicant', function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            })->orWhere('registration_number', 'like', "%{$search}%");
        }

        $registrations = $query->latest()->paginate(20);

        if ($request->ajax()) {
            $html = view('admin.partials.registrations-index', compact('registrations'))->render();
            return response()->json(['html' => $html]);
        }

        $periods = RegistrationPeriod::all();
        $tracks = RegistrationTrack::all();
        $schools = School::all();
        $majors = Major::with('school')->get();

        return view('admin.registrations.index', compact(
            'registrations',
            'periods',
            'tracks',
            'schools',
            'majors'
        ));
    }

    public function show(Registration $registration)
    {
        // ponytail: same Xendit poll as student so admin sees paid instantly without webhook; no queue/cron needed
        try {
            (new class(app(\App\Services\XenditService::class)) {
                use \App\Traits\EnrollsStudent, \App\Traits\SyncsXenditPayment;
                public function __construct(public \App\Services\XenditService $xenditService) {}
                public function run($r) { $this->syncXenditPayment($r); }
            })->run($registration);
        } catch (\Throwable $e) {}

        $registration->load([
            'applicant.user',
            'registrationPeriod.schoolLevel',
            'registrationTrack',
            'school',
            'major',
            'finalMajor',
            'documents',
            'verifiedBy',
            'payments'
        ]);

        $latestVerifiedPayment = $registration->payments
            ->where('status', 'verified')
            ->whereNotNull('invoice_pdf')
            ->sortByDesc('id')
            ->first();

        // Fallback: jika belum ada invoice_pdf (mis. pembayaran lama), tetap tampilkan tombol
        if (!$latestVerifiedPayment) {
            $latestVerifiedPayment = $registration->payments
                ->where('status', 'verified')
                ->sortByDesc('id')
                ->first();
        }
        if (!$latestVerifiedPayment) {
            $latestVerifiedPayment = $registration->payments
                ->whereNotNull('invoice_pdf')
                ->sortByDesc('id')
                ->first();
        }
        if (!$latestVerifiedPayment) {
            $latestVerifiedPayment = $registration->payments->sortByDesc('id')->first();
        }

        return view('admin.registrations.show', compact('registration', 'latestVerifiedPayment'));
    }

    public function verify(Request $request, Registration $registration)
    {
        $validated = $request->validate([
            'status' => 'required|in:verified,rejected',
            'verified_notes' => 'nullable|string|max:1000',
            'payment_amount' => 'nullable|numeric|min:0',
        ]);

        $oldStatus = $registration->status;

        $registration->update([
            'status' => $validated['status'],
            'verified_by' => auth()->id(),
            'verified_notes' => $validated['verified_notes'] ?? null,
        ]);

        ActivityLogger::statusChange('registration.verify', 'Verifikasi pendaftaran ' . $registration->registration_number . ': ' . $validated['status'], $registration, $oldStatus, $validated['status'], [
            'registration_number' => $registration->registration_number,
            'verified_notes' => $validated['verified_notes'] ?? null,
        ]);

        if ($validated['status'] === 'verified') {
            $registration->loadMissing('registrationPeriod.schoolLevel', 'registrationTrack');
            if (! $registration->hasAllDocumentsVerified()) {
                // Batalkan transisi status yang sudah terlanjur tersimpan di atas
                $registration->update(['status' => $oldStatus]);
                return back()->with('error', 'Semua dokumen wajib harus diverifikasi terlebih dahulu sebelum verifikasi pendaftaran.')
                    ->withInput();
            }
            // Tentukan biaya: input admin (jalur non-reguler, per siswa) atau auto-fee Reguler.
            $isReguler = $registration->registrationTrack && strtolower($registration->registrationTrack->name) === 'reguler';
            $fee = $validated['payment_amount'] ?? null;
            if ($fee === null && $isReguler) {
                $levelId = $registration->registrationPeriod->school_level_id ?? null;
                $trackId = $registration->registration_track_id;
                $raw = $levelId ? \App\Models\Setting::get("fee_{$levelId}_{$trackId}") : null;
                $fee = ($raw !== null && $raw !== '' && is_numeric($raw)) ? (float) $raw : 500000;
            } elseif ($fee === null && ! $isReguler) {
                // Pertahankan fee yang sudah ditetapkan sebelumnya (jangan di-null-kan).
                $fee = $registration->payment_amount;
            }
            $registration->update(['payment_amount' => $fee]);
            // Gratis (biaya 0) → langsung lunas tanpa siswa bayar (jalur non-reguler tertentu).
            if ($fee !== null && (float) $fee == 0.0) {
                $this->markFreePaid($registration);
            }
        }

        // Saat menolak pendaftaran, hapus semua berkas yang tersimpan agar kolom
        // dokumen kosong; saat memverifikasi, bersihkan catatan penolakan lama.
        if ($validated['status'] === 'rejected') {
            foreach ($registration->documents as $doc) {
                Storage::disk('public')->delete($doc->file_path);
            }
            $registration->documents()->delete();
        } else {
            $registration->documents()->whereNotNull('verification_notes')->update([
                'verification_notes' => null,
            ]);
        }

        $this->enrollIfReady($registration);

        return back()->with('success', 'Status verifikasi berhasil diperbarui');
    }

    /**
     * Tandai pendaftaran lunas untuk jalur gratis (biaya 0): buat pembayaran
     * terverifikasi Rp 0 lalu panggil enrollIfReady agar status jadi accepted + NIS.
     */
    protected function markFreePaid(Registration $registration): void
    {
        $registration->payments()->create([
            'payment_type' => 'registration_fee',
            'amount' => 0,
            'payment_method' => 'cash',
            'status' => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'notes' => 'Pembayaran gratis (jalur ' . ($registration->registrationTrack->name ?? '-') . ')',
        ]);
        $registration->update(['payment_status' => 'paid']);
        $this->enrollIfReady($registration);
    }

    public function updatePayment(Request $request, Registration $registration)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:unpaid,pending,paid,failed',
            'payment_amount' => 'nullable|numeric|min:0',
        ]);

        $oldPaymentStatus = $registration->payment_status;

        $registration->update($validated);

        ActivityLogger::statusChange('registration.payment_status', 'Status pembayaran ' . $registration->registration_number . ': ' . $oldPaymentStatus . ' → ' . $validated['payment_status'], $registration, $oldPaymentStatus, $validated['payment_status'], [
            'registration_number' => $registration->registration_number,
            'payment_amount' => $validated['payment_amount'] ?? $registration->payment_amount,
        ]);

        if ($validated['payment_status'] === 'paid') {
            $existingVerified = $registration->payments()->where('status', 'verified')->exists();
            if (! $existingVerified) {
                $toVerify = $registration->payments()
                    ->where('status', 'pending')
                    ->where(function ($q) {
                        $q->where('payment_method', '!=', 'online')
                          ->orWhereNotNull('xendit_paid_at')
                          ->orWhereNotNull('proof_file');
                    })
                    ->latest('id')->first();
                if ($toVerify) {
                    $toVerify->update([
                        'status' => 'verified',
                        'verified_by' => auth()->id(),
                        'verified_at' => now(),
                        'rejection_reason' => null,
                    ]);
                } else {
                    $amountForRecord = $validated['payment_amount'] ?? $registration->payment_amount;
                    if ($amountForRecord !== null && (float) $amountForRecord == 0.0) {
                        $registration->payments()->create([
                            'payment_type' => 'registration_fee',
                            'amount' => 0,
                            'payment_method' => 'cash',
                            'status' => 'verified',
                            'verified_by' => auth()->id(),
                            'verified_at' => now(),
                            'notes' => 'Pembayaran gratis (Prestasi/Beasiswa)',
                        ]);
                    }
                }
            }
            $registration->payments()
                ->whereIn('status', ['pending', 'rejected'])
                ->where('payment_method', 'online')
                ->whereNull('xendit_paid_at')
                ->whereNull('proof_file')
                ->delete();
            $registration->refresh();
            $this->enrollIfReady($registration);
        } elseif ($validated['payment_status'] === 'failed') {
            $registration->payments()->where('status', 'pending')->update([
                'status' => 'rejected',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'rejection_reason' => 'Pembayaran dibatalkan oleh panitia',
            ]);
        }

        if ($registration->wasChanged('status') && $registration->status === 'accepted') {
            ActivityLogger::log('registration.accepted', 'Pendaftar DITERIMA otomatis: ' . $registration->registration_number, $registration, [
                'registration_number' => $registration->registration_number,
            ]);
        }

        return back()->with('success', 'Status pembayaran berhasil diperbarui');
    }

    public function reset(Request $request, Registration $registration)
    {
        $request->validate([
            'scope' => 'nullable|in:one,all',
        ]);

        $scope = $request->input('scope', 'one');
        $applicant = $registration->applicant;

        if (! $applicant) {
            return back()->with('error', 'Data pendaftar tidak ditemukan');
        }

        $registration->loadMissing(['documents', 'payments']);

        if ($scope === 'all') {
            $toDelete = $applicant->registrations()->with(['documents', 'payments'])->get();
            if ($toDelete->isEmpty()) {
                return back()->with('error', 'Tidak ada pendaftaran untuk direset');
            }
        } else {
            $toDelete = collect([$registration]);
        }

        $regNumbers = $toDelete->pluck('registration_number')->all();
        $userLabel = $applicant->full_name ?? ('#' . $applicant->id);

        // Kumpulkan path file sebelum hapus DB
        $paths = [];
        foreach ($toDelete as $reg) {
            foreach ($reg->documents as $doc) {
                if (! empty($doc->file_path)) $paths[] = $doc->file_path;
            }
            foreach ($reg->payments as $pay) {
                if (! empty($pay->proof_file)) $paths[] = $pay->proof_file;
                if (! empty($pay->invoice_pdf)) $paths[] = $pay->invoice_pdf;
            }
        }

        DB::transaction(function () use ($toDelete, $applicant) {
            foreach ($toDelete as $reg) {
                $reg->delete();
            }
            if ($applicant->registrations()->count() === 0 && $applicant->student_number !== null) {
                $applicant->update(['student_number' => null]);
            }
        });

        foreach ($paths as $path) {
            try { Storage::disk('public')->delete($path); } catch (\Throwable $e) {}
        }

        $count = count($regNumbers);
        ActivityLogger::log('registration.reset', 'Reset pendaftaran ' . implode(', ', $regNumbers) . ' (' . $userLabel . ') — ' . $count . ' pendaftaran dihapus, akun & profil tetap', null, [
            'registration_numbers' => $regNumbers,
            'applicant_id' => $applicant->id,
            'scope' => $scope,
            'deleted_count' => $count,
        ]);

        $msg = $count === 1
            ? 'Pendaftaran ' . $regNumbers[0] . ' berhasil direset — akun & profil siswa tetap, data pendaftaran bersih seperti baru.'
            : $count . ' pendaftaran milik ' . $userLabel . ' berhasil direset — akun & profil siswa tetap.';

        return redirect()->route('admin.registrations.index')->with('success', $msg);
    }

    public function destroyAccount(Registration $registration)
    {
        $user = $registration->applicant?->user;

        if (! $user) {
            return back()->with('error', 'Akun siswa tidak ditemukan');
        }

        if ((int) auth()->id() === (int) $user->id) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        $deletedName = $user->name;
        $deletedEmail = $user->email;
        $deletedId = $user->id;

        DB::transaction(fn () => $user->delete());

        ActivityLogger::log('account.delete', 'Akun siswa dihapus via pendaftaran: ' . $deletedName . ' (' . $deletedEmail . ')', null, [
            'deleted_user_id' => $deletedId,
            'deleted_name' => $deletedName,
            'deleted_email' => $deletedEmail,
            'registration_number' => $registration->registration_number,
        ]);

        return redirect()
            ->route('admin.registrations.index')
            ->with('success', 'Akun siswa beserta seluruh data pendaftarannya berhasil dihapus');
    }
}

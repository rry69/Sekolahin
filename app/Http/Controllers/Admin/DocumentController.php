<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegistrationDocument;
use App\Services\ActivityLogger;
use App\Traits\EnrollsStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    use EnrollsStudent;

    public function verify(RegistrationDocument $document)
    {
        $document->update([
            'verified_at' => now(),
            'verified_by' => auth()->id(),
            'verification_notes' => null,
        ]);

        ActivityLogger::log('document.verify', 'Dokumen diverifikasi: ' . $document->document_type . ' (' . $document->registration->registration_number . ')', $document, [
            'registration_number' => $document->registration->registration_number,
            'document_type' => $document->document_type,
        ]);

        $registration = $document->registration;

        $allVerified = $registration->documents()
            ->whereNotNull('verified_at')
            ->count() === $registration->documents()->count();

        if ($allVerified && $registration->documents()->count() > 0) {
            $this->enrollIfReady($registration);
        }

        $registration->refresh();
        $hasAllRequired = $registration->hasAllDocumentsVerified();

        if (request()->expectsJson() || request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diverifikasi',
                'document' => [
                    'id' => $document->id,
                    'document_type' => $document->document_type,
                    'verified_at' => $document->verified_at?->toIso8601String(),
                ],
                'all_verified' => $allVerified,
                'has_all_required_verified' => $hasAllRequired,
            ]);
        }

        return back()->with('success', 'Dokumen berhasil diverifikasi');
    }

    public function unverify(RegistrationDocument $document)
    {
        if (! $document->verified_at) {
            $msg = 'Dokumen belum diverifikasi';
            if (request()->expectsJson() || request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $document->update([
            'verified_at' => null,
            'verified_by' => null,
        ]);

        ActivityLogger::log('document.unverify', 'Verifikasi dokumen dibatalkan: ' . $document->document_type . ' (' . $document->registration->registration_number . ')', $document, [
            'registration_number' => $document->registration->registration_number,
            'document_type' => $document->document_type,
        ]);

        $reg = $document->registration()->with(['registrationPeriod.schoolLevel', 'registrationTrack'])->first() ?? $document->registration;
        $reg->loadMissing('registrationPeriod.schoolLevel', 'registrationTrack');
        $hasAllRequired = $reg->hasAllDocumentsVerified();

        if (request()->expectsJson() || request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Verifikasi dokumen dibatalkan',
                'document' => [
                    'id' => $document->id,
                    'document_type' => $document->document_type,
                    'verified_at' => null,
                ],
                'has_all_required_verified' => $hasAllRequired,
            ]);
        }

        return back()->with('success', 'Verifikasi dokumen dibatalkan');
    }

    public function reject(Request $request, RegistrationDocument $document)
    {
        $validated = $request->validate([
            'verification_notes' => 'required|string|max:500',
        ]);

        // Simpan alasan penolakan di catatan registrasi agar pendaftar tahu,
        // lalu hapus file yang tersimpan dan kosongkan kolom dokumen.
        $registration = $document->registration;
        $registration->update([
            'status' => 'rejected',
            'verified_by' => auth()->id(),
            'verified_notes' => $validated['verification_notes'],
        ]);

        $regNum = $document->registration->registration_number;
        $docType = $document->document_type;

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        ActivityLogger::log('document.reject', 'Dokumen ditolak: ' . $docType . ' (' . $regNum . ')', $registration, [
            'registration_number' => $regNum,
            'document_type' => $docType,
            'reason' => $validated['verification_notes'],
        ]);

        return back()->with('success', 'Dokumen ditolak dan file dihapus');
    }
}

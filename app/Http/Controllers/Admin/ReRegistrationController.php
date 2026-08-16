<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReRegistration;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = ReRegistration::with(['registration.applicant.user', 'verifier']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $reRegistrations = $query->orderBy('submitted_at', 'desc')->paginate(20);

        if ($request->ajax()) {
            return response()->json(['html' => view('admin.partials.re-registrations-index', compact('reRegistrations'))->render()]);
        }

        return view('admin.re-registrations.index', compact('reRegistrations'));
    }

    public function show(ReRegistration $reRegistration)
    {
        $reRegistration->load(['registration.applicant.user', 'verifier', 'registration.registrationPeriod.schoolLevel', 'registration.registrationTrack']);

        if (request()->ajax()) {
            return response()->json(['html' => view('admin.partials.re-registrations-show', compact('reRegistration'))->render()]);
        }

        return view('admin.re-registrations.show', compact('reRegistration'));
    }

    public function verify(ReRegistration $reRegistration)
    {
        if ($reRegistration->status !== 'pending') {
            return back()->with('error', 'Daftar ulang ini sudah diverifikasi');
        }

        $reRegistration->update([
            'status' => 'completed',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        $reRegistration->registration->update(['status' => 're_registration_complete']);

        ActivityLogger::log('re_registration.verify', 'Daftar ulang diverifikasi: ' . $reRegistration->registration->registration_number, $reRegistration, [
            'registration_number' => $reRegistration->registration->registration_number,
        ]);

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Daftar ulang berhasil diverifikasi']);
        }

        return back()->with('success', 'Daftar ulang berhasil diverifikasi');
    }

    public function verifyByCode(Request $request)
    {
        $request->validate(['verification_code' => 'required|string|max:20']);
        $code = Str::upper(trim($request->input('verification_code')));

        $reRegistration = ReRegistration::where('verification_code', $code)->first();

        if (! $reRegistration) {
            return back()->with('error', 'Kode verifikasi tidak ditemukan');
        }

        if ($reRegistration->status !== 'pending') {
            return back()->with('info', 'Daftar ulang dengan kode ini sudah diverifikasi');
        }

        $reRegistration->update([
            'status' => 'completed',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        $reRegistration->registration->update(['status' => 're_registration_complete']);

        ActivityLogger::log('re_registration.verify_code', 'Daftar ulang diverifikasi via kode: ' . $reRegistration->registration->registration_number . ' (' . $code . ')', $reRegistration, [
            'registration_number' => $reRegistration->registration->registration_number,
            'verification_code' => $code,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Kode terverifikasi — siswa terdaftar resmi: ' . ($reRegistration->registration->applicant->full_name ?? $reRegistration->registration->registration_number)]);
        }

        return back()->with('success', 'Kode terverifikasi — siswa terdaftar resmi: ' . ($reRegistration->registration->applicant->full_name ?? $reRegistration->registration->registration_number));
    }
}

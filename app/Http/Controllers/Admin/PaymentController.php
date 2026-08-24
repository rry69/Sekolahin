<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\ActivityLogger;
use App\Traits\EnrollsStudent;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use EnrollsStudent;

    public function index(Request $request)
    {
        $query = Payment::with(['registration.applicant.user', 'verifier'])
            ->whereRaw("NOT (payment_method = 'online' AND xendit_paid_at IS NULL AND proof_file IS NULL AND status IN ('pending','rejected'))");

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        if ($request->ajax()) {
            return response()->json(['html' => view('admin.partials.payments-index', compact('payments'))->render()]);
        }

        return view('admin.payments.index', compact('payments'));
    }

    public function verify(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Pembayaran ini sudah diverifikasi atau ditolak');
        }

        $registration = $payment->registration;
        $isReRegFee = ($payment->payment_type === 're_registration_fee');

        // Untuk biaya daftar ulang: tidak ada kaitan dengan payment_status biaya pendaftaran.
        // Untuk biaya pendaftaran: cegah duplikasi pembayaran sukses.
        if (!$isReRegFee
            && ($registration->payment_status === 'paid' || $registration->payments()->where('status', 'verified')->exists())) {
            return back()->with('error', 'Sudah ada pembayaran sukses untuk pendaftaran ini');
        }

        $payment->update([
            'status' => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        if (!$isReRegFee) {
            $registration->update(['payment_status' => 'paid']);
            $registration->payments()
                ->where('id', '!=', $payment->id)
                ->whereIn('status', ['pending', 'rejected'])
                ->where('payment_method', 'online')
                ->whereNull('xendit_paid_at')
                ->whereNull('proof_file')
                ->delete();
        }

        ActivityLogger::log('payment.verify', 'Pembayaran diverifikasi: ' . $payment->registration->registration_number, $payment, [
            'registration_number' => $payment->registration->registration_number,
            'amount' => $payment->amount,
            'payment_type' => $payment->payment_type,
        ]);

        if (!$isReRegFee) {
            $this->enrollIfReady($payment->registration);
        }

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pembayaran berhasil diverifikasi']);
        }

        return back()->with('success', 'Pembayaran berhasil diverifikasi');
    }

    public function reject(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($payment->status !== 'pending') {
            return back()->with('error', 'Pembayaran ini sudah diverifikasi atau ditolak');
        }

        $payment->update([
            'status' => 'rejected',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Biaya daftar ulang TIDAK mengubah payment_status biaya pendaftaran.
        if ($payment->payment_type !== 're_registration_fee') {
            $payment->registration->update(['payment_status' => 'failed']);
        }

        ActivityLogger::log('payment.reject', 'Pembayaran ditolak: ' . $payment->registration->registration_number, $payment, [
            'registration_number' => $payment->registration->registration_number,
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pembayaran ditolak']);
        }

        return back()->with('success', 'Pembayaran ditolak');
    }

    public function reset(Payment $payment)
    {
        if ($payment->status === 'pending') {
            return back()->with('error', 'Pembayaran ini masih berstatus pending');
        }

        $registration = $payment->registration;
        $registrationNumber = $registration->registration_number;

        foreach ($registration->payments as $p) {
            if ($p->proof_file) {
                \Illuminate\Support\Facades\Storage::disk('private')->delete($p->proof_file);
            }
            if ($p->invoice_pdf) {
                \Illuminate\Support\Facades\Storage::disk('private')->delete($p->invoice_pdf);
            }
        }
        $registration->payments()->delete();
        $registration->update(['payment_status' => 'pending']);

        if (in_array($registration->status, ['accepted', 're_registration_complete'], true)) {
            $registration->update(['status' => 'verified', 'final_major_id' => null]);
            if ($registration->applicant) {
                $registration->applicant->update(['student_number' => null]);
            }
            if ($registration->reRegistration) {
                $registration->reRegistration()->delete();
            }
        }

        ActivityLogger::log('payment.reset', 'Pembayaran direset ke pending: ' . $registrationNumber, $payment, [
            'registration_number' => $registrationNumber,
        ]);

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Status pembayaran dikembalikan ke pending']);
        }

        return back()->with('success', 'Status pembayaran dikembalikan ke pending');
    }
}

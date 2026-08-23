<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Registration;
use App\Services\ActivityLogger;
use App\Services\InvoiceService;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    protected $xenditService;
    protected $invoiceService;

    public function __construct(XenditService $xenditService, InvoiceService $invoiceService)
    {
        $this->xenditService = $xenditService;
        $this->invoiceService = $invoiceService;
    }

    public function store(Request $request)
    {
        $rules = [
            'registration_id' => 'required|exists:registrations,id',
            'payment_type' => 'required|in:registration_fee,re_registration_fee',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:bank_transfer,cash,online',
        ];

        if ($request->payment_method !== 'online') {
            $rules['proof_file'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:2048';
        }

        $validated = $request->validate($rules);

        $registration = Registration::findOrFail($validated['registration_id']);

        if ($registration->applicant->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($registration->status === 'withdrawn') {
            return back()->with('error', 'Pendaftaran sudah dibatalkan (mengundurkan diri). Pembayaran tidak dapat dilanjutkan dan status dokumen telah ditolak.');
        }

        $isReRegFee = ($validated['payment_type'] === 're_registration_fee');

        if ($isReRegFee) {
            // Biaya daftar ulang terpisah dari biaya pendaftaran:
            // hanya relevan setelah siswa DITERIMA (accepted) dan belum lunas.
            if ($registration->status !== 'accepted') {
                return back()->with('error', 'Pembayaran biaya daftar ulang hanya tersedia setelah Anda dinyatakan DITERIMA.');
            }
            $hasPaidReRegFee = $registration->payments()
                ->where('payment_type', 're_registration_fee')
                ->where('status', 'verified')
                ->exists();
            if ($hasPaidReRegFee) {
                return back()->with('error', 'Biaya daftar ulang sudah lunas.');
            }
        } elseif ($registration->payment_status === 'paid') {
            return back()->with('error', 'Pembayaran untuk pendaftaran ini sudah lunas');
        }

        // Semua jalur (termasuk Reguler): biaya baru tampil setelah berkas Terverifikasi.
        // Khusus biaya daftar ulang (re_registration_fee), syaratnya sudah DITERIMA
        // (status accepted) — dilewati dari guard ini.
        if (!$isReRegFee) {
            $registration->loadMissing('registrationTrack');
            if ($registration->status !== 'verified' || $registration->payment_amount === null) {
                $trackName = $registration->registrationTrack->name ?? 'ini';
                return back()->with('error', 'Pembayaran jalur ' . $trackName . ' hanya tersedia setelah berkas Terverifikasi oleh panitia.');
            }
        }

        $paymentDeadlineHours = (int) \App\Models\Setting::get('payment_deadline_hours', 72);
        $paymentDeadline = now()->addHours($paymentDeadlineHours);

        // Tagihan online (Xendit) yang belum lunas bukan tagihan mengikat: siswa boleh
        // mengulang Bayar Online. Hanya pembayaran manual pending yang memblokir.
        if ($request->payment_method !== 'online'
            && $registration->payments()->where('status', 'pending')->where('payment_method', '!=', 'online')->exists()) {
            return back()->with('error', 'Terdapat pembayaran yang masih menunggu verifikasi. Selesaikan atau tunggu verifikasi pembayaran yang sudah dibuat.');
        }

        // Biaya daftar ulang via online tidak didukung: Xendit callback hanya
        // memahami payment_status biaya pendaftaran. Gunakan transfer manual.
        if ($isReRegFee && $request->payment_method === 'online') {
            return back()->with('error', 'Pembayaran biaya daftar ulang dilakukan via transfer manual. Silakan upload bukti transfer.');
        }

        if ($request->payment_method === 'online') {
            // Jika sudah ada invoice online yang belum selesai, lanjutkan invoice itu
            // (jangan membuat invoice Xendit baru setiap kali tombol ditekan).
            $existing = $registration->payments()
                ->where('status', 'pending')
                ->where('payment_method', 'online')
                ->whereNotNull('xendit_invoice_url')
                ->latest('id')
                ->first();

            if ($existing) {
                $registration->update(['deadline_at' => $paymentDeadline]);
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Lanjutkan pembayaran invoice yang belum selesai',
                        'payment' => $existing,
                        'invoice_url' => route('payments.invoice.view', $existing),
                    ]);
                }
                return redirect()->route('payments.invoice.view', $existing);
            }

            $payment = Payment::create([
                'registration_id' => $validated['registration_id'],
                'payment_type' => $validated['payment_type'],
                'amount' => $validated['amount'],
                'payment_method' => 'online',
                'status' => 'pending',
            ]);

            $registration->update([
                'deadline_at' => $paymentDeadline,
            ]);

            $result = $this->xenditService->createInvoice($payment);

            if (!$result['success']) {
                $payment->delete();
                return back()->with('error', 'Gagal membuat invoice pembayaran: ' . $result['error']);
            }

            // Tagihan nyata (punya xendit_invoice_id) → terbitkan invoice milik sistem
            $this->invoiceService->issue($payment);

            ActivityLogger::log('payment.create_online', 'Invoice Xendit dibuat untuk ' . $registration->registration_number, $payment, [
                'registration_number' => $registration->registration_number,
                'amount' => $validated['amount'],
                'payment_method' => 'online',
                'invoice_id' => $result['invoice_id'] ?? null,
            ]);

            // Arahkan ke invoice milik sistem, bukan halaman checkout Xendit.
            // payment_status registrasi tetap 'unpaid' selama invoice Xendit belum lunas,
            // sehingga halaman ditutup tanpa bayar tidak mengubah status menjadi pending.
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice pembayaran berhasil dibuat',
                    'payment' => $payment,
                    'invoice_url' => route('payments.invoice.view', $payment),
                ]);
            }

            return redirect()->route('payments.invoice.view', $payment);
        }

        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $path = $file->store('payment-proofs', 'public');
            $validated['proof_file'] = $path;
        }

        $payment = Payment::create($validated);

        $this->invoiceService->issue($payment);

        // Biaya daftar ulang TIDAK menyentuh payment_status/deadline_at pendaftaran
        // (itu milik biaya pendaftaran). Hanya biaya pendaftaran yang menggeser status.
        if (!$isReRegFee) {
            $registration->update([
                'payment_status' => 'pending',
                'deadline_at' => $paymentDeadline,
            ]);
        }

        ActivityLogger::log('payment.upload_proof', 'Bukti bayar diupload untuk ' . $registration->registration_number, $payment, [
            'registration_number' => $registration->registration_number,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_type' => $validated['payment_type'],
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diunggah',
                'payment' => $payment
            ]);
        }

        return back()->with('success', 'Bukti pembayaran berhasil diunggah');
    }

    /**
     * Otorisasi akses payment/invoice: hanya pemilik pendaftaran atau Admin.
     * (Sebelumnya: `&& !auth()->user()->role_id` — semua user ber-role termasuk
     * Siswa lolos → IDOR. Sekarang cek role eksplisit.)
     */
    private function authorizePaymentAccess(Payment $payment): void
    {
        $registration = $payment->registration;

        $isOwner = $registration->applicant && $registration->applicant->user_id === auth()->id();
        $isAdmin = auth()->user()->role?->name === 'Admin';

        if (! $isOwner && ! $isAdmin) {
            abort(403, 'Unauthorized');
        }
    }

    public function show(Payment $payment)
    {
        $this->authorizePaymentAccess($payment);

        return view('payments.show', compact('payment'));
    }

    public function invoice(Payment $payment)
    {
        $this->authorizePaymentAccess($payment);

        if (!$payment->invoice_pdf) {
            abort(404, 'Invoice belum tersedia');
        }

        return Storage::disk('public')->download($payment->invoice_pdf, 'invoice-' . ($payment->invoice_number ?? $payment->id) . '.pdf');
    }

    /**
     * Tampilkan invoice milik sistem di browser (bukan invoice Xendit).
     * Jika invoice Xendit masih aktif, tombol lanjut bayar disediakan.
     */
    public function invoiceView(Payment $payment)
    {
        $this->authorizePaymentAccess($payment);

        $registration = $payment->registration;

        $canPayOnline = $payment->payment_method === 'online'
            && $payment->status === 'pending'
            && $payment->xendit_invoice_url;

        return view('payments.invoice', compact('payment', 'registration', 'canPayOnline'));
    }
}

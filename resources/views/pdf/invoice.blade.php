<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Pembayaran</title>
    <style>
        /* ============================================================
           BRINGOVA DESIGN SYSTEM — PDF Invoice
           (Palet di-hardcode karena DomPDF tidak mendukung CSS variables;
            tanpa box-shadow & grid — pakai table + block sederhana.)
           ============================================================ */
        @page { size: A4 portrait; margin: 0; }
        html, body { margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1a1a2e;              /* --ink */
            background: #f6f7fb;         /* bg Bringova */
            line-height: 1.45;
        }

        .page { padding: 30px 34px 26px; }

        /* ---------- Header sekolah ---------- */
        .sch-head { width: 100%; border-collapse: collapse; border-bottom: 1px solid rgba(26,26,46,.10); padding-bottom: 0; }
        .sch-head td { vertical-align: middle; padding-bottom: 14px; }
        .sch-ic {
            width: 46px; height: 46px;
            background: #FF6B6B;         /* --coral */
            color: #ffffff;
            border-radius: 14px;
            font-size: 16px; font-weight: 800;
            text-align: center; vertical-align: middle;
        }
        .sch-ic-img {
            width: 46px; height: 46px;
            border-radius: 14px;
            text-align: center; vertical-align: middle;
        }
        .sch-ic-img img { width: 46px; height: 46px; }
        .sch-info { padding-left: 14px; }
        .sch-name { font-size: 16px; font-weight: 800; color: #1a1a2e; }
        .sch-addr { font-size: 11.5px; color: #8a8f9d; margin-top: 2px; }
        .sch-right { text-align: right; }
        .doc-title { font-size: 18px; font-weight: 800; color: #1a1a2e; letter-spacing: .02em; }
        .doc-sub { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .12em; color: #8a8f9d; margin-top: 2px; }

        /* ---------- Pill status (Bringova) ---------- */
        .badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 99px;
            font-size: 11.5px;
            font-weight: 800;
            letter-spacing: .06em;
            margin-top: 9px;
        }
        .badge.verified { background: #D1FAE5; color: #047857; }   /* green-soft / green dark */
        .badge.rejected { background: #FEE2E2; color: #B91C1C; }   /* red-soft / red dark */
        .badge.expired  { background: #F3F4F6; color: #6b7280; }   /* gray-soft / gray */
        .badge.pending  { background: #FEF3C7; color: #B45309; }   /* amber-soft / amber dark */

        /* ---------- Info grid (2 kolom) ---------- */
        .info-table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        .info-table td {
            padding: 9px 8px 9px 0;
            vertical-align: top;
            border-bottom: 1px solid rgba(26,26,46,.08);
        }
        .info-table td.gap { padding-left: 18px; }
        .info-table .lbl {
            width: 26%;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #8a8f9d;
        }
        .info-table .val { width: 24%; font-size: 12.5px; font-weight: 600; color: #1a1a2e; }

        /* ---------- Amount highlight ---------- */
        .amount-box {
            margin-top: 18px;
            border-left: 4px solid #FF6B6B;         /* --coral */
            background: #FFE5E3;                     /* --coral-soft */
            border-radius: 14px;
            padding: 15px 20px;
        }
        .amount-label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; color: #8a8f9d; }
        .amount-value { font-size: 24px; font-weight: 800; color: #1a1a2e; letter-spacing: -.01em; line-height: 1.15; margin-top: 2px; }
        .amount-terbilang { font-size: 11.5px; color: #6b7280; margin-top: 4px; }

        /* ---------- Status strip ---------- */
        .status-strip {
            margin-top: 16px;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 12.5px;
            font-weight: 700;
        }
        .status-strip.green { background: #D1FAE5; color: #047857; }
        .status-strip.amber { background: #FEF3C7; color: #B45309; }
        .status-strip.red   { background: #FEE2E2; color: #B91C1C; }
        .status-strip.gray  { background: #F3F4F6; color: #6b7280; }

        /* ---------- Bank box (manual only) ---------- */
        .bank-box {
            margin-top: 16px;
            background: #FEF3C7;                     /* --amber-soft */
            border: 1px solid #FCD34D;
            border-radius: 14px;
            padding: 14px 18px;
        }
        .bank-title { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; color: #B45309; }
        .bank-acc { font-size: 15px; font-weight: 800; color: #1a1a2e; margin-top: 6px; letter-spacing: .01em; }
        .bank-holder { font-size: 12px; color: #92400E; margin-top: 3px; }

        /* ---------- Footer ---------- */
        .footer { margin-top: 34px; }
        .signature { text-align: right; margin-top: 14px; }
        .sig-city { font-size: 12px; color: #1a1a2e; }
        .sig-role { margin-top: 62px; font-size: 12px; color: #1a1a2e; }
        .footnote {
            margin-top: 24px;
            border-top: 1px solid rgba(26,26,46,.10);
            padding-top: 12px;
            text-align: center;
            color: #8a8f9d;
            font-size: 10.5px;
        }
        .footnote p { margin: 3px 0; }
    </style>
</head>
<body>
<div class="page">
    @php
        $registration = $payment->registration;
        $school = $registration->school;

        // Status dinamis — ikuti status pembayaran saat ini (tidak di-hardcode):
        // - rejected + rejection_reason 'expired' → KADALUARSA
        // - verified                               → LUNAS
        // - rejected                               → DITOLAK
        // - selain itu                             → MENUNGGU
        $isVerified = $payment->status === 'verified';
        $isRejected = $payment->status === 'rejected';
        $isExpired = $isRejected && str_contains(strtolower((string) $payment->rejection_reason), 'expired');
        $badgeClass = $isVerified ? 'verified' : ($isExpired ? 'expired' : ($isRejected ? 'rejected' : 'pending'));
        $badgeLabel = $isVerified ? 'LUNAS' : ($isExpired ? 'KADALUARSA' : ($isRejected ? 'DITOLAK' : 'MENUNGGU'));

        $isManual = $payment->payment_method !== 'online';
        $paidAt = $payment->verified_at;

        // Pesan strip status (konsisten dengan halaman invoice)
        if ($isVerified) {
            $stripClass = 'green';
            $stripText = 'Pembayaran berhasil diverifikasi — Tagihan ini sudah lunas. Terima kasih!';
        } elseif ($isExpired) {
            $stripClass = 'gray';
            $stripText = 'Invoice telah kadaluarsa. Silakan hubungi panitia untuk informasi lebih lanjut.';
        } elseif ($isRejected) {
            $stripClass = 'red';
            $stripText = 'Pembayaran ditolak. Silakan hubungi panitia untuk informasi lebih lanjut.';
        } else {
            $stripClass = 'amber';
            $stripText = 'Menunggu pembayaran — selesaikan pembayaran sebelum batas waktu.';
        }

        $bankName = \App\Models\Setting::get('bank_name', '');
        $bankNumber = \App\Models\Setting::get('bank_account_number');
        $bankAccountName = \App\Models\Setting::get('bank_account_name');
        $deadline = $registration->deadline_at ?? now()->addHours((int) \App\Models\Setting::get('payment_deadline_hours', 72));
        $invoiceService = app(\App\Services\InvoiceService::class);

        // Logo sekolah: jika sudah diunggah admin, tampilkan sebagai data-URI
        // (DomPDF `enable_remote=false`, jadi URL /storage tidak bisa di-fetch —
        //  data-URI paling andal untuk embed gambar dari CLI maupun web).
        $logoDataUri = null;
        if ($school->logo_path) {
            $logoAbs = \Illuminate\Support\Facades\Storage::disk('public')->path($school->logo_path);
            if (is_file($logoAbs)) {
                $mime = function_exists('mime_content_type') ? mime_content_type($logoAbs) : 'image/jpeg';
                $logoDataUri = 'data:' . ($mime ?: 'image/jpeg') . ';base64,' . base64_encode(file_get_contents($logoAbs));
            }
        }
    @endphp

    {{-- ================= HEADER SEKOLAH ================= --}}
    <table class="sch-head">
        <tr>
            @if ($logoDataUri)
                <td class="sch-ic-img" width="46"><img src="{{ $logoDataUri }}"></td>
            @else
                <td class="sch-ic" width="46">SPMB</td>
            @endif
            <td class="sch-info">
                <div class="sch-name">{{ $school->name }}</div>
                <div class="sch-addr">{{ $school->address }}</div>
            </td>
            <td class="sch-right">
                <div class="doc-title">INVOICE PEMBAYARAN</div>
                <div class="doc-sub">Invoice Tagihan</div>
                <div class="badge {{ $badgeClass }}">{{ $badgeLabel }}</div>
            </td>
        </tr>
    </table>

    {{-- ================= INFO TAGIHAN (2 KOLOM) ================= --}}
    <table class="info-table">
        <tr>
            <td class="lbl">No. Invoice</td>
            <td class="val">{{ $payment->invoice_number ?? '-' }}</td>
            <td class="lbl gap">No. Registrasi</td>
            <td class="val">{{ $registration->registration_number }}</td>
        </tr>
        <tr>
            <td class="lbl">Tanggal Terbit</td>
            <td class="val">{{ optional($payment->invoice_issued_at ?? $payment->created_at)->format('d M Y') }}</td>
            <td class="lbl gap">Nama Lengkap</td>
            <td class="val">{{ $registration->applicant->full_name }}</td>
        </tr>
        <tr>
            <td class="lbl">NISN</td>
            <td class="val">{{ $registration->applicant->nisn }}</td>
            <td class="lbl gap">Jenjang</td>
            <td class="val">{{ $registration->registrationPeriod->schoolLevel->name }}</td>
        </tr>
        <tr>
            <td class="lbl">Periode</td>
            <td class="val">{{ $registration->registrationPeriod->name }}</td>
            <td class="lbl gap">Jalur</td>
            <td class="val">{{ $registration->registrationTrack->name }}</td>
        </tr>
        <tr>
            <td class="lbl">Jenis Biaya</td>
            <td class="val">{{ $payment->payment_type === 'registration_fee' ? 'Biaya Pendaftaran' : 'Biaya Daftar Ulang' }}</td>
            <td class="lbl gap">Metode</td>
            <td class="val">{{ $isManual ? 'Transfer Bank (Manual)' : 'Online (Xendit)' }}</td>
        </tr>
        @if ($isVerified && $paidAt)
            <tr>
                <td class="lbl">Tanggal Pembayaran</td>
                <td class="val">{{ $paidAt->format('d M Y H:i') }}</td>
                <td class="lbl gap"></td>
                <td class="val"></td>
            </tr>
        @elseif (!$isVerified && $deadline)
            <tr>
                <td class="lbl">Batas Waktu</td>
                <td class="val">{{ $deadline->format('d M Y H:i') }}</td>
                <td class="lbl gap"></td>
                <td class="val"></td>
            </tr>
        @endif
    </table>

    {{-- ================= NOMINAL (MENONJOL) ================= --}}
    <div class="amount-box">
        <div class="amount-label">Jumlah Tagihan</div>
        <div class="amount-value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
        <div class="amount-terbilang">Terbilang: {{ ucfirst($invoiceService->terbilang($payment->amount)) }} Rupiah</div>
    </div>

    {{-- ================= STRIP STATUS ================= --}}
    <div class="status-strip {{ $stripClass }}">{{ $stripText }}</div>

    {{-- ================= REKENING (KHUSUS MANUAL) ================= --}}
    @if ($isManual && $bankNumber)
        <div class="bank-box">
            <div class="bank-title">Transfer Manual ke</div>
            <div class="bank-acc">{{ $bankName ?: '' }} {{ $bankNumber }}</div>
            <div class="bank-holder">a.n. {{ $bankAccountName ?: '-' }}</div>
        </div>
    @endif

    {{-- ================= FOOTER & TANDA TANGAN ================= --}}
    <div class="footer">
        <div class="signature">
            <div class="sig-city">{{ $school->name }}, {{ now()->format('d F Y') }}</div>
            <div class="sig-role">( Bendahara / Panitia )</div>
        </div>
        <div class="footnote">
            <p>Invoice ini merupakan bukti sah. Harap disimpan dengan baik.</p>
            <p>Dibuat otomatis oleh sistem SPMB {{ $school->name }} pada {{ now()->format('d M Y H:i') }}.</p>
        </div>
    </div>
</div>
</body>
</html>

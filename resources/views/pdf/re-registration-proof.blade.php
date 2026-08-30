<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bukti Daftar Ulang</title>
    <style>
        /* ============================================================
           BRINGOVA DESIGN SYSTEM — PDF Bukti Daftar Ulang
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
        .sch-head { width: 100%; border-collapse: collapse; border-bottom: 1px solid rgba(26,26,46,.10); }
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
        .badge.green { background: #D1FAE5; color: #047857; }   /* green-soft / green dark */

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

        /* ---------- Kode verifikasi (menonjol) ---------- */
        .code-box {
            margin-top: 18px;
            border-left: 4px solid #10B981;         /* --green */
            background: #D1FAE5;                    /* --green-soft */
            border-radius: 14px;
            padding: 14px 20px;
        }
        .code-label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; color: #047857; }
        .code-value {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 22px;
            font-weight: 800;
            color: #065F46;
            letter-spacing: 3px;
            line-height: 1.2;
            margin-top: 4px;
        }
        .code-note { font-size: 11px; color: #065F46; margin-top: 6px; }

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
        $school = $registration->school;
        $reReg = $registration->reRegistration;

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
                <div class="doc-title">BUKTI DAFTAR ULANG</div>
                <div class="doc-sub">Diterima Sebagai Siswa</div>
                <div class="badge green">DITERIMA</div>
            </td>
        </tr>
    </table>

    {{-- ================= INFO SISWA (2 KOLOM) ================= --}}
    <table class="info-table">
        <tr>
            <td class="lbl">No. Registrasi</td>
            <td class="val">{{ $registration->registration_number }}</td>
            <td class="lbl gap">Nomor Induk Siswa</td>
            <td class="val">{{ $registration->applicant->student_number ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Nama Lengkap</td>
            <td class="val">{{ $registration->applicant->full_name }}</td>
            <td class="lbl gap">NISN</td>
            <td class="val">{{ $registration->applicant->nisn }}</td>
        </tr>
        <tr>
            <td class="lbl">Jenjang</td>
            <td class="val">{{ $registration->registrationPeriod?->schoolLevel?->name ?? '-' }}</td>
            <td class="lbl gap">Periode</td>
            <td class="val">{{ $registration->registrationPeriod?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Jalur</td>
            <td class="val">{{ $registration->registrationTrack?->name ?? '-' }}</td>
            <td class="lbl gap">Jurusan Diterima</td>
            <td class="val">{{ $registration->finalMajor?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Tanggal Diterima</td>
            <td class="val">{{ $registration->updated_at->format('d M Y') }}</td>
            <td class="lbl gap"></td>
            <td class="val"></td>
        </tr>
    </table>

    {{-- ================= KODE VERIFIKASI (MENONJOL) ================= --}}
    @if ($reReg?->verification_code)
        <div class="code-box">
            <div class="code-label">Kode Verifikasi Daftar Ulang</div>
            <div class="code-value">{{ $reReg->verification_code }}</div>
            <div class="code-note">Tunjukkan kode verifikasi di atas kepada panitia di sekolah untuk verifikasi daftar ulang.</div>
        </div>
    @endif

    {{-- ================= FOOTER & TANDA TANGAN ================= --}}
    <div class="footer">
        <div class="signature">
            <div class="sig-city">{{ $school->name }}, {{ now()->format('d F Y') }}</div>
            <div class="sig-role">( Panitia PPDB )</div>
        </div>
        <div class="footnote">
            <p>Surat ini adalah bukti sah bahwa yang bersangkutan telah diterima dan terdaftar sebagai siswa.</p>
            <p>Dibuat otomatis oleh sistem SPMB {{ $school->name }} pada {{ now()->format('d M Y H:i') }}.</p>
        </div>
    </div>
</div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bukti Daftar Ulang</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #333; }
        .container { max-width: 640px; margin: 0 auto; }
        .header { text-align: center; border-bottom: 3px solid #1a1a2e; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; color: #1a1a2e; }
        .header p { margin: 2px 0; color: #666; }
        .badge { display: inline-block; margin-top: 8px; padding: 6px 16px; background: #16a34a; color: #fff; font-weight: bold; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        td { padding: 6px 8px; border-bottom: 1px solid #eee; vertical-align: top; }
        td:first-child { width: 40%; color: #666; }
        td:last-child { font-weight: 500; color: #1a1a2e; }
        .footer { margin-top: 24px; text-align: center; color: #999; font-size: 11px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{{ $registration->school?->name ?? 'Sekolah' }}</h1>
        <p>{{ $registration->school?->address ?? '' }}</p>
        <p>BUKTI PENDAFTARAN ULANG (DITERIMA SEBAGAI SISWA)</p>
        <div class="badge">DITERIMA</div>
    </div>

    <table>
        <tr><td>No. Registrasi</td><td>{{ $registration->registration_number }}</td></tr>
        <tr><td>Nomor Induk Siswa (NIS)</td><td>{{ $registration->applicant->student_number ?? '-' }}</td></tr>
        <tr><td>Nama Lengkap</td><td>{{ $registration->applicant->full_name }}</td></tr>
        <tr><td>NISN</td><td>{{ $registration->applicant->nisn }}</td></tr>
        <tr><td>Jenjang</td><td>{{ $registration->registrationPeriod?->schoolLevel?->name ?? '-' }}</td></tr>
        <tr><td>Periode</td><td>{{ $registration->registrationPeriod?->name ?? '-' }}</td></tr>
        <tr><td>Jalur</td><td>{{ $registration->registrationTrack?->name ?? '-' }}</td></tr>
        <tr><td>Jurusan Diterima</td><td>{{ $registration->finalMajor?->name ?? '-' }}</td></tr>
        <tr><td>Tanggal Diterima</td><td>{{ $registration->updated_at->format('d M Y') }}</td></tr>
        @if($registration->reRegistration?->verification_code)
        <tr><td>Kode Verifikasi</td><td style="font-family: DejaVu Sans Mono, monospace; letter-spacing: 2px; font-size: 15px; font-weight: bold;">{{ $registration->reRegistration->verification_code }}</td></tr>
        @endif
    </table>
    @if($registration->reRegistration?->verification_code)
    <p style="text-align:center; margin-top: 14px; font-size: 11px; color: #555;">Tunjukkan kode verifikasi di atas kepada panitia di sekolah untuk verifikasi daftar ulang.</p>
    @endif

    <div class="footer">
        Surat ini adalah bukti sah bahwa yang bersangkutan telah diterima dan terdaftar sebagai siswa.<br>
        Dibuat otomatis oleh sistem SPMB {{ $registration->school?->name ?? '' }}.
    </div>
</div>
</body>
</html>

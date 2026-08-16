<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Pembayaran</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #333; }
        .container { max-width: 640px; margin: 0 auto; }
        .header { text-align: center; border-bottom: 3px solid #1a1a2e; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; color: #1a1a2e; }
        .header p { margin: 2px 0; color: #666; }
        .badge { display: inline-block; margin-top: 8px; padding: 6px 16px; background: #f59e0b; color: #fff; font-weight: bold; border-radius: 4px; }
        .badge.verified { background: #16a34a; }
        .badge.rejected { background: #dc2626; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        td { padding: 6px 8px; border-bottom: 1px solid #eee; vertical-align: top; }
        td:first-child { width: 40%; color: #666; }
        td:last-child { font-weight: 500; color: #1a1a2e; }
        .amount { font-size: 16px; font-weight: bold; color: #1a1a2e; }
        .footer { margin-top: 40px; }
        .signature { margin-top: 60px; text-align: right; }
        .signature p { margin: 2px 0; }
    </style>
</head>
<body>
<div class="container">
    @php
        $registration = $payment->registration;
        $school = $registration->school;
        $badgeClass = match($payment->status) { 'verified' => 'verified', 'rejected' => 'rejected', default => '' };
        $badgeLabel = match($payment->status) { 'verified' => 'LUNAS', 'rejected' => 'DITOLAK', default => 'MENUNGGU' };
    @endphp
    <div class="header">
        <h1>{{ $school->name }}</h1>
        <p>{{ $school->address }}</p>
        <p>INVOICE PEMBAYARAN</p>
        <div class="badge {{ $badgeClass }}">{{ $badgeLabel }}</div>
    </div>

    <table>
        <tr><td>No. Invoice</td><td>{{ $payment->invoice_number ?? '-' }}</td></tr>
        <tr><td>No. Registrasi</td><td>{{ $registration->registration_number }}</td></tr>
        <tr><td>Tanggal Terbit</td><td>{{ optional($payment->invoice_issued_at ?? $payment->created_at)->format('d M Y') }}</td></tr>
        <tr><td>Nama Lengkap</td><td>{{ $registration->applicant->full_name }}</td></tr>
        <tr><td>NISN</td><td>{{ $registration->applicant->nisn }}</td></tr>
        <tr><td>Jenjang</td><td>{{ $registration->registrationPeriod->schoolLevel->name }}</td></tr>
        <tr><td>Periode</td><td>{{ $registration->registrationPeriod->name }}</td></tr>
        <tr><td>Jalur</td><td>{{ $registration->registrationTrack->name }}</td></tr>
        <tr><td>Jenis Biaya</td><td>{{ $payment->payment_type === 'registration_fee' ? 'Biaya Pendaftaran' : 'Biaya Daftar Ulang' }}</td></tr>
        <tr><td>Jumlah</td><td class="amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td></tr>
        <tr><td>Terbilang</td><td>{{ ucfirst(app(\App\Services\InvoiceService::class)->terbilang($payment->amount)) }} Rupiah</td></tr>
        <tr><td>Metode</td><td>{{ $payment->payment_method === 'online' ? 'Online (Xendit)' : ucwords(str_replace('_', ' ', $payment->payment_method)) }}</td></tr>
    </table>

    <div class="footer">
        <div class="signature">
            <p>{{ $school->name }}, {{ now()->format('d F Y') }}</p>
            <p style="margin-top:60px;">( Bendahara / Panitia )</p>
        </div>
        <p style="text-align:center;color:#999;font-size:11px;">Dibuat otomatis oleh sistem SPMB {{ $school->name }}.</p>
    </div>
</div>
</body>
</html>

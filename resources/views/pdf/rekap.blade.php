<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Siswa Diterima</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; border-bottom: 3px solid #1a1a2e; padding-bottom: 10px; margin-bottom: 16px; }
        .header h1 { font-size: 16px; margin: 0 0 4px; color: #1a1a2e; }
        .header p { margin: 2px 0; color: #666; }
        .header .title { font-weight: bold; font-size: 13px; margin-top: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1a1a2e; color: #fff; padding: 6px 5px; text-align: left; font-size: 10px; }
        td { padding: 5px; border-bottom: 1px solid #ddd; font-size: 10px; vertical-align: top; }
        tr:nth-child(even) td { background: #f6f6f9; }
        .meta { margin-bottom: 12px; font-size: 11px; color: #444; }
        .footer { margin-top: 24px; text-align: right; font-size: 11px; color: #666; }
        .total { margin-top: 10px; font-weight: bold; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $registrations->first()->school->name ?? 'Laporan Rekap' }}</h1>
        <p>{{ $registrations->first()->school->address ?? '' }}</p>
        <div class="title">REKAP SISWA DITERIMA</div>
    </div>

    <div class="meta">
        <strong>Periode:</strong> {{ $registrations->first()->registrationPeriod->name ?? 'Semua Periode' }}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Total Siswa Diterima:</strong> {{ $registrations->count() }} orang
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>No. Registrasi</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Jenjang</th>
                <th>Jalur</th>
                <th>Sekolah</th>
                <th>Jurusan Diterima</th>
                <th>Periode</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($registrations as $i => $reg)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $reg->registration_number }}</td>
                    <td>{{ $reg->applicant->student_number ?? '-' }}</td>
                    <td>{{ $reg->applicant->full_name ?? '-' }}</td>
                    <td>{{ $reg->registrationPeriod?->schoolLevel?->name ?? '-' }}</td>
                    <td>{{ $reg->registrationTrack->name ?? '-' }}</td>
                    <td>{{ $reg->school->name ?? '-' }}</td>
                    <td>{{ $reg->finalMajor->name ?? '-' }}</td>
                    <td>{{ $reg->registrationPeriod->name ?? '-' }}</td>
                    <td>{{ $reg->status === 're_registration_complete' ? 'Terdaftar' : 'Diterima' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">Total: {{ $registrations->count() }} siswa</div>

    <div class="footer">
        <p>Dicetak: {{ $exportedAt->format('d F Y H:i') }}</p>
        <p>( {{ $registrations->first()->school->name ?? 'Panitia SPMB' }} )</p>
    </div>
</body>
</html>

<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>No. Registrasi</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>Email</th>
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
                <td>{{ $reg->applicant->user->email ?? '-' }}</td>
                <td>{{ $reg->registrationPeriod?->schoolLevel?->name ?? '-' }}</td>
                <td>{{ $reg->registrationTrack->name ?? '-' }}</td>
                <td>{{ $reg->school->name ?? '-' }}</td>
                <td>{{ $reg->finalMajor->name ?? '-' }}</td>
                <td>{{ $reg->registrationPeriod->name ?? '-' }}</td>
                <td>
                    @if ($reg->status === 're_registration_complete')
                        Terdaftar
                    @else
                        Diterima
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

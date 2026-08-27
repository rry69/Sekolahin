<div class="breadcrumb">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <span class="sep">/</span>
  <a href="{{ route('admin.re-registrations.index') }}">Daftar Ulang</a>
  <span class="sep">/</span>
  <span>Detail</span>
</div>

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;">
  <div>
    <h1 class="page-title">Detail Daftar Ulang</h1>
    <p style="font-size:13px;color:var(--tx2);margin-top:4px;">No. Registrasi: {{ $reRegistration->registration->registration_number }}</p>
  </div>
  @php
    $statusMap = [
      'pending' => 'status-pending',
      'completed' => 'status-accepted',
    ];
  @endphp
  <span class="status-badge {{ $statusMap[$reRegistration->status] ?? 'status-pending' }}">{{ ucfirst($reRegistration->status) }}</span>
</div>

@if (session('success'))
<div class="ajax-success alert alert-success">
  {{ session('success') }}
</div>
@endif

<div style="border-bottom:1px solid var(--border);padding-bottom:20px;margin-bottom:20px;">
  <h4 style="font-size:11px;font-weight:600;color:var(--tx3);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;">Informasi Pendaftar</h4>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div>
      <p style="font-size:12px;color:var(--tx2);margin-bottom:4px;">Nama Lengkap</p>
      <p style="font-weight:500;">{{ $reRegistration->registration->applicant->full_name }}</p>
    </div>
    <div>
      <p style="font-size:12px;color:var(--tx2);margin-bottom:4px;">Email</p>
      <p style="font-weight:500;">{{ $reRegistration->registration->applicant->user->email }}</p>
    </div>
    <div>
      <p style="font-size:12px;color:var(--tx2);margin-bottom:4px;">Jenjang</p>
      <p style="font-weight:500;">{{ $reRegistration->registration->registrationPeriod->schoolLevel->name }}</p>
    </div>
    <div>
      <p style="font-size:12px;color:var(--tx2);margin-bottom:4px;">Jalur Pendaftaran</p>
      <p style="font-weight:500;">{{ $reRegistration->registration->registrationTrack->name }}</p>
    </div>
  </div>
</div>

@if($reRegistration->verification_code)
<div style="border-bottom:1px solid var(--border);padding-bottom:20px;margin-bottom:20px;">
  <h4 style="font-size:11px;font-weight:600;color:var(--tx3);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;">Kode Verifikasi</h4>
  <p style="font-family:monospace;letter-spacing:2px;font-size:18px;font-weight:bold;">{{ $reRegistration->verification_code }}</p>
  <p style="font-size:12px;color:var(--tx3);margin-top:4px;">Kode pada kartu daftar ulang</p>
</div>
@endif

<div style="border-bottom:1px solid var(--border);padding-bottom:20px;margin-bottom:20px;">
  <h4 style="font-size:11px;font-weight:600;color:var(--tx3);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;">Status Verifikasi</h4>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div>
      <p style="font-size:12px;color:var(--tx2);margin-bottom:4px;">Tanggal Submit</p>
      <p style="font-weight:500;">{{ $reRegistration->submitted_at ? $reRegistration->submitted_at->format('d M Y H:i') : '-' }}</p>
    </div>
    @if ($reRegistration->verified_at)
      <div>
        <p style="font-size:12px;color:var(--tx2);margin-bottom:4px;">Tanggal Verifikasi</p>
        <p style="font-weight:500;">{{ $reRegistration->verified_at->format('d M Y H:i') }}</p>
      </div>
      <div>
        <p style="font-size:12px;color:var(--tx2);margin-bottom:4px;">Diverifikasi Oleh</p>
        <p style="font-weight:500;">{{ $reRegistration->verifier->name ?? '-' }}</p>
      </div>
    @endif
    @if ($reRegistration->notes)
      <div style="grid-column:1/-1;">
        <p style="font-size:12px;color:var(--tx2);margin-bottom:4px;">Catatan</p>
        <p style="font-weight:500;">{{ $reRegistration->notes }}</p>
      </div>
    @endif
  </div>
</div>

<div style="display:flex;justify-content:space-between;align-items:center;">
  <a href="{{ route('admin.re-registrations.index') }}" class="btn btn-outline">Kembali</a>
  @if ($reRegistration->status === 'pending')
    <form action="{{ route('admin.re-registrations.verify', $reRegistration) }}" method="POST" onsubmit="return confirm('Yakin ingin verifikasi daftar ulang ini?')">
      @csrf
      <button type="submit" class="btn btn-primary">Verifikasi Daftar Ulang</button>
    </form>
  @endif
</div>

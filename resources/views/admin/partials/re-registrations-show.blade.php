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
      'rejected' => 'status-rejected',
    ];
    $statusLabels = ['pending' => 'Pending', 'completed' => 'Selesai', 'rejected' => 'Ditolak'];
  @endphp
  <span class="status-badge {{ $statusMap[$reRegistration->status] ?? 'status-pending' }}">{{ $statusLabels[$reRegistration->status] ?? ucfirst($reRegistration->status) }}</span>
</div>

@if (session('success'))
<div class="ajax-success alert alert-success">
  {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="ajax-success alert alert-error">
  {{ session('error') }}
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
    <div style="display:flex;gap:8px;">
      <button type="button" onclick="showReRegRejectModal({{ $reRegistration->id }})" class="btn btn-danger">Tolak Daftar Ulang</button>
      <form action="{{ route('admin.re-registrations.verify', $reRegistration) }}" method="POST" onsubmit="return confirm('Yakin ingin verifikasi daftar ulang ini?')">
        @csrf
        <button type="submit" class="btn btn-primary">Verifikasi Daftar Ulang</button>
      </form>
    </div>
  @endif
</div>

<div id="reRegRejectModal" class="modal-overlay" style="display:none;">
  <div class="modal-card">
    <div class="modal-head">
      <div style="flex:1;">
        <h3 class="modal-title">Tolak Daftar Ulang</h3>
      </div>
    </div>
    <form id="reRegRejectForm" method="POST">
      @csrf
      <div style="margin-bottom:16px;">
        <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;">Catatan / Alasan Penolakan</label>
        <textarea name="notes" rows="4" style="width:100%;padding:8px 12px;border:1px solid var(--input-border);border-radius:6px;font-size:13px;font-family:inherit;background:var(--input-bg);color:var(--tx-body);" required></textarea>
      </div>
      <div style="display:flex;gap:8px;justify-content:flex-end;">
        <button type="button" onclick="hideReRegRejectModal()" class="btn btn-outline">Batal</button>
        <button type="submit" class="btn btn-danger">Tolak</button>
      </div>
    </form>
  </div>
</div>

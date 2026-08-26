@extends('layouts.dashboard')
@section('title', 'Detail Jurusan')
@section('content')

<style>
  .mjr-card { background: var(--panel); border: 1px solid var(--border); border-radius: 12px; }
  .mjr-card-body { padding: 22px; }
  .mjr-stat { flex: 1; min-width: 150px; background: var(--panel-2); border: 1px solid var(--border); border-radius: 10px; padding: 16px; }
  .mjr-stat .lbl { font-size: 12px; color: var(--tx3); margin-bottom: 4px; }
  .mjr-stat .val { font-size: 22px; font-weight: 700; color: var(--tx1); }
  .mjr-stat .val.green { color: var(--badge-accepted-fg); }
  .mjr-stat .val.yellow { color: var(--badge-pending-fg); }
  .mjr-stat .val.red { color: var(--badge-rejected-fg); }
  .mjr-stat .val.blue { color: var(--badge-verified-fg); }
  .mjr-info { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px 24px; }
  .mjr-info .item { display: flex; justify-content: space-between; gap: 12px; font-size: 13px; padding: 8px 0; border-bottom: 1px solid var(--hairline); }
  .mjr-info .item .k { color: var(--tx3); }
  .mjr-info .item .v { font-weight: 500; color: var(--tx1); text-align: right; }
  .status-active { background: var(--badge-accepted-bg); color: var(--badge-accepted-fg); }
  .status-inactive { background: var(--badge-rejected-bg); color: var(--badge-rejected-fg); }
  @media (max-width: 640px) { .mjr-info { grid-template-columns: 1fr; } }
</style>

<div class="breadcrumb">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <span class="sep">/</span>
  <a href="{{ route('admin.majors.index') }}">Kelola Jurusan</a>
  <span class="sep">/</span>
  <span>Detail Jurusan</span>
</div>

@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif

<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:18px;">
  <div>
    <h1 class="page-title" style="margin-bottom:2px;">{{ $major->name }}</h1>
    <p style="font-size:13px;color:var(--tx2);">
      <span class="status-badge status-{{ $major->is_active ? 'active' : 'inactive' }}">{{ $major->statusLabel() }}</span>
      &nbsp;{{ $major->school->name ?? '-' }} · {{ $major->schoolLevel->name ?? '-' }} · Kode {{ $major->code }}
      @if ($major->order !== null) · Urutan {{ $major->order }} @endif
    </p>
  </div>
  <div style="display:flex;gap:8px;flex-wrap:wrap;">
    <a href="{{ route('admin.majors.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left" style="font-size:10px;"></i> Kembali</a>
    <a href="{{ route('admin.majors.edit', $major) }}" class="btn btn-outline"><i class="fa-solid fa-pen" style="font-size:10px;"></i> Edit</a>
    <form action="{{ route('admin.majors.toggle-status', $major) }}" method="POST" style="display:inline;">
      @csrf
      <button type="submit" class="btn {{ $major->is_active ? 'btn-outline' : 'btn-primary' }}">
        <i class="fa-solid fa-{{ $major->is_active ? 'toggle-on' : 'toggle-off' }}" style="font-size:11px;"></i>
        {{ $major->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
      </button>
    </form>
    <button type="button" class="btn btn-danger" onclick="openMajorDelete({{ $major->id }}, {{ json_encode($major->name) }})">
      <i class="fa-solid fa-trash-can" style="font-size:10px;"></i> Hapus
    </button>
  </div>
</div>

<div class="mjr-card" style="margin-bottom:16px;">
  <div class="mjr-card-body">
    <h4 style="margin:0 0 14px;font-size:13px;font-weight:600;color:var(--tx3);text-transform:uppercase;letter-spacing:.4px;">Ringkasan Pendaftar</h4>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
      <div class="mjr-stat"><div class="lbl">Total Kuota</div><div class="val">{{ $statistics['total_quota'] }}</div></div>
      <div class="mjr-stat"><div class="lbl">Sisa Kuota</div><div class="val">{{ $statistics['available_quota'] }}</div></div>
      <div class="mjr-stat"><div class="lbl">Total Pendaftar</div><div class="val">{{ $statistics['total_applicants'] }}</div></div>
      <div class="mjr-stat"><div class="lbl">Pending</div><div class="val yellow">{{ $statistics['pending'] }}</div></div>
      <div class="mjr-stat"><div class="lbl">Terverifikasi</div><div class="val blue">{{ $statistics['verified'] }}</div></div>
      <div class="mjr-stat"><div class="lbl">Diterima</div><div class="val green">{{ $statistics['accepted'] }}</div></div>
      <div class="mjr-stat"><div class="lbl">Ditolak</div><div class="val red">{{ $statistics['rejected'] }}</div></div>
    </div>
  </div>
</div>

@if (isset($statistics['by_track']))
<div class="mjr-card" style="margin-bottom:16px;">
  <div class="mjr-card-body">
    <h4 style="margin:0 0 14px;font-size:13px;font-weight:600;color:var(--tx3);text-transform:uppercase;letter-spacing:.4px;">Kuota per Jalur</h4>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;">
      @foreach($statistics['by_track'] as $trackName => $row)
        <div class="mjr-stat" style="background:var(--panel);">
          <div class="lbl">{{ $trackName }}</div>
          <div class="val" style="font-size:16px;">{{ $row['quota'] }}</div>
          <div style="font-size:12px;color:var(--tx3);margin-top:4px;">Terisi {{ $row['accepted'] }} · Sisa <span style="font-weight:600;color:{{ $row['sisa']===0 ? 'var(--badge-rejected-fg)' : 'var(--badge-accepted-fg)' }};">{{ $row['sisa'] }}</span></div>
        </div>
      @endforeach
    </div>
  </div>
</div>
@endif

<div class="mjr-card" style="margin-bottom:16px;">
  <div class="mjr-card-body">
    <h4 style="margin:0 0 14px;font-size:13px;font-weight:600;color:var(--tx3);text-transform:uppercase;letter-spacing:.4px;">Informasi Jurusan</h4>
    <div class="mjr-info">
      <div class="item"><span class="k">Nama Jurusan</span><span class="v">{{ $major->name }}</span></div>
      <div class="item"><span class="k">Kode</span><span class="v">{{ $major->code }}</span></div>
      <div class="item"><span class="k">Jenjang</span><span class="v">{{ $major->schoolLevel->name ?? '-' }}</span></div>
      <div class="item"><span class="k">Sekolah</span><span class="v">{{ $major->school->name ?? '-' }}</span></div>
      <div class="item"><span class="k">Status</span><span class="v">{{ $major->statusLabel() }}</span></div>
      <div class="item"><span class="k">Urutan</span><span class="v">{{ $major->order ?? '-' }}</span></div>
      @if($major->description)
      <div class="item" style="grid-column:1/-1;flex-direction:column;align-items:flex-start;">
        <span class="k">Deskripsi</span>
        <span class="v" style="text-align:left;font-weight:400;">{{ $major->description }}</span>
      </div>
      @endif
    </div>
  </div>
</div>

<div class="mjr-card">
  <div class="mjr-card-body">
    <h4 style="margin:0 0 14px;font-size:13px;font-weight:600;color:var(--tx3);text-transform:uppercase;letter-spacing:.4px;">Daftar Pendaftar</h4>
    <div style="overflow-x:auto;">
      <table class="data-table">
        <thead>
          <tr><th>Nama</th><th>Jalur</th><th>Status</th></tr>
        </thead>
        <tbody>
          @forelse ($registrations as $registration)
            <tr>
              <td>{{ $registration->applicant->full_name ?? '-' }}</td>
              <td>{{ $registration->registrationTrack->name ?? '-' }}</td>
              <td><span class="status-badge status-{{ $registration->status }}">{{ \App\Models\Registration::statusLabel($registration->status) }}</span></td>
            </tr>
          @empty
            <tr><td colspan="3" class="empty-state">Belum ada pendaftar</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div style="margin-top:14px;">{{ $registrations->links('vendor.pagination.egglore') }}</div>
  </div>
</div>

{{-- Modal konfirmasi hapus --}}
<div id="majorDeleteModal" class="modal-overlay" style="display:none;">
  <div class="modal-card">
    <div class="modal-head">
      <div class="modal-icon modal-icon-amber">🗑️</div>
      <div>
        <h3 class="modal-title">Hapus jurusan?</h3>
        <p class="modal-text">Yakin ingin menghapus jurusan <strong id="majorDeleteName"></strong>? Aksi ini tidak dapat dibatalkan.</p>
        <p class="modal-sub">Jurusan yang masih memiliki pendaftar tidak dapat dihapus — nonaktifkan saja.</p>
      </div>
    </div>
    <div class="modal-actions">
      <button type="button" onclick="closeMajorDelete()" class="modal-btn-cancel">Batal</button>
      <form id="majorDeleteForm" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
      </form>
    </div>
  </div>
</div>

<script>
function openMajorDelete(id, name) {
  var modal = document.getElementById('majorDeleteModal');
  var nameEl = document.getElementById('majorDeleteName');
  var form = document.getElementById('majorDeleteForm');
  if (nameEl) nameEl.textContent = name;
  if (form) form.action = '/admin/majors/' + id;
  if (modal) modal.style.display = 'flex';
}
function closeMajorDelete() {
  var modal = document.getElementById('majorDeleteModal');
  if (modal) modal.style.display = 'none';
}
document.addEventListener('click', function (e) {
  var modal = document.getElementById('majorDeleteModal');
  if (modal && modal.style.display === 'flex' && e.target === modal) closeMajorDelete();
});
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') closeMajorDelete();
});
</script>
@endsection

@extends('layouts.dashboard')
@section('title', 'Detail Jurusan')
@section('content')
<style>
  /* ===================== DETAIL JURUSAN — Bringova (no cards, scoped) ===================== */
  .mjd {
    --coral: #FF6B6B;
    --coral-soft: #FFE5E3;
    --coral-2: #FF8E6E;
    --amber: #F59E0B;
    --amber-soft: #FEF3C7;
    --green: #10B981;
    --green-soft: #D1FAE5;
    --blue: #3B82F6;
    --blue-soft: #DBEAFE;
    --purple: #8B5CF6;
    --purple-soft: #EDE9FE;
    --red: #EF4444;
    --red-soft: #FEE2E2;
    --gray: #6b7280;
    --gray-soft: #F3F4F6;
    --ink: #1a1a2e;
    --muted: #8a8f9d;
    --divider: rgba(26, 26, 46, 0.10);
    position: relative;
    border-radius: 24px;
    padding: 28px 28px 40px;
    background: #f6f7fb;
  }
  .mjd .m-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
  .mjd .m-crumb a { color: var(--coral); text-decoration: none; }
  .mjd .m-crumb a:hover { text-decoration: underline; }
  .mjd .m-crumb .sep { color: #d3d6de; }
  .mjd .m-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 2px; }
  .mjd .m-meta { font-size: 13px; color: var(--muted); line-height: 1.5; }
  .mjd .m-meta .dot { color: #d3d6de; margin: 0 4px; }
  .mjd .m-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px; font-weight: 500; }
  .mjd .m-alert i { margin-top: 2px; }
  .mjd .m-alert.success { background: var(--green-soft); color: var(--green); }
  .mjd .m-alert.error { background: var(--red-soft); color: var(--red); }
  .mjd .m-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 22px; }
  .mjd .m-head-actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
  .mjd .m-btn { display: inline-flex; align-items: center; gap: 7px; border: none; cursor: pointer; border-radius: 11px; padding: 10px 17px; font-size: 13px; font-weight: 700; text-decoration: none; transition: transform .15s ease, filter .15s ease, background-color .15s ease; }
  .mjd .m-btn:hover { transform: translateY(-1px); }
  .mjd .m-btn.coral { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 8px 18px -8px rgba(255,107,107,0.6); }
  .mjd .m-btn.coral:hover { filter: brightness(1.04); }
  .mjd .m-btn.ghost { background: rgba(255,255,255,0.65); color: var(--ink); box-shadow: 0 2px 10px -8px rgba(26,26,46,0.3); }
  .mjd .m-btn.ghost:hover { background: #fff; color: var(--coral); }
  .mjd .m-btn.red { background: var(--red); color: #fff; }
  .mjd .m-btn.red:hover { background: #dc2626; }
  .mjd .m-btn.amber { background: var(--amber); color: #fff; }
  .mjd .m-btn.amber:hover { background: #d97706; }
  .mjd .m-btn.sm { padding: 7px 12px; font-size: 12px; border-radius: 9px; }
  .mjd .m-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 700; white-space: nowrap; }
  .mjd .m-pill.green { background: transparent; border: 1px solid currentColor; color: var(--green); }
  .mjd .m-pill.red { background: transparent; border: 1px solid currentColor; color: var(--red); }
  .mjd .m-pill.blue { background: transparent; border: 1px solid currentColor; color: var(--blue); }
  .mjd .m-pill.amber { background: transparent; border: 1px solid currentColor; color: #b45309; }
  .mjd .m-pill.gray { background: transparent; border: 1px solid currentColor; color: var(--gray); }
  .mjd .m-pill.coral { background: transparent; border: 1px solid currentColor; color: var(--coral); }
  .mjd .m-sec { border-top: 1px solid var(--divider); padding: 24px 0 6px; }
  .mjd .m-sec:first-of-type { border-top: none; padding-top: 4px; }
  .mjd .m-sec-title { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--ink); text-transform: uppercase; letter-spacing: .4px; margin-bottom: 16px; }
  .mjd .m-sec-title i { color: var(--coral); font-size: 13px; }
  .mjd .m-summary { display: flex; gap: 18px; flex-wrap: wrap; }
  .mjd .m-sum { display: flex; align-items: center; gap: 12px; }
  .mjd .m-sum-ic { flex: 0 0 auto; width: 46px; height: 46px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
  .mjd .m-sum-ic.coral { background: var(--coral-soft); color: var(--coral); }
  .mjd .m-sum-ic.green { background: var(--green-soft); color: var(--green); }
  .mjd .m-sum-ic.amber { background: var(--amber-soft); color: #b45309; }
  .mjd .m-sum-ic.blue { background: var(--blue-soft); color: var(--blue); }
  .mjd .m-sum-ic.red { background: var(--red-soft); color: var(--red); }
  .mjd .m-sum-ic.gray { background: var(--gray-soft); color: var(--gray); }
  .mjd .m-sum-lbl { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .3px; }
  .mjd .m-sum-val { font-size: 19px; font-weight: 800; color: var(--ink); }
  .mjd .m-quotas-min { display: flex; gap: 6px; flex-wrap: wrap; font-size: 11.5px; color: var(--muted); align-items: center; }
  .mjd .m-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px 24px; }
  .mjd .m-item { display: flex; justify-content: space-between; gap: 12px; font-size: 13px; padding: 10px 2px; border-bottom: 1px solid var(--divider); }
  .mjd .m-item:last-child { border-bottom: none; }
  .mjd .m-item .k { color: var(--muted); font-weight: 500; }
  .mjd .m-item .v { font-weight: 600; color: var(--ink); text-align: right; }
  .mjd .m-item.full { grid-column: 1 / -1; flex-direction: column; align-items: flex-start; }
  .mjd .m-item.full .v { text-align: left; font-weight: 400; }
  .mjd .m-list { display: flex; flex-direction: column; }
  .mjd .m-row { display: flex; align-items: center; gap: 14px; padding: 14px 4px; border-bottom: 1px solid var(--divider); }
  .mjd .m-row:last-child { border-bottom: none; }
  .mjd .m-row-ic { flex: 0 0 auto; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 15px; background: var(--gray-soft); color: var(--gray); }
  .mjd .m-row-body { flex: 1; min-width: 0; }
  .mjd .m-row-name { font-size: 13.5px; font-weight: 700; color: var(--ink); }
  .mjd .m-row-sub { font-size: 12px; color: var(--muted); margin-top: 2px; }
  .mjd .m-row-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
  .mjd .m-empty { text-align: center; color: var(--muted); font-size: 13px; padding: 28px 0; }
  .mjd .m-empty i { display: block; font-size: 24px; margin-bottom: 8px; color: #d3d6de; }
  .mjd .m-pager { margin-top: 18px; display: flex; justify-content: center; }
  .mjd .m-pager > nav { display: flex; justify-content: center; }
  .mjd .m-modal-backdrop { position: fixed; inset: 0; z-index: 90; background: rgba(26,26,46,0.36); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; padding: 16px; }
  .mjd .m-modal-backdrop.is-open { display: flex; }
  .mjd .m-modal { width: 100%; max-width: 400px; background: #fff; border-radius: 18px; padding: 22px; box-shadow: 0 24px 60px -18px rgba(26,26,46,0.4); animation: mModalPop .2s cubic-bezier(.22,1.2,.36,1); }
  @keyframes mModalPop { from { opacity: 0; transform: scale(0.97) translateY(4px); } to { opacity: 1; transform: scale(1) translateY(0); } }
  .mjd .m-modal-body { display: flex; align-items: flex-start; gap: 13px; margin-bottom: 18px; }
  .mjd .m-modal-ic { flex: 0 0 auto; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 17px; background: var(--red-soft); color: var(--red); }
  .mjd .m-modal-title { font-size: 15px; font-weight: 700; color: var(--ink); }
  .mjd .m-modal-msg { font-size: 13px; color: var(--muted); margin-top: 3px; line-height: 1.5; }
  .mjd .m-modal-actions { display: flex; justify-content: flex-end; gap: 8px; }
  .mjd .m-modal-actions .m-btn-ghost { background: transparent; color: var(--muted); }
  .mjd .m-modal-actions .m-btn-ghost:hover { color: var(--ink); }
  @media (max-width: 720px) {
    .mjd { padding: 20px 16px 32px; }
    .mjd .m-grid { grid-template-columns: 1fr; }
    .mjd .m-row { flex-wrap: wrap; }
    .mjd .m-row-actions { width: 100%; justify-content: flex-end; }
  }
</style>

<div class="mjd">
  <div class="m-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <a href="{{ route('admin.majors.index') }}">Kelola Jurusan</a>
    <span class="sep">/</span>
    <span>Detail Jurusan</span>
  </div>

  <div class="m-head">
    <div>
      <h1 class="m-title">{{ $major->name }}</h1>
      <p class="m-meta">
        <span class="m-pill {{ $major->is_active ? 'green' : 'red' }}"><x-hi :name="$major->is_active ? 'checkmark-circle-02' : 'cancel-circle'" /> {{ $major->statusLabel() }}</span>
        <span class="dot">·</span>{{ $major->school->name ?? '-' }}
        <span class="dot">·</span>{{ $major->schoolLevel->name ?? '-' }}
        <span class="dot">·</span><span style="color:var(--muted)">Kode {{ $major->code }}</span>
        @if ($major->order !== null) <span class="dot">·</span><span style="color:var(--muted)">#{{ $major->order }}</span> @endif
      </p>
    </div>
    <div class="m-head-actions">
      <a href="{{ route('admin.majors.index') }}" class="m-btn ghost sm"><x-hi name="arrow-left-01" /> Kembali</a>
      <a href="{{ route('admin.majors.edit', $major) }}" class="m-btn ghost sm"><x-hi name="edit-02" /> Edit</a>
      <form action="{{ route('admin.majors.toggle-status', $major) }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="m-btn {{ $major->is_active ? 'ghost' : 'coral' }} sm">
          <x-hi :name="$major->is_active ? 'toggle-on' : 'toggle-off'" />
          {{ $major->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
        </button>
      </form>
      <button type="button" class="m-btn red sm" onclick="openMajorDelete({{ $major->id }}, {{ json_encode($major->name) }})">
        <x-hi name="delete-02" /> Hapus
      </button>
    </div>
  </div>

  @if (session('success'))
    <div class="m-alert success"><x-hi name="checkmark-circle-02" /><span>{{ session('success') }}</span></div>
  @endif
  @if (session('error'))
    <div class="m-alert error"><x-hi name="alert-02" /><span>{{ session('error') }}</span></div>
  @endif

  <div class="m-sec">
    <div class="m-sec-title"><x-hi name="chart-up" /> Ringkasan Pendaftar</div>
    <div class="m-summary">
      <div class="m-sum"><span class="m-sum-ic coral"><x-hi name="layers-01" /></span><div><div class="m-sum-lbl">Total Kuota</div><div class="m-sum-val">{{ $statistics['total_quota'] }}</div></div></div>
      <div class="m-sum"><span class="m-sum-ic green"><x-hi name="checkmark" /></span><div><div class="m-sum-lbl">Sisa Kuota</div><div class="m-sum-val">{{ $statistics['available_quota'] }}</div></div></div>
      <div class="m-sum"><span class="m-sum-ic gray"><x-hi name="user-multiple-02" /></span><div><div class="m-sum-lbl">Total Pendaftar</div><div class="m-sum-val">{{ $statistics['total_applicants'] }}</div></div></div>
      <div class="m-sum"><span class="m-sum-ic amber"><x-hi name="clock-01" /></span><div><div class="m-sum-lbl">Pending</div><div class="m-sum-val">{{ $statistics['pending'] }}</div></div></div>
      <div class="m-sum"><span class="m-sum-ic blue"><x-hi name="file-verified" /></span><div><div class="m-sum-lbl">Terverifikasi</div><div class="m-sum-val">{{ $statistics['verified'] }}</div></div></div>
      <div class="m-sum"><span class="m-sum-ic green"><x-hi name="user-check-01" /></span><div><div class="m-sum-lbl">Diterima</div><div class="m-sum-val">{{ $statistics['accepted'] }}</div></div></div>
      <div class="m-sum"><span class="m-sum-ic red"><x-hi name="user-remove-01" /></span><div><div class="m-sum-lbl">Ditolak</div><div class="m-sum-val">{{ $statistics['rejected'] }}</div></div></div>
    </div>
  </div>

  @if (isset($statistics['by_track']))
  <div class="m-sec">
    <div class="m-sec-title"><x-hi name="route-01" /> Kuota per Jalur</div>
    <div class="m-quotas-min">
      @foreach($statistics['by_track'] as $trackName => $row)
        <span>{{ $trackName }} {{ $row['quota'] }} <span style="color:var(--muted)">terisi {{ $row['accepted'] }}, sisa {{ $row['sisa'] }}</span></span>
        <span style="color:var(--divider)">·</span>
      @endforeach
    </div>
  </div>
  @endif

  <div class="m-sec">
    <div class="m-sec-title"><x-hi name="information-circle" /> Informasi Jurusan</div>
    <div class="m-grid">
      <div class="m-item"><span class="k">Nama Jurusan</span><span class="v">{{ $major->name }}</span></div>
      <div class="m-item"><span class="k">Kode</span><span class="v"><span style="color:var(--muted)">· {{ $major->code }}</span></span></div>
      <div class="m-item"><span class="k">Jenjang</span><span class="v">{{ $major->schoolLevel->name ?? '-' }}</span></div>
      <div class="m-item"><span class="k">Sekolah</span><span class="v">{{ $major->school->name ?? '-' }}</span></div>
      <div class="m-item"><span class="k">Status</span><span class="v"><span class="m-pill {{ $major->is_active ? 'green' : 'red' }}">{{ $major->statusLabel() }}</span></span></div>
      <div class="m-item"><span class="k">Urutan</span><span class="v"><span style="color:var(--muted)">{{ $major->order !== null ? '#'.$major->order : '—' }}</span></span></div>
      @if($major->description)
      <div class="m-item full"><span class="k">Deskripsi</span><span class="v">{{ $major->description }}</span></div>
      @endif
    </div>
  </div>

  <div class="m-sec">
    <div class="m-sec-title"><x-hi name="user-multiple-02" /> Daftar Pendaftar</div>
    @if ($registrations->isEmpty())
      <div class="m-empty"><x-hi name="folder-open" />Belum ada pendaftar</div>
    @else
      <div class="m-list">
        @foreach ($registrations as $registration)
          <div class="m-row">
            <span class="m-row-ic"><x-hi name="user" /></span>
            <div class="m-row-body">
              <div class="m-row-name">{{ $registration->applicant->full_name ?? '-' }}</div>
              <div class="m-row-sub">
                <span style="color:var(--muted)">{{ $registration->registrationTrack->name ?? '-' }}</span>
                <span style="color:var(--divider)">·</span>
                <span style="color:{{ $registration->status === 'accepted' || $registration->status === 're_registration_complete' ? 'var(--green)' : ($registration->status === 'pending' ? '#b45309' : ($registration->status === 'rejected' ? 'var(--red)' : 'var(--muted)')) }};font-weight:600">{{ \App\Models\Registration::statusLabel($registration->status) }}</span>
              </div>
            </div>
            <div class="m-row-actions">
              <a href="{{ route('admin.registrations.show', $registration) }}" class="m-btn ghost sm"><x-hi name="view" /> Detail</a>
            </div>
          </div>
        @endforeach
      </div>
      <div class="m-pager">{{ $registrations->links('vendor.pagination.bringova') }}</div>
    @endif
  </div>

{{-- ================== MODAL HAPUS (Bringova) ================== --}}
<div id="majorDeleteModal" class="m-modal-backdrop" aria-hidden="true">
  <div class="m-modal" role="dialog" aria-modal="true">
    <div class="m-modal-body">
      <div class="m-modal-ic"><x-hi name="delete-02" /></div>
      <div style="flex:1;min-width:0">
        <h3 class="m-modal-title">Hapus jurusan?</h3>
        <p class="m-modal-text" style="font-size:13px;color:var(--muted);margin-top:3px">Yakin ingin menghapus jurusan <strong id="majorDeleteName" style="color:var(--ink)"></strong>? Aksi ini tidak dapat dibatalkan.</p>
        <p style="font-size:12px;color:var(--muted);margin-top:6px">Jurusan yang masih memiliki pendaftar tidak dapat dihapus — nonaktifkan saja.</p>
      </div>
    </div>
    <div class="m-modal-actions">
      <button type="button" onclick="closeMajorDelete()" class="m-btn ghost sm m-btn-ghost">Batal</button>
      <form id="majorDeleteForm" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="m-btn red sm">Ya, Hapus</button>
      </form>
    </div>
  </div>
</div>
</div>

<script>
(function(){
  var pending = null;
  window.openMajorDelete = function(id, name){
    var modal = document.getElementById('majorDeleteModal');
    var nameEl = document.getElementById('majorDeleteName');
    var form = document.getElementById('majorDeleteForm');
    if(nameEl) nameEl.textContent = name;
    if(form) form.action = '/admin/majors/' + id;
    if(modal){ modal.classList.add('is-open'); modal.setAttribute('aria-hidden','false'); modal.style.display='flex'; }
  };
  window.closeMajorDelete = function(){
    var modal = document.getElementById('majorDeleteModal');
    if(modal){ modal.classList.remove('is-open'); modal.setAttribute('aria-hidden','true'); modal.style.display='none'; }
  };
  var modal = document.getElementById('majorDeleteModal');
  if(modal) modal.addEventListener('click', function(e){ if(e.target===this) closeMajorDelete(); });
  document.addEventListener('keydown', function(e){ if(e.key==='Escape'){ var m=document.getElementById('majorDeleteModal'); if(m&&m.classList.contains('is-open')) closeMajorDelete(); }});
})();
</script>
@endsection
<style>
  .prd-card { background: var(--panel); border: 1px solid var(--border); border-radius: 12px; }
  .prd-toolbar { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; padding: 16px 18px; border-bottom: 1px solid var(--hairline); }
  .prd-toolbar .grow { flex: 1; min-width: 200px; }
  .prd-field { display: flex; flex-direction: column; gap: 5px; }
  .prd-field label { font-size: 11px; font-weight: 600; color: var(--tx3); text-transform: uppercase; letter-spacing: .4px; }
  .prd-input { width: 100%; padding: 8px 12px; border: 1px solid var(--input-border); border-radius: 8px; font-size: 13px; background: var(--input-bg); color: var(--tx-body); box-sizing: border-box; }
  .prd-input:focus { outline: none; border-color: var(--accent); }
  .prd-table-wrap { overflow-x: auto; }
  .prd-summary { padding: 12px 18px; border-bottom: 1px solid var(--hairline); font-size: 12px; color: var(--tx2); display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; }
  .prd-footer { padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; border-top: 1px solid var(--hairline); }
  .prd-skeleton td { height: 40px; }
  .skel { display: block; height: 10px; border-radius: 9999px; background: var(--panel-2); }
  @media (max-width: 640px) { .prd-toolbar { padding: 12px; } }
  /* Badge status — 4 warna berbeda sesuai EGGPLORE */
  .badge-nonaktif { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
  .badge-belum { background: #e3f0fc; color: #248fe6; border: 1px solid #bfdbfe; }
  .badge-berlangsung { background: #e1f5f1; color: #0f7a5f; border: 1px solid #a7f3d0; }
  .badge-selesai { background: #fbf3d9; color: #92400e; border: 1px solid #fde68a; }
  .kuota-ok { color: var(--badge-accepted-fg); }
  .kuota-warn { color: var(--badge-pending-fg); }
  .kuota-full { color: var(--badge-rejected-fg); }
  /* Modal destruktif 2 langkah */
  .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); backdrop-filter: blur(2px); display: flex; align-items: center; justify-content: center; z-index: 60; padding: 16px; }
  .modal-card { background: var(--panel); border: 1px solid var(--border); border-radius: 12px; max-width: 440px; width: 100%; padding: 20px; box-shadow: var(--shadow-lg); }
  .modal-head { display: flex; gap: 14px; align-items: flex-start; }
  .modal-icon { width: 36px; height: 36px; border-radius: 50%; display: grid; place-items: center; font-size: 16px; flex-shrink: 0; }
  .modal-icon-amber { background: #fef3c7; color: #d97706; }
  .modal-icon-red { background: #fee2e2; color: #dc2626; }
  .modal-title { font-size: 15px; font-weight: 600; color: var(--tx1); margin: 0 0 4px; }
  .modal-text { font-size: 13px; color: var(--tx2); line-height: 1.5; margin: 0; }
  .modal-sub { font-size: 12px; color: var(--tx3); margin-top: 8px; line-height: 1.4; }
  .modal-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px; flex-wrap: wrap; }
  .modal-btn-cancel { padding: 7px 14px; border-radius: 8px; border: 1px solid var(--input-border); background: var(--input-bg); color: var(--tx2); font-size: 13px; cursor: pointer; }
  .modal-btn-cancel:hover { border-color: var(--accent); color: var(--accent); }
</style>

<div class="breadcrumb">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <span class="sep">/</span>
  <span>Periode Pendaftaran</span>
</div>

<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:18px;">
  <div>
    <h1 class="page-title" style="margin-bottom:2px;">Periode Pendaftaran</h1>
    <p style="font-size:13px;color:var(--tx2);">Kelola jendela pendaftaran per jenjang, tahun ajaran, dan gelombang.</p>
  </div>
  <a href="{{ route('admin.periods.create') }}" class="btn btn-primary" style="white-space:nowrap;">
    <i class="fa-solid fa-plus" style="font-size:10px;"></i> Tambah Periode
  </a>
</div>

@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
  <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="prd-card" id="periodsCard">
  <div class="prd-toolbar">
    <div class="prd-field grow">
      <label for="prdSearch"><i class="fa-solid fa-magnifying-glass" style="font-size:10px;"></i> Cari Periode</label>
      <input type="text" id="prdSearch" class="prd-input" placeholder="Cari nama periode, tahun ajaran, atau catatan..." value="{{ $filters['q'] ?? '' }}" autocomplete="off">
    </div>
    <div class="prd-field">
      <label for="prdLevel">Jenjang</label>
      <select id="prdLevel" class="prd-input">
        <option value="">Semua Jenjang</option>
        @foreach ($schoolLevels as $lv)
          <option value="{{ $lv->id }}" {{ ($filters['level'] ?? '') == $lv->id ? 'selected' : '' }}>{{ $lv->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="prd-field">
      <label for="prdStatus">Status</label>
      <select id="prdStatus" class="prd-input">
        <option value="">Semua Status</option>
        <option value="berlangsung" {{ ($filters['status'] ?? '') === 'berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
        <option value="belum_dibuka" {{ ($filters['status'] ?? '') === 'belum_dibuka' ? 'selected' : '' }}>Belum Dibuka</option>
        <option value="selesai" {{ ($filters['status'] ?? '') === 'selesai' ? 'selected' : '' }}>Selesai</option>
        <option value="nonaktif" {{ ($filters['status'] ?? '') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
      </select>
    </div>
    <div class="prd-field">
      <label for="prdYear">Tahun Ajaran</label>
      <select id="prdYear" class="prd-input">
        <option value="">Semua Tahun</option>
        @foreach ($academicYears as $ay)
          <option value="{{ $ay }}" {{ ($filters['academic_year'] ?? '') === $ay ? 'selected' : '' }}>{{ $ay }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="prd-summary">
    <span id="prdTotal"><i class="fa-solid fa-calendar-days" style="font-size:11px;"></i> Total <strong>{{ $periods->count() }}</strong> periode</span>
    @if (!empty($filters['q']) || !empty($filters['level']) || !empty($filters['status']) || !empty($filters['academic_year']))
      <a href="{{ route('admin.periods.index') }}" class="btn btn-outline" style="padding:3px 10px;font-size:11px;"><i class="fa-solid fa-xmark" style="font-size:9px;"></i> Reset filter</a>
    @endif
  </div>

  <div id="prdBody">
    @include('admin.partials.periods-table')
  </div>
</div>

{{-- Modal konfirmasi hapus 2 langkah --}}
<div id="periodDeleteModal" class="modal-overlay" style="display:none;">
  <div class="modal-card">
    <div class="modal-head">
      <div class="modal-icon modal-icon-red" id="prdModalIcon">!</div>
      <div style="flex:1;">
        <h3 class="modal-title" id="prdModalTitle">Hapus periode?</h3>
        <p class="modal-text">Yakin ingin menghapus periode <strong id="prdDeleteName"></strong>? Aksi ini tidak dapat dibatalkan.</p>
        <p class="modal-sub" id="prdModalSub">Periode yang sudah memiliki pendaftar tidak dapat dihapus — nonaktifkan saja.</p>
        <div id="prdBlockedBox" style="display:none;margin-top:10px;padding:10px 12px;border-radius:8px;background:var(--error-bg);border:1px solid var(--error-border);color:var(--error-fg);font-size:12px;">
          Periode ini sudah memiliki <strong id="prdBlockedCount"></strong> pendaftar dan tidak dapat dihapus.
        </div>
        <label id="prdConfirmWrap" style="display:flex;gap:8px;align-items:flex-start;margin-top:12px;font-size:12px;color:var(--tx2);cursor:pointer;">
          <input type="checkbox" id="prdConfirmCheck" style="margin-top:2px;accent-color:var(--danger);">
          <span>Saya paham data periode akan hilang permanen dan tidak dapat dipulihkan.</span>
        </label>
      </div>
    </div>
    <div class="modal-actions">
      <button type="button" onclick="closePeriodDelete()" class="modal-btn-cancel">Batal</button>
      <form id="prdDeleteForm" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" id="prdDeleteConfirm" disabled>Ya, Hapus</button>
      </form>
    </div>
  </div>
</div>

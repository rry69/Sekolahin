<style>
  /* ===================== PERIODE PENDAFTARAN — Bringova (no cards, scoped) ===================== */
  .prd {
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
  .prd .prd-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
  .prd .prd-crumb a { color: var(--coral); text-decoration: none; }
  .prd .prd-crumb a:hover { text-decoration: underline; }
  .prd .prd-crumb .sep { color: #d3d6de; }
  .prd .prd-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 2px; }
  .prd .prd-meta { font-size: 13px; color: var(--muted); margin-bottom: 18px; }
  .prd .prd-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px; font-weight: 500; }
  .prd .prd-alert i { margin-top: 2px; }
  .prd .prd-alert.success { background: var(--green-soft); color: var(--green); }
  .prd .prd-alert.error { background: var(--red-soft); color: var(--red); }
  .prd .prd-summary { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; padding: 12px 4px; border-bottom: 1px solid var(--divider); margin-bottom: 16px; font-size: 13px; color: var(--muted); }
  .prd .prd-summary strong { color: var(--ink); font-weight: 700; }
  .prd .prd-toolbar { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 16px; }
  .prd .prd-search { position: relative; flex: 1; min-width: 200px; }
  .prd .prd-search i { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 12px; pointer-events: none; }
  .prd .prd-search input { width: 100%; padding: 10px 14px 10px 36px; border: 1px solid rgba(26,26,46,0.14); border-radius: 11px; font-size: 13px; color: var(--ink); background: rgba(255,255,255,0.55); box-sizing: border-box; transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
  .prd .prd-search input::placeholder { color: var(--muted); }
  .prd .prd-search input:focus { outline: none; border-color: var(--coral); box-shadow: 0 0 0 4px rgba(255,107,107,0.14); background: #fff; }
  .prd .prd-field { display: flex; flex-direction: column; gap: 5px; min-width: 150px; }
  .prd .prd-field label { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; }
  .prd .r-pick { display: inline-flex; align-items: center; gap: 8px; flex-wrap: nowrap; padding: 9px 4px; border: none; border-bottom: 1px solid rgba(26,26,46,0.18); border-radius: 0; font-size: 13px; color: var(--ink); background: transparent; min-width: 150px; max-width: 210px; cursor: pointer; text-align: left; min-height: 38px; transition: border-color .18s ease, color .18s ease; }
  .prd .r-pick:hover { border-bottom-color: var(--coral); }
  .prd .r-pick:focus { outline: none; border-bottom-color: var(--coral); }
  .prd .r-pick .pick-label { flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .prd .r-pick .pick-label.is-placeholder { color: var(--muted); }
  .prd .r-pick .pick-caret { display: none; }
  .prd .r-pick .pick-clear { flex: 0 0 auto; display: none; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 6px; background: var(--gray-soft); color: var(--gray); cursor: pointer; font-size: 9px; user-select: none; }
  .prd .r-pick .pick-clear:hover { background: var(--red-soft); color: var(--red); }
  .prd .r-pick.has-value .pick-clear { display: inline-flex; }
  .prd .r-pick.has-value .pick-label.is-placeholder { display: none; }
  .prd .picker-backdrop { position: fixed; inset: 0; z-index: 80; background: rgba(26,26,46,0.32); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); display: none; align-items: flex-start; justify-content: center; padding: 80px 16px 16px; animation: prdPickerFade .18s ease-out; }
  .prd .picker-backdrop.is-open { display: flex; }
  @keyframes prdPickerFade { from { opacity: 0; } to { opacity: 1; } }
  .prd .picker-panel { width: 100%; max-width: 380px; max-height: min(520px, calc(100vh - 120px)); display: flex; flex-direction: column; background: #fff; border-radius: 18px; box-shadow: 0 20px 50px -16px rgba(26,26,46,0.35), 0 0 0 1px rgba(26,26,46,0.06); overflow: hidden; animation: prdPickerPop .22s cubic-bezier(.22,1.2,.36,1); }
  @keyframes prdPickerPop { from { opacity: 0; transform: translateY(-6px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
  .prd .picker-head { display: flex; align-items: center; gap: 10px; padding: 14px 16px; border-bottom: 1px solid var(--divider); }
  .prd .picker-head .picker-title { font-size: 14px; font-weight: 700; color: var(--ink); flex: 1; }
  .prd .picker-head .picker-close { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 8px; border: none; background: transparent; color: var(--muted); cursor: pointer; font-size: 12px; transition: background-color .15s ease, color .15s ease; }
  .prd .picker-head .picker-close:hover { background: var(--gray-soft); color: var(--ink); }
  .prd .picker-search { position: relative; padding: 10px 14px; border-bottom: 1px solid var(--divider); }
  .prd .picker-search i { position: absolute; left: 24px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 12px; pointer-events: none; }
  .prd .picker-search input { width: 100%; padding: 9px 12px 9px 32px; border: 1px solid rgba(26,26,46,0.14); border-radius: 10px; font-size: 13px; color: var(--ink); background: rgba(255,255,255,0.7); transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
  .prd .picker-search input:focus { outline: none; border-color: var(--coral); background: #fff; box-shadow: 0 0 0 3px rgba(255,107,107,0.12); }
  .prd .picker-list { flex: 1; overflow-y: auto; padding: 6px 8px; }
  .prd .picker-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; font-size: 13px; color: var(--ink); cursor: pointer; user-select: none; transition: background-color .15s ease, color .15s ease; }
  .prd .picker-item:hover, .prd .picker-item.is-active { background: var(--coral-soft); color: var(--coral); }
  .prd .picker-item.is-selected { background: var(--coral); color: #fff; font-weight: 600; }
  .prd .picker-item.is-selected:hover { background: var(--coral); }
  .prd .picker-item .pi-label { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .prd .picker-item .pi-check { font-size: 11px; opacity: 0; }
  .prd .picker-item.is-selected .pi-check { opacity: 1; }
  .prd .picker-empty { padding: 26px 12px; text-align: center; color: var(--muted); font-size: 12.5px; }
  .prd .picker-empty i { display: block; font-size: 20px; margin-bottom: 6px; color: #d3d6de; }
  .prd .picker-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 10px 14px; border-top: 1px solid var(--divider); background: rgba(255,255,255,0.5); }
  .prd .picker-foot .picker-clear-all { display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px; border-radius: 9px; border: none; background: transparent; color: var(--muted); font-size: 12px; font-weight: 600; cursor: pointer; transition: color .15s ease, background-color .15s ease; }
  .prd .picker-foot .picker-clear-all:hover { color: var(--red); background: var(--red-soft); }
  .prd .picker-foot .picker-done { display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 9px; border: none; background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; font-size: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 6px 14px -6px rgba(255,107,107,0.55); transition: filter .15s ease, transform .15s ease; }
  .prd .picker-foot .picker-done:hover { filter: brightness(1.04); transform: translateY(-1px); }
  .prd .prd-btn { display: inline-flex; align-items: center; gap: 7px; border: none; cursor: pointer; border-radius: 11px; padding: 10px 17px; font-size: 13px; font-weight: 700; text-decoration: none; transition: transform .15s ease, filter .15s ease, background-color .15s ease; }
  .prd .prd-btn:hover { transform: translateY(-1px); }
  .prd .prd-btn.coral { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 8px 18px -8px rgba(255,107,107,0.6); }
  .prd .prd-btn.coral:hover { filter: brightness(1.04); }
  .prd .prd-btn.ghost { background: rgba(255,255,255,0.6); color: var(--ink); box-shadow: 0 2px 10px -8px rgba(26,26,46,0.3); }
  .prd .prd-btn.ghost:hover { background: #fff; color: var(--coral); }
  .prd .prd-btn.sm { padding: 6px 11px; font-size: 11.5px; border-radius: 9px; }
  /* list rows */
  .prd .prd-list { display: flex; flex-direction: column; }
  .prd .prd-row { display: flex; align-items: center; gap: 15px; padding: 16px 4px; border-bottom: 1px solid var(--divider); }
  .prd .prd-row:last-child { border-bottom: none; }
  .prd .prd-ic { flex: 0 0 auto; width: 46px; height: 46px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 17px; background: var(--coral-soft); color: var(--coral); }
  .prd .prd-body { flex: 1; min-width: 0; }
  .prd .prd-name { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; font-size: 14px; font-weight: 700; color: var(--ink); }
  .prd .prd-sub { font-size: 12px; color: var(--muted); margin-top: 2px; display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
  .prd .prd-sub .dot { color: #d3d6de; }
  .prd .prd-tags { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 6px; align-items: center; }
  .prd .prd-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 700; white-space: nowrap; }
  .prd .prd-pill.green { background: var(--green-soft); color: var(--green); }
  .prd .prd-pill.blue { background: var(--blue-soft); color: var(--blue); }
  .prd .prd-pill.amber { background: var(--amber-soft); color: #b45309; }
  .prd .prd-pill.red { background: var(--red-soft); color: var(--red); }
  .prd .prd-pill.gray { background: var(--gray-soft); color: var(--gray); }
  .prd .badge-nonaktif { background: var(--gray-soft); color: var(--gray); border: none; }
  .prd .badge-belum { background: var(--blue-soft); color: var(--blue); border: none; }
  .prd .badge-berlangsung { background: var(--green-soft); color: var(--green); border: none; }
  .prd .badge-selesai { background: var(--amber-soft); color: #b45309; border: none; }
  .prd .kuota-ok { color: var(--green); font-weight: 700; }
  .prd .kuota-warn { color: #b45309; font-weight: 700; }
  .prd .kuota-full { color: var(--red); font-weight: 700; }
  .prd .prd-actions { display: flex; gap: 6px; align-items: center; flex-shrink: 0; flex-wrap: wrap; justify-content: flex-end; }
  .prd .prd-empty { text-align: center; color: var(--muted); font-size: 13px; padding: 30px 0; }
  .prd .prd-empty i { display: block; font-size: 24px; margin-bottom: 8px; color: #d3d6de; }
  .prd .prd-footer { display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 18px; padding: 14px 4px 0; border-top: 1px solid var(--divider); font-size: 12px; color: var(--muted); }
  /* modal hapus Bringova */
  .prd .prd-modal-backdrop { position: fixed; inset: 0; z-index: 90; background: rgba(26,26,46,0.36); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; padding: 16px; }
  .prd .prd-modal-backdrop.is-open { display: flex; }
  .prd .prd-modal { width: 100%; max-width: 440px; background: #fff; border-radius: 18px; padding: 22px; box-shadow: 0 24px 60px -18px rgba(26,26,46,0.4); animation: prdModalPop .2s cubic-bezier(.22,1.2,.36,1); }
  @keyframes prdModalPop { from { opacity: 0; transform: scale(0.97) translateY(4px); } to { opacity: 1; transform: scale(1) translateY(0); } }
  .prd .prd-modal-head { display: flex; gap: 14px; align-items: flex-start; }
  .prd .prd-modal-ic { flex: 0 0 auto; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 17px; background: var(--red-soft); color: var(--red); }
  .prd .prd-modal-ic.amber { background: var(--amber-soft); color: #b45309; }
  .prd .prd-modal-title { font-size: 15px; font-weight: 700; color: var(--ink); margin: 0 0 4px; }
  .prd .prd-modal-text { font-size: 13px; color: var(--muted); line-height: 1.5; margin: 0; }
  .prd .prd-modal-sub { font-size: 12px; color: var(--muted); margin-top: 8px; line-height: 1.4; }
  .prd .prd-modal-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px; flex-wrap: wrap; }
  .prd .prd-modal-actions .prd-btn-ghost { background: transparent; color: var(--muted); }
  .prd .prd-modal-actions .prd-btn-ghost:hover { color: var(--ink); }
  /* ---------- responsive: tablet (641-1024px) ---------- */
  @media (min-width: 641px) and (max-width: 1024px) {
    .prd { padding: 24px 20px 32px; }
    /* header: title + button tetap baris tapi button tidak gepeng */
    .prd > div[style*="display:flex"] { gap: 12px; }
    /* toolbar: search fullwidth + 3 picker 2 kolom */
    .prd .prd-toolbar { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; align-items: end; }
    .prd .prd-search { grid-column: 1 / -1; min-width: 0; flex: none; }
    .prd .prd-field { min-width: 0; }
    .prd .prd-field .r-pick { min-width: 0; max-width: none; width: 100%; }
    .prd .prd-row { flex-wrap: wrap; }
    .prd .prd-actions { justify-content: flex-end; width: auto; }
  }
  /* ---------- responsive: mobile (≤640px) ---------- */
  @media (max-width: 640px) {
    .prd { padding: 18px 14px 28px; overflow: hidden; }
    .prd .prd-crumb { margin-top: 8px; padding-left: 48px; box-sizing: border-box; }
    .prd .prd-title { font-size: 22px; box-sizing: border-box; }
    .prd > div[style*="display:flex"] { flex-direction: column; align-items: stretch !important; }
    .prd > div[style*="display:flex"] .prd-btn.coral { width: 100%; justify-content: center; min-height: 44px; white-space: nowrap; }
    /* toolbar: stack vertikal — search + 3 picker fullwidth */
    .prd .prd-toolbar { flex-direction: column; align-items: stretch; gap: 10px; }
    .prd .prd-search { min-width: 0; flex: none; width: 100%; }
    .prd .prd-field { min-width: 0; width: 100%; }
    .prd .prd-field .r-pick { width: 100%; max-width: none; min-width: 0; background: rgba(255,255,255,.6); border: 1px solid rgba(26,26,46,.08); border-bottom: 1px solid rgba(26,26,46,.12); border-radius: 11px; padding: 11px 12px; }
    .prd .prd-row { flex-wrap: wrap; }
    .prd .prd-actions { justify-content: flex-start; width: 100%; }
  }
</style>

<div class="prd">
  <div class="prd-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span>Periode Pendaftaran</span>
  </div>

  <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:2px;">
    <div>
      <h1 class="prd-title">Periode Pendaftaran</h1>
      <p class="prd-meta">Kelola jendela pendaftaran per jenjang, tahun ajaran, dan gelombang.</p>
    </div>
    <a href="{{ route('admin.periods.create') }}" class="prd-btn coral" style="white-space:nowrap;">
      <i class="fa-solid fa-plus" style="font-size:10px;"></i> Tambah Periode
    </a>
  </div>

  @if (session('success'))
    <div class="prd-alert success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
  @endif
  @if (session('error'))
    <div class="prd-alert error"><i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span></div>
  @endif

  <div class="prd-toolbar">
    <div class="prd-search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" id="prdSearch" placeholder="Cari nama periode, tahun ajaran, atau catatan..." value="{{ $filters['q'] ?? '' }}" autocomplete="off">
    </div>
    <div class="prd-field">
      <label>Jenjang</label>
      <button type="button" class="r-pick" data-picker="level" aria-haspopup="listbox" aria-expanded="false">
        <span class="pick-label is-placeholder">Semua Jenjang</span>
        <span class="pick-clear" data-clear="level" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
        <i class="fa-solid fa-chevron-down pick-caret"></i>
      </button>
      <input type="hidden" id="prdLevel" data-picker-input="level" value="{{ $filters['level'] ?? '' }}">
    </div>
    <div class="prd-field">
      <label>Status</label>
      <button type="button" class="r-pick" data-picker="status" aria-haspopup="listbox" aria-expanded="false">
        <span class="pick-label is-placeholder">Semua Status</span>
        <span class="pick-clear" data-clear="status" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
        <i class="fa-solid fa-chevron-down pick-caret"></i>
      </button>
      <input type="hidden" id="prdStatus" data-picker-input="status" value="{{ $filters['status'] ?? '' }}">
    </div>
    <div class="prd-field">
      <label>Tahun Ajaran</label>
      <button type="button" class="r-pick" data-picker="year" aria-haspopup="listbox" aria-expanded="false">
        <span class="pick-label is-placeholder">Semua Tahun</span>
        <span class="pick-clear" data-clear="year" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
        <i class="fa-solid fa-chevron-down pick-caret"></i>
      </button>
      <input type="hidden" id="prdYear" data-picker-input="year" value="{{ $filters['academic_year'] ?? '' }}">
    </div>
  </div>

  <div class="prd-summary">
    <span id="prdTotal"><i class="fa-solid fa-calendar-days" style="font-size:11px;"></i> Total <strong>{{ $periods->count() }}</strong> periode</span>
    @if (!empty($filters['q']) || !empty($filters['level']) || !empty($filters['status']) || !empty($filters['academic_year']))
      <a href="{{ route('admin.periods.index') }}" class="prd-btn ghost sm" style="padding:4px 12px;font-size:11.5px;"><i class="fa-solid fa-xmark" style="font-size:9px;"></i> Reset filter</a>
    @endif
  </div>

  <div id="prdBody">
    @include('admin.partials.periods-table')
  </div>

{{-- ===================== Modal Picker (Bringova) ===================== --}}
<div id="pickerBackdrop" class="picker-backdrop" aria-hidden="true">
  <div class="picker-panel" role="dialog" aria-modal="true" aria-labelledby="pickerTitle">
    <div class="picker-head">
      <div class="picker-title" id="pickerTitle">Pilih item</div>
      <button type="button" class="picker-close" onclick="closePicker()" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="picker-search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input id="pickerSearch" type="search" placeholder="Cari…" autocomplete="off">
    </div>
    <div class="picker-list" id="pickerList" role="listbox"></div>
    <div class="picker-foot">
      <button type="button" class="picker-clear-all" onclick="clearCurrentPicker()"><i class="fa-solid fa-eraser"></i> Bersihkan</button>
      <button type="button" class="picker-done" onclick="closePicker()">Selesai</button>
    </div>
  </div>
</div>

@php
  $pickLevels = [['v'=>'','l'=>'Semua Jenjang']];
  foreach ($schoolLevels as $lv) { $pickLevels[] = ['v'=>(string)$lv->id, 'l'=>$lv->name]; }
  $pickStatuses = [['v'=>'','l'=>'Semua Status'],['v'=>'berlangsung','l'=>'Sedang Berlangsung'],['v'=>'belum_dibuka','l'=>'Belum Dibuka'],['v'=>'selesai','l'=>'Selesai'],['v'=>'nonaktif','l'=>'Nonaktif']];
  $pickYears = [['v'=>'','l'=>'Semua Tahun']];
  foreach ($academicYears as $ay) { $pickYears[] = ['v'=>(string)$ay, 'l'=>$ay]; }
  $pickerJson = ['level'=>$pickLevels,'status'=>$pickStatuses,'year'=>$pickYears];
  $pickerLabels = ['level'=>'Pilih Jenjang','status'=>'Pilih Status','year'=>'Pilih Tahun Ajaran'];
@endphp
<div id="reg-data" hidden data-picker='@json($pickerJson)' data-picker-labels='@json($pickerLabels)'></div>

{{-- ============ MODAL HAPUS 2 LANGKAH (Bringova) ============ --}}
<div id="periodDeleteModal" class="prd-modal-backdrop" aria-hidden="true">
  <div class="prd-modal" role="dialog" aria-modal="true">
    <div class="prd-modal-head">
      <div class="prd-modal-ic" id="prdModalIcon"><i class="fa-solid fa-trash-can"></i></div>
      <div style="flex:1;min-width:0">
        <h3 class="prd-modal-title" id="prdModalTitle">Hapus periode?</h3>
        <p class="prd-modal-text">Yakin ingin menghapus periode <strong id="prdDeleteName"></strong>? Aksi ini tidak dapat dibatalkan.</p>
        <p class="prd-modal-sub" id="prdModalSub">Periode yang sudah memiliki pendaftar tidak dapat dihapus — nonaktifkan saja.</p>
        <div id="prdBlockedBox" style="display:none;margin-top:10px;padding:10px 12px;border-radius:10px;background:var(--red-soft);border:1px solid rgba(239,68,68,0.18);color:var(--red);font-size:12px;">
          Periode ini sudah memiliki <strong id="prdBlockedCount"></strong> pendaftar dan tidak dapat dihapus.
        </div>
        <label id="prdConfirmWrap" style="display:flex;gap:8px;align-items:flex-start;margin-top:12px;font-size:12px;color:var(--muted);cursor:pointer;">
          <input type="checkbox" id="prdConfirmCheck" style="margin-top:2px;accent-color:var(--red);width:15px;height:15px;">
          <span>Saya paham data periode akan hilang permanen dan tidak dapat dipulihkan.</span>
        </label>
      </div>
    </div>
    <div class="prd-modal-actions">
      <button type="button" onclick="closePeriodDelete()" class="prd-btn ghost sm prd-btn-ghost">Batal</button>
      <form id="prdDeleteForm" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="prd-btn coral sm" id="prdDeleteConfirm" disabled style="opacity:.5;pointer-events:none;background:var(--red);">Ya, Hapus</button>
      </form>
    </div>
  </div>
</div>
</div>

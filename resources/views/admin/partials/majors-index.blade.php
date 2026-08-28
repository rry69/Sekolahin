<style>
  /* ===================== KELOLA JURUSAN — Bringova (no cards, scoped) ===================== */
  .mjr {
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
    max-width: 100%;
    overflow: hidden;
    box-sizing: border-box;
  }

  /* ---------- header ---------- */
  .mjr .mjr-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
  .mjr .mjr-crumb a { color: var(--coral); text-decoration: none; }
  .mjr .mjr-crumb a:hover { text-decoration: underline; }
  .mjr .mjr-crumb .sep { color: #d3d6de; }
  .mjr .mjr-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 2px; }
  .mjr .mjr-meta { font-size: 13px; color: var(--muted); margin-bottom: 18px; }

  /* ---------- alerts ---------- */
  .mjr .mjr-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px; font-weight: 500; }
  .mjr .mjr-alert i { margin-top: 2px; }
  .mjr .mjr-alert.success { background: var(--green-soft); color: var(--green); }
  .mjr .mjr-alert.error { background: var(--red-soft); color: var(--red); }

  /* ---------- summary ---------- */
  .mjr .mjr-summary { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; padding: 12px 4px; border-bottom: 1px solid var(--divider); margin-bottom: 16px; font-size: 13px; color: var(--muted); }
  .mjr .mjr-summary strong { color: var(--ink); font-weight: 700; }

  /* ---------- toolbar ---------- */
  .mjr .mjr-toolbar { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 16px; }
  .mjr .mjr-search { position: relative; flex: 1; min-width: 200px; }
  .mjr .mjr-search i { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 12px; pointer-events: none; }
  .mjr .mjr-search input { width: 100%; padding: 10px 14px 10px 36px; border: 1px solid rgba(26,26,46,0.14); border-radius: 11px; font-size: 13px; color: var(--ink); background: rgba(255,255,255,0.55); box-sizing: border-box; transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
  .mjr .mjr-search input::placeholder { color: var(--muted); }
  .mjr .mjr-search input:focus { outline: none; border-color: var(--coral); box-shadow: 0 0 0 4px rgba(255,107,107,0.14); background: #fff; }
  .mjr .mjr-field { display: flex; flex-direction: column; gap: 5px; min-width: 160px; }
  .mjr .mjr-field label { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; }

  /* ---------- picker trigger (border-bottom) ---------- */
  .mjr .r-pick { display: inline-flex; align-items: center; gap: 8px; flex-wrap: nowrap; padding: 9px 4px; border: none; border-bottom: 1px solid rgba(26,26,46,0.18); border-radius: 0; font-size: 13px; color: var(--ink); background: transparent; min-width: 160px; max-width: 220px; cursor: pointer; text-align: left; min-height: 38px; transition: border-color .18s ease, color .18s ease; }
  .mjr .r-pick:hover { border-bottom-color: var(--coral); }
  .mjr .r-pick:focus { outline: none; border-bottom-color: var(--coral); }
  .mjr .r-pick .pick-label { flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .mjr .r-pick .pick-label.is-placeholder { color: var(--muted); }
  .mjr .r-pick .pick-caret { display: none; }
  .mjr .r-pick .pick-clear { flex: 0 0 auto; display: none; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 6px; background: var(--gray-soft); color: var(--gray); cursor: pointer; font-size: 9px; user-select: none; }
  .mjr .r-pick .pick-clear:hover { background: var(--red-soft); color: var(--red); }
  .mjr .r-pick.has-value .pick-clear { display: inline-flex; }
  .mjr .r-pick.has-value .pick-label.is-placeholder { display: none; }

  /* ---------- picker modal ---------- */
  .mjr .picker-backdrop { position: fixed; inset: 0; z-index: 80; background: rgba(26,26,46,0.32); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); display: none; align-items: flex-start; justify-content: center; padding: 80px 16px 16px; animation: mjrPickerFade .18s ease-out; }
  .mjr .picker-backdrop.is-open { display: flex; }
  @keyframes mjrPickerFade { from { opacity: 0; } to { opacity: 1; } }
  .mjr .picker-panel { width: 100%; max-width: 380px; max-height: min(520px, calc(100vh - 120px)); display: flex; flex-direction: column; background: #fff; border-radius: 18px; box-shadow: 0 20px 50px -16px rgba(26,26,46,0.35), 0 0 0 1px rgba(26,26,46,0.06); overflow: hidden; animation: mjrPickerPop .22s cubic-bezier(.22,1.2,.36,1); }
  @keyframes mjrPickerPop { from { opacity: 0; transform: translateY(-6px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
  .mjr .picker-head { display: flex; align-items: center; gap: 10px; padding: 14px 16px; border-bottom: 1px solid var(--divider); }
  .mjr .picker-head .picker-title { font-size: 14px; font-weight: 700; color: var(--ink); flex: 1; }
  .mjr .picker-head .picker-close { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 8px; border: none; background: transparent; color: var(--muted); cursor: pointer; font-size: 12px; transition: background-color .15s ease, color .15s ease; }
  .mjr .picker-head .picker-close:hover { background: var(--gray-soft); color: var(--ink); }
  .mjr .picker-search { position: relative; padding: 10px 14px; border-bottom: 1px solid var(--divider); }
  .mjr .picker-search i { position: absolute; left: 24px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 12px; pointer-events: none; }
  .mjr .picker-search input { width: 100%; padding: 9px 12px 9px 32px; border: 1px solid rgba(26,26,46,0.14); border-radius: 10px; font-size: 13px; color: var(--ink); background: rgba(255,255,255,0.7); transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
  .mjr .picker-search input:focus { outline: none; border-color: var(--coral); background: #fff; box-shadow: 0 0 0 3px rgba(255,107,107,0.12); }
  .mjr .picker-list { flex: 1; overflow-y: auto; padding: 6px 8px; }
  .mjr .picker-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; font-size: 13px; color: var(--ink); cursor: pointer; user-select: none; transition: background-color .15s ease, color .15s ease; }
  .mjr .picker-item:hover, .mjr .picker-item.is-active { background: var(--coral-soft); color: var(--coral); }
  .mjr .picker-item.is-selected { background: var(--coral); color: #fff; font-weight: 600; }
  .mjr .picker-item.is-selected:hover { background: var(--coral); }
  .mjr .picker-item .pi-label { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .mjr .picker-item .pi-check { font-size: 11px; opacity: 0; }
  .mjr .picker-item.is-selected .pi-check { opacity: 1; }
  .mjr .picker-empty { padding: 26px 12px; text-align: center; color: var(--muted); font-size: 12.5px; }
  .mjr .picker-empty i { display: block; font-size: 20px; margin-bottom: 6px; color: #d3d6de; }
  .mjr .picker-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 10px 14px; border-top: 1px solid var(--divider); background: rgba(255,255,255,0.5); }
  .mjr .picker-foot .picker-clear-all { display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px; border-radius: 9px; border: none; background: transparent; color: var(--muted); font-size: 12px; font-weight: 600; cursor: pointer; transition: color .15s ease, background-color .15s ease; }
  .mjr .picker-foot .picker-clear-all:hover { color: var(--red); background: var(--red-soft); }
  .mjr .picker-foot .picker-done { display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 9px; border: none; background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; font-size: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 6px 14px -6px rgba(255,107,107,0.55); transition: filter .15s ease, transform .15s ease; }
  .mjr .picker-foot .picker-done:hover { filter: brightness(1.04); transform: translateY(-1px); }

  /* ---------- pills ---------- */
  .mjr .mjr-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 700; white-space: nowrap; }
  .mjr .mjr-pill.green { background: var(--green-soft); color: var(--green); }
  .mjr .mjr-pill.red { background: var(--red-soft); color: var(--red); }
  .mjr .mjr-pill.blue { background: var(--blue-soft); color: var(--blue); }
  .mjr .mjr-pill.amber { background: var(--amber-soft); color: #b45309; }
  .mjr .mjr-pill.purple { background: var(--purple-soft); color: var(--purple); }
  .mjr .mjr-pill.coral { background: var(--coral-soft); color: var(--coral); }
  .mjr .mjr-pill.gray { background: var(--gray-soft); color: var(--gray); }

  /* ---------- buttons ---------- */
  .mjr .mjr-btn { display: inline-flex; align-items: center; gap: 6px; border: none; cursor: pointer; border-radius: 11px; padding: 10px 17px; font-size: 13px; font-weight: 700; text-decoration: none; transition: transform .15s ease, filter .15s ease, background-color .15s ease; }
  .mjr .mjr-btn:hover { transform: translateY(-1px); }
  .mjr .mjr-btn.coral { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 8px 18px -8px rgba(255,107,107,0.6); }
  .mjr .mjr-btn.coral:hover { filter: brightness(1.04); }
  .mjr .mjr-btn.ghost { background: rgba(255,255,255,0.6); color: var(--ink); box-shadow: 0 2px 10px -8px rgba(26,26,46,0.3); }
  .mjr .mjr-btn.ghost:hover { background: #fff; color: var(--coral); }
  .mjr .mjr-btn.green { background: var(--green-soft); color: var(--green); }
  .mjr .mjr-btn.green:hover { background: #bbf7d5; }
  .mjr .mjr-btn.red { background: var(--red-soft); color: var(--red); }
  .mjr .mjr-btn.red:hover { background: #fecaca; }
  .mjr .mjr-btn.amber { background: var(--amber-soft); color: #b45309; }
  .mjr .mjr-btn.amber:hover { background: #fde68a; }
  .mjr .mjr-btn.outline { background: #fff; color: var(--ink); border: 1px solid var(--divider); }
  .mjr .mjr-btn.outline:hover { border-color: var(--coral); color: var(--coral); }
  .mjr .mjr-btn.sm { padding: 6px 11px; font-size: 11.5px; border-radius: 9px; }

  /* ---------- list rows ---------- */
  .mjr .mjr-list { display: flex; flex-direction: column; }
  .mjr .mjr-row { display: flex; align-items: center; gap: 15px; padding: 16px 4px; border-bottom: 1px solid var(--divider); }
  .mjr .mjr-row:last-child { border-bottom: none; }
  .mjr .mjr-ic { flex: 0 0 auto; width: 46px; height: 46px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 17px; background: var(--gray-soft); color: var(--gray); }
  .mjr .mjr-ic.active { background: var(--green-soft); color: var(--green); }
  .mjr .mjr-ic.inactive { background: var(--red-soft); color: var(--red); }
  .mjr .mjr-body { flex: 1; min-width: 0; }
  .mjr .mjr-name { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; font-size: 14px; font-weight: 700; color: var(--ink); }
  .mjr .mjr-name a { color: var(--coral); text-decoration: none; font-weight: 700; }
  .mjr .mjr-name a:hover { text-decoration: underline; }
  .mjr .mjr-sub { font-size: 12px; color: var(--muted); margin-top: 2px; }
  .mjr .mjr-tags { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 6px; }
  .mjr .mjr-cap { font-size: 11px; color: var(--muted); font-weight: 500; }
  .mjr .mjr-stats { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 6px; font-size: 11.5px; color: var(--muted); align-items: center; }
  .mjr .mjr-stats b { color: var(--ink); font-weight: 700; }
  .mjr .mjr-quotas-min { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 6px; font-size: 11.5px; color: var(--muted); align-items: center; }
  .mjr .mjr-actions { display: flex; gap: 6px; align-items: center; flex-shrink: 0; flex-wrap: wrap; justify-content: flex-end; }

  /* ---------- empty & footer ---------- */
  .mjr .mjr-empty { text-align: center; color: var(--muted); font-size: 13px; padding: 30px 0; }
  .mjr .mjr-empty i { display: block; font-size: 24px; margin-bottom: 8px; color: #d3d6de; }
  .mjr .mjr-footer { display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 18px; padding: 14px 4px 0; border-top: 1px solid var(--divider); font-size: 12px; color: var(--muted); }
  .mjr .mjr-pager { margin-top: 22px; display: flex; justify-content: center; }
  .mjr .mjr-pager > nav { display: flex; justify-content: center; }

  /* ---------- delete modal ---------- */
  .mjr .mjr-modal-backdrop { position: fixed; inset: 0; z-index: 90; background: rgba(26,26,46,0.36); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; padding: 16px; }
  .mjr .mjr-modal-backdrop.is-open { display: flex; }
  .mjr .mjr-modal { width: 100%; max-width: 400px; background: #fff; border-radius: 18px; padding: 22px; box-shadow: 0 24px 60px -18px rgba(26,26,46,0.4); animation: mjrModalPop .2s cubic-bezier(.22,1.2,.36,1); }
  @keyframes mjrModalPop { from { opacity: 0; transform: scale(0.97) translateY(4px); } to { opacity: 1; transform: scale(1) translateY(0); } }
  .mjr .mjr-modal-body { display: flex; align-items: flex-start; gap: 13px; margin-bottom: 18px; }
  .mjr .mjr-modal-ic { flex: 0 0 auto; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 17px; background: var(--red-soft); color: var(--red); }
  .mjr .mjr-modal-title { font-size: 15px; font-weight: 700; color: var(--ink); }
  .mjr .mjr-modal-msg { font-size: 13px; color: var(--muted); margin-top: 3px; line-height: 1.5; }
  .mjr .mjr-modal-actions { display: flex; justify-content: flex-end; gap: 8px; }
  .mjr .mjr-modal-actions .mjr-btn-ghost { background: transparent; color: var(--muted); }
  .mjr .mjr-modal-actions .mjr-btn-ghost:hover { color: var(--ink); }

  /* ---------- responsive: tablet (641-1024px) ---------- */
  @media (min-width: 641px) and (max-width: 1024px) {
    .mjr { padding: 24px 20px 32px; }
    .mjr .mjr-toolbar { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; align-items: end; }
    .mjr .mjr-search { grid-column: 1 / -1; min-width: 0; flex: none; }
    .mjr .mjr-field { min-width: 0; }
    .mjr .mjr-field .r-pick { min-width: 0; max-width: none; width: 100%; }
    .mjr .mjr-toolbar .mjr-btn.coral { grid-column: 1 / -1; width: 100%; justify-content: center; min-height: 42px; }
    .mjr .mjr-row { flex-wrap: wrap; gap: 12px 14px; align-items: flex-start; }
    .mjr .mjr-body { flex: 1 1 280px; min-width: 200px; max-width: 100%; }
    .mjr .mjr-stats { gap: 6px 12px; }
    .mjr .mjr-quotas-min { gap: 4px 12px; }
    .mjr .mjr-actions { flex: 1 1 100%; justify-content: flex-end; gap: 8px; }
  }
  /* ---------- responsive: mobile (≤640px) ---------- */
  @media (max-width: 640px) {
    .mjr { padding: 18px 14px 28px; overflow: hidden; }
    .mjr .mjr-crumb { margin-top: 8px; padding-left: 48px; box-sizing: border-box; }
    .mjr .mjr-title { font-size: 22px; padding-left: 48px; box-sizing: border-box; }
    .mjr .mjr-meta { font-size: 12.5px; padding-left: 48px; box-sizing: border-box; }
    .mjr .mjr-summary { padding: 10px 0; }
    /* toolbar: stack vertikal full-width */
    .mjr .mjr-toolbar { flex-direction: column; align-items: stretch; gap: 10px; }
    .mjr .mjr-search { min-width: 0; flex: none; width: 100%; }
    .mjr .mjr-field { min-width: 0; width: 100%; }
    .mjr .mjr-field .r-pick { width: 100%; max-width: none; min-width: 0; background: rgba(255,255,255,.6); border: 1px solid rgba(26,26,46,.08); border-bottom: 1px solid rgba(26,26,46,.12); border-radius: 11px; padding: 11px 12px; }
    .mjr .mjr-toolbar .mjr-btn.coral { width: 100%; justify-content: center; min-height: 44px; white-space: nowrap; }
    /* card grid */
    .mjr .mjr-row {
      display: grid;
      grid-template-columns: 46px 1fr;
      grid-template-areas: "ic body" "actions actions";
      column-gap: 14px;
      row-gap: 0;
      align-items: start;
      padding: 14px 0 16px;
      max-width: 100%;
      overflow: hidden;
      box-sizing: border-box;
    }
    .mjr .mjr-ic { grid-area: ic; align-self: start; }
    .mjr .mjr-body { grid-area: body; min-width: 0; overflow: hidden; }
    .mjr .mjr-name { font-size: 14.5px; line-height: 1.3; gap: 6px; word-break: break-word; }
    .mjr .mjr-name .mjr-cap { font-size: 11px; word-break: break-word; }
    .mjr .mjr-sub { font-size: 12px; word-break: break-word; overflow-wrap: anywhere; margin-top: 4px; }
    /* statistik grid 2 kolom */
    .mjr .mjr-stats {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 6px 10px;
      margin-top: 10px;
      background: rgba(255,255,255,.50);
      border: 1px solid rgba(26,26,46,.06);
      border-radius: 10px;
      padding: 10px 12px;
      font-size: 12px;
    }
    .mjr .mjr-stats .mjr-dot { display: none; }
    .mjr .mjr-quotas-min {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 4px 10px;
      margin-top: 8px;
      font-size: 11.5px;
      line-height: 1.4;
    }
    .mjr .mjr-quotas-min .mjr-dot { display: none; }
    .mjr .mjr-quotas-min span { word-break: break-word; }
    /* actions 2x2 */
    .mjr .mjr-actions {
      grid-area: actions;
      width: 100%;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
      margin-top: 12px;
      justify-content: stretch;
    }
    .mjr .mjr-actions .mjr-btn { width: 100%; justify-content: center; min-height: 40px; font-size: 12.5px; padding: 9px 10px; box-sizing: border-box; }
    .mjr .mjr-actions form { display: block; width: 100%; }
    .mjr .mjr-actions form .mjr-btn { width: 100%; }
    .mjr .mjr-footer { flex-direction: column; align-items: stretch; gap: 12px; }
    .mjr .mjr-footer .pager { width: 100%; display: flex; justify-content: center; }
  }
  @media (max-width: 360px) {
    .mjr .mjr-stats { grid-template-columns: 1fr; }
    .mjr .mjr-quotas-min { grid-template-columns: 1fr; }
  }
</style>

<div class="mjr" id="majorsCard">
  <div class="mjr-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span>Kelola Jurusan</span>
  </div>

  <h1 class="mjr-title">Daftar Jurusan</h1>
  <p class="mjr-meta">Kelola jurusan per sekolah dan per jenjang, beserta kuota per jalur.</p>

  @if (session('success'))
    <div class="mjr-alert success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
  @endif
  @if (session('error'))
    <div class="mjr-alert error"><i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span></div>
  @endif

  <div class="mjr-summary">
    <span id="mjrTotal"><i class="fa-solid fa-layer-group" style="font-size:11px;"></i> Total <strong>{{ $majors->total() }}</strong> jurusan</span>
    @if (request()->has('q') || request()->has('school_id') || request()->has('level'))
      <a href="{{ route('admin.majors.index') }}" class="mjr-btn ghost sm" style="padding:4px 12px;font-size:11.5px;"><i class="fa-solid fa-xmark" style="font-size:9px;"></i> Reset filter</a>
    @endif
  </div>

  <div class="mjr-toolbar">
    <div class="mjr-search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" id="mjrSearch" placeholder="Cari nama jurusan atau kode..." value="{{ request('q') }}" autocomplete="off">
    </div>
    <div class="mjr-field">
      <label>Jenjang</label>
      <button type="button" class="r-pick" data-picker="level" aria-haspopup="listbox" aria-expanded="false">
        <span class="pick-label is-placeholder">Semua Jenjang</span>
        <span class="pick-clear" data-clear="level" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
        <i class="fa-solid fa-chevron-down pick-caret"></i>
      </button>
      <input type="hidden" id="mjrLevel" name="level" data-picker-input="level" value="{{ request('level') }}">
    </div>
    <div class="mjr-field">
      <label>Sekolah</label>
      <button type="button" class="r-pick" data-picker="school" aria-haspopup="listbox" aria-expanded="false">
        <span class="pick-label is-placeholder">Semua Sekolah</span>
        <span class="pick-clear" data-clear="school" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
        <i class="fa-solid fa-chevron-down pick-caret"></i>
      </button>
      <input type="hidden" id="mjrSchool" name="school_id" data-picker-input="school" value="{{ request('school_id') }}">
    </div>
    <a href="{{ route('admin.majors.create') }}" class="mjr-btn coral" style="white-space:nowrap;">
      <i class="fa-solid fa-plus" style="font-size:10px;"></i> Tambah Jurusan
    </a>
  </div>

  <div id="mjrBody">
    @include('admin.partials.majors-table')
  </div>

{{-- ===================== Modal Picker (Bringova) — reuse global picker ===================== --}}
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
  $pickLevels = [['v' => '', 'l' => 'Semua Jenjang']];
  foreach ($levels as $l) { $pickLevels[] = ['v' => (string)$l->id, 'l' => $l->name]; }
  $pickSchools = [['v' => '', 'l' => 'Semua Sekolah']];
  foreach ($schools as $s) { $pickSchools[] = ['v' => (string)$s->id, 'l' => $s->name]; }
  $pickerJson = ['level' => $pickLevels, 'school' => $pickSchools];
  $pickerLabels = ['level' => 'Pilih Jenjang', 'school' => 'Pilih Sekolah'];
@endphp

<div id="reg-data" hidden data-picker='@json($pickerJson)' data-picker-labels='@json($pickerLabels)'></div>

{{-- ================== DELETE JURUSAN CONFIRM MODAL (Bringova) ================== --}}
<div id="majorDeleteModal" class="mjr-modal-backdrop" aria-hidden="true">
  <div class="mjr-modal" role="dialog" aria-modal="true">
    <div class="mjr-modal-body">
      <div class="mjr-modal-ic"><i class="fa-solid fa-trash-can"></i></div>
      <div style="flex:1;min-width:0">
        <h3 class="mjr-modal-title">Hapus jurusan?</h3>
        <p class="mjr-modal-msg">Yakin ingin menghapus jurusan <strong id="majorDeleteName"></strong>? Aksi ini tidak dapat dibatalkan.</p>
        <p class="mjr-modal-msg" style="margin-top:6px;font-size:11.5px;">Jurusan yang masih memiliki pendaftar tidak dapat dihapus — nonaktifkan saja.</p>
      </div>
    </div>
    <div class="mjr-modal-actions">
      <button type="button" onclick="closeMajorDelete()" class="mjr-btn ghost sm mjr-btn-ghost">Batal</button>
      <form id="majorDeleteForm" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="mjr-btn red sm" id="majorDeleteConfirm">Ya, Hapus</button>
      </form>
    </div>
  </div>
</div>
</div>

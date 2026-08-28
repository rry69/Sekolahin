<style>
  /* ===================== KELOLA AKUN SISWA — Bringova (no cards, scoped) ===================== */
  .acc {
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
  .acc .acc-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
  .acc .acc-crumb a { color: var(--coral); text-decoration: none; }
  .acc .acc-crumb a:hover { text-decoration: underline; }
  .acc .acc-crumb .sep { color: #d3d6de; }
  .acc .acc-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 2px; }
  .acc .acc-meta { font-size: 13px; color: var(--muted); margin-bottom: 14px; }
  .acc .acc-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px; font-weight: 500; }
  .acc .acc-alert i { margin-top: 2px; }
  .acc .acc-alert.success { background: var(--green-soft); color: var(--green); }
  .acc .acc-alert.error { background: var(--red-soft); color: var(--red); }
  /* tabs underline — scrollable on mobile */
  .acc .acc-tabs { display: flex; align-items: center; gap: 4px; flex-wrap: nowrap; overflow-x: auto; overflow-y: hidden; -webkit-overflow-scrolling: touch; scrollbar-width: none; border-bottom: 1px solid var(--divider); margin-bottom: 16px; padding-bottom: 2px; }
  .acc .acc-tabs::-webkit-scrollbar { display: none; }
  .acc .acc-tabs a.doc-tab, .acc .acc-tabs a.acc-tab { all: unset; flex: 0 0 auto; display: inline-flex; align-items: center; gap: 6px; padding: 10px 14px 11px; font-size: 13px; font-weight: 600; color: var(--muted); cursor: pointer; border-bottom: 2.5px solid transparent; margin-bottom: -1px; transition: color .18s, border-color .18s; white-space: nowrap; }
  .acc .acc-tabs a.doc-tab:hover, .acc .acc-tabs a.acc-tab:hover { color: var(--ink); }
  .acc .acc-tabs a.doc-tab.active, .acc .acc-tabs a.acc-tab.active { color: var(--coral); border-bottom-color: var(--coral); }
  .acc .acc-tabs .acc-tools { margin-left: auto; display: flex; align-items: center; gap: 8px; padding-bottom: 6px; }
  /* toolbar search + filter */
  .acc .acc-toolbar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 12px; }
  .acc .acc-search { position: relative; flex: 1; min-width: 200px; }
  .acc .acc-search i { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 12px; pointer-events: none; }
  .acc .acc-search input { width: 100%; padding: 10px 14px 10px 36px; border: 1px solid rgba(26,26,46,0.14); border-radius: 11px; font-size: 13px; color: var(--ink); background: rgba(255,255,255,0.55); box-sizing: border-box; transition: border-color .18s, box-shadow .18s, background .18s; }
  .acc .acc-search input::placeholder { color: var(--muted); }
  .acc .acc-search input:focus { outline: none; border-color: var(--coral); box-shadow: 0 0 0 4px rgba(255,107,107,0.14); background: #fff; }
  .acc .acc-fbtn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 9px 14px; border-radius: 10px; border: 1px solid var(--divider); font-size: 12.5px; font-weight: 600; color: var(--muted); background: rgba(255,255,255,0.6); cursor: pointer; transition: all .15s; white-space: nowrap; }
  .acc .acc-fbtn:hover { background: #fff; color: var(--coral); border-color: var(--coral); }
  .acc .acc-gobtn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 18px; border-radius: 11px; border: none; font-size: 13px; font-weight: 700; color: #fff; background: linear-gradient(135deg, var(--coral), var(--coral-2)); cursor: pointer; box-shadow: 0 8px 18px -8px rgba(255,107,107,0.6); transition: filter .15s, transform .15s; white-space: nowrap; }
  .acc .acc-gobtn:hover { filter: brightness(1.04); transform: translateY(-1px); }
  .acc .acc-filters { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; padding: 14px 16px; border: 1px dashed rgba(26,26,46,0.14); border-radius: 14px; background: rgba(255,255,255,0.35); margin-bottom: 12px; }
  .acc .acc-field { display: flex; flex-direction: column; gap: 5px; min-width: 160px; }
  .acc .acc-field label { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; }
  /* picker trigger */
  .acc .r-pick { display: inline-flex; align-items: center; gap: 8px; flex-wrap: nowrap; padding: 9px 4px; border: none; border-bottom: 1px solid rgba(26,26,46,0.18); border-radius: 0; font-size: 13px; color: var(--ink); background: transparent; min-width: 160px; max-width: 220px; cursor: pointer; text-align: left; min-height: 38px; transition: border-color .18s, color .18s; }
  .acc .r-pick:hover { border-bottom-color: var(--coral); }
  .acc .r-pick:focus { outline: none; border-bottom-color: var(--coral); }
  .acc .r-pick .pick-label { flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .acc .r-pick .pick-label.is-placeholder { color: var(--muted); }
  .acc .r-pick .pick-caret { display: none; }
  .acc .r-pick .pick-clear { flex: 0 0 auto; display: none; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 6px; background: var(--gray-soft); color: var(--gray); cursor: pointer; font-size: 9px; user-select: none; }
  .acc .r-pick .pick-clear:hover { background: var(--red-soft); color: var(--red); }
  .acc .r-pick.has-value .pick-clear { display: inline-flex; }
  /* picker modal */
  .acc .picker-backdrop { position: fixed; inset: 0; z-index: 80; background: rgba(26,26,46,0.32); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); display: none; align-items: flex-start; justify-content: center; padding: 80px 16px 16px; animation: accPickerFade .18s ease-out; }
  .acc .picker-backdrop.is-open { display: flex; }
  @keyframes accPickerFade { from { opacity: 0; } to { opacity: 1; } }
  .acc .picker-panel { width: 100%; max-width: 380px; max-height: min(520px, calc(100vh - 120px)); display: flex; flex-direction: column; background: #fff; border-radius: 18px; box-shadow: 0 20px 50px -16px rgba(26,26,46,0.35), 0 0 0 1px rgba(26,26,46,0.06); overflow: hidden; animation: accPickerPop .22s cubic-bezier(.22,1.2,.36,1); }
  @keyframes accPickerPop { from { opacity: 0; transform: translateY(-6px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
  .acc .picker-head { display: flex; align-items: center; gap: 10px; padding: 14px 16px; border-bottom: 1px solid var(--divider); }
  .acc .picker-head .picker-title { font-size: 14px; font-weight: 700; color: var(--ink); flex: 1; }
  .acc .picker-head .picker-close { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 8px; border: none; background: transparent; color: var(--muted); cursor: pointer; font-size: 12px; transition: background-color .15s, color .15s; }
  .acc .picker-head .picker-close:hover { background: var(--gray-soft); color: var(--ink); }
  .acc .picker-search { position: relative; padding: 10px 14px; border-bottom: 1px solid var(--divider); }
  .acc .picker-search i { position: absolute; left: 24px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 12px; pointer-events: none; }
  .acc .picker-search input { width: 100%; padding: 9px 12px 9px 32px; border: 1px solid rgba(26,26,46,0.14); border-radius: 10px; font-size: 13px; color: var(--ink); background: rgba(255,255,255,0.7); transition: border-color .18s, box-shadow .18s, background .18s; }
  .acc .picker-search input:focus { outline: none; border-color: var(--coral); background: #fff; box-shadow: 0 0 0 3px rgba(255,107,107,0.12); }
  .acc .picker-list { flex: 1; overflow-y: auto; padding: 6px 8px; }
  .acc .picker-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; font-size: 13px; color: var(--ink); cursor: pointer; user-select: none; transition: background-color .15s, color .15s; }
  .acc .picker-item:hover, .acc .picker-item.is-active { background: var(--coral-soft); color: var(--coral); }
  .acc .picker-item.is-selected { background: var(--coral); color: #fff; font-weight: 600; }
  .acc .picker-item.is-selected:hover { background: var(--coral); }
  .acc .picker-item .pi-label { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .acc .picker-item .pi-check { font-size: 11px; opacity: 0; }
  .acc .picker-item.is-selected .pi-check { opacity: 1; }
  .acc .picker-empty { padding: 26px 12px; text-align: center; color: var(--muted); font-size: 12.5px; }
  .acc .picker-empty i { display: block; font-size: 20px; margin-bottom: 6px; color: #d3d6de; }
  .acc .picker-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 10px 14px; border-top: 1px solid var(--divider); background: rgba(255,255,255,0.5); }
  .acc .picker-foot .picker-clear-all { display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px; border-radius: 9px; border: none; background: transparent; color: var(--muted); font-size: 12px; font-weight: 600; cursor: pointer; transition: color .15s, background .15s; }
  .acc .picker-foot .picker-clear-all:hover { color: var(--red); background: var(--red-soft); }
  .acc .picker-foot .picker-done { display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 9px; border: none; background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; font-size: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 6px 14px -6px rgba(255,107,107,0.55); transition: filter .15s, transform .15s; }
  .acc .picker-foot .picker-done:hover { filter: brightness(1.04); transform: translateY(-1px); }
  /* list rows — desktop flex */
  .acc .acc-list { display: flex; flex-direction: column; }
  .acc .acc-row { display: flex; align-items: center; gap: 15px; padding: 15px 4px; border-bottom: 1px solid var(--divider); }
  .acc .acc-row:last-child { border-bottom: none; }
  .acc .acc-ic { flex: 0 0 auto; width: 46px; height: 46px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 17px; background: var(--gray-soft); color: var(--gray); }
  .acc .acc-body { flex: 1; min-width: 0; }
  .acc .acc-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
  .acc .acc-name { font-size: 14px; font-weight: 700; color: var(--ink); }
  .acc .acc-count-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 20px; font-size: 11.5px; font-weight: 600; color: var(--ink); background: var(--gray-soft); white-space: nowrap; }
  .acc .acc-count-badge b { font-size: 12.5px; font-weight: 800; color: var(--ink); }
  /* legacy count (hidden on mobile, visible desktop fallback) */
  .acc .acc-count { flex: 0 0 auto; display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: var(--muted); }
  .acc .acc-count b { color: var(--ink); font-size: 15px; }
  .acc .acc-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 16px; margin-top: 6px; }
  .acc .acc-meta-item { font-size: 12px; color: var(--muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .acc .acc-meta-item b { color: var(--ink); font-weight: 600; }
  .acc .acc-meta-item i { font-size: 10px; margin-right: 3px; color: var(--muted); }
  .acc .acc-actions { display: flex; gap: 6px; align-items: center; flex-shrink: 0; flex-wrap: wrap; justify-content: flex-end; }
  /* buttons */
  .acc .acc-btn { display: inline-flex; align-items: center; gap: 6px; border: none; cursor: pointer; border-radius: 11px; padding: 8px 14px; font-size: 12.5px; font-weight: 700; text-decoration: none; transition: transform .15s, filter .15s, background-color .15s; }
  .acc .acc-btn:hover { transform: translateY(-1px); }
  .acc .acc-btn.coral { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 8px 18px -8px rgba(255,107,107,0.6); }
  .acc .acc-btn.coral:hover { filter: brightness(1.04); }
  .acc .acc-btn.ghost { background: rgba(255,255,255,0.6); color: var(--ink); box-shadow: 0 2px 10px -8px rgba(26,26,46,0.3); }
  .acc .acc-btn.ghost:hover { background: #fff; color: var(--coral); }
  .acc .acc-btn.red { background: var(--red-soft); color: var(--red); }
  .acc .acc-btn.red:hover { background: #fecaca; }
  .acc .acc-btn.sm { padding: 6px 11px; font-size: 11.5px; border-radius: 9px; }
  .acc .acc-empty { text-align: center; color: var(--muted); font-size: 13px; padding: 30px 0; }
  .acc .acc-empty i { display: block; font-size: 24px; margin-bottom: 8px; color: #d3d6de; }
  .acc .acc-pager { margin-top: 22px; display: flex; justify-content: center; }
  .acc .acc-pager > nav { display: flex; justify-content: center; }
  /* delete modal */
  .acc .acc-modal-backdrop { position: fixed; inset: 0; z-index: 90; background: rgba(26,26,46,0.36); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; padding: 16px; }
  .acc .acc-modal-backdrop.is-open { display: flex; }
  .acc .acc-modal { width: 100%; max-width: 400px; background: #fff; border-radius: 18px; padding: 22px; box-shadow: 0 24px 60px -18px rgba(26,26,46,0.4); animation: accModalPop .2s cubic-bezier(.22,1.2,.36,1); }
  @keyframes accModalPop { from { opacity: 0; transform: scale(0.97) translateY(4px); } to { opacity: 1; transform: scale(1) translateY(0); } }
  .acc .acc-modal-body { display: flex; align-items: flex-start; gap: 13px; margin-bottom: 18px; }
  .acc .acc-modal-ic { flex: 0 0 auto; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 17px; background: var(--red-soft); color: var(--red); }
  .acc .acc-modal-title { font-size: 15px; font-weight: 700; color: var(--ink); }
  .acc .acc-modal-msg { font-size: 13px; color: var(--muted); margin-top: 3px; line-height: 1.5; }
  .acc .acc-modal-actions { display: flex; justify-content: flex-end; gap: 8px; }
  .acc .acc-modal-actions .acc-btn-ghost { background: transparent; color: var(--muted); }
  .acc .acc-modal-actions .acc-btn-ghost:hover { color: var(--ink); }
  /* ---------- desktop restore (≥1025px): original inline layout ---------- */
  @media (min-width: 1025px) {
    .acc .acc-head { display: block; }
    .acc .acc-count-badge { display: none !important; }
    .acc .acc-count { display: flex !important; }
    .acc .acc-meta-grid { display: flex; flex-wrap: wrap; gap: 0 6px; margin-top: 2px; align-items: center; }
    .acc .acc-meta-grid .acc-meta-item { white-space: nowrap; overflow: visible; text-overflow: clip; }
    .acc .acc-meta-grid .acc-meta-item:nth-child(4) { flex-basis: 100%; margin-top: 3px; }
    .acc .acc-meta-grid .acc-meta-item:nth-child(1)::after,
    .acc .acc-meta-grid .acc-meta-item:nth-child(2)::after { content: "  \00B7"; color: #d3d6de; margin-left: 4px; white-space: pre; }
  }
  /* ---------- responsive: tablet (641-1024px) ---------- */
  @media (min-width: 641px) and (max-width: 1024px) {
    .acc { padding: 24px 20px 32px; }
    .acc .acc-toolbar { gap: 10px; }
    .acc .acc-search { min-width: 160px; }
    .acc .acc-filters { gap: 10px; }
    .acc .acc-row { flex-wrap: wrap; gap: 12px 14px; align-items: flex-start; }
    .acc .acc-body { flex: 1 1 280px; min-width: 220px; }
    .acc .acc-meta-grid { grid-template-columns: 1fr 1fr; gap: 4px 14px; }
    .acc .acc-count { display: none !important; }
    .acc .acc-count-badge { display: inline-flex !important; }
    .acc .acc-actions { flex: 1 1 100%; justify-content: flex-end; gap: 8px; }
  }
  /* ---------- responsive: mobile (≤640px) ---------- */
  @media (max-width: 640px) {
    .acc { padding: 18px 14px 28px; overflow: hidden; }
    .acc .acc-crumb { margin-top: 8px; padding-left: 48px; box-sizing: border-box; }
    .acc .acc-title { font-size: 22px; box-sizing: border-box; }
    /* tabs: keep scroll single-line with bottom indicator */
    .acc .acc-tabs { gap: 0; padding-bottom: 0; margin-bottom: 14px; }
    .acc .acc-tabs a.doc-tab, .acc .acc-tabs a.acc-tab { padding: 9px 12px 10px; font-size: 12.5px; }
    /* toolbar: search full width, buttons 2-col grid below */
    .acc .acc-toolbar { display: grid; grid-template-columns: 1fr 1fr; grid-template-areas: "search search" "fbtn gobtn"; gap: 8px; }
    .acc .acc-search { grid-area: search; min-width: 0; flex: none; width: 100%; }
    .acc .acc-fbtn { grid-area: fbtn; width: 100%; min-height: 42px; }
    .acc .acc-gobtn { grid-area: gobtn; width: 100%; min-height: 42px; justify-content: center; }
    /* alias for toolbar variation: handle both button orders */
    .acc .acc-toolbar .acc-fbtn { width: 100%; }
    .acc .acc-toolbar .acc-gobtn { width: 100%; }
    .acc .acc-filters { flex-direction: column; align-items: stretch; padding: 12px; gap: 10px; }
    .acc .acc-field { min-width: 0; width: 100%; }
    .acc .acc-field .r-pick { width: 100%; max-width: none; min-width: 0; background: rgba(255,255,255,.6); border: 1px solid rgba(26,26,46,.08); border-bottom: 1px solid rgba(26,26,46,.12); border-radius: 11px; padding: 11px 12px; }
    /* card grid: icon | body, actions full width below */
    .acc .acc-row { display: grid; grid-template-columns: 46px 1fr; grid-template-areas: "ic body" "actions actions"; column-gap: 12px; row-gap: 0; align-items: start; padding: 14px 0 16px; }
    .acc .acc-ic { grid-area: ic; align-self: start; }
    .acc .acc-body { grid-area: body; min-width: 0; overflow: hidden; }
    .acc .acc-head { align-items: flex-start; gap: 8px; }
    .acc .acc-name { font-size: 14.5px; line-height: 1.3; }
    .acc .acc-count { display: none; }
    .acc .acc-count-badge { font-size: 11px; padding: 3px 8px; }
    .acc .acc-meta-grid { grid-template-columns: 1fr; gap: 3px; margin-top: 8px; }
    .acc .acc-meta-item { white-space: normal; word-break: break-word; overflow-wrap: anywhere; line-height: 1.4; }
    .acc .acc-actions { grid-area: actions; width: 100%; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 12px; margin-left: 0; justify-content: stretch; }
    .acc .acc-actions .acc-btn { width: 100%; justify-content: center; min-height: 40px; font-size: 12.5px; box-sizing: border-box; }
    .acc .acc-actions form { display: none; }
  }
  @media (max-width: 360px) {
    .acc .acc-meta-grid { grid-template-columns: 1fr; }
  }
</style>

<div class="acc">
  <div class="acc-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span>Akun Siswa</span>
  </div>
  <h1 class="acc-title">Daftar Akun Siswa</h1>
  <p class="acc-meta">Kelola akun siswa dan pendaftaran terkait</p>

  @if (session('success'))
    <div class="acc-alert success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
  @endif
  @if (session('error'))
    <div class="acc-alert error"><i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span></div>
  @endif

  <div class="acc-tabs">
    <a href="{{ route('admin.accounts.index') }}" class="acc-tab doc-tab {{ !request('registration_status') && !request('major_id') ? 'active' : '' }}">Semua</a>
    <a href="{{ route('admin.accounts.index', ['registration_status' => 'pending']) }}" class="acc-tab doc-tab {{ request('registration_status') == 'pending' ? 'active' : '' }}">Pending</a>
    <a href="{{ route('admin.accounts.index', ['registration_status' => 'verified']) }}" class="acc-tab doc-tab {{ request('registration_status') == 'verified' ? 'active' : '' }}">Terverifikasi</a>
    <a href="{{ route('admin.accounts.index', ['registration_status' => 'accepted']) }}" class="acc-tab doc-tab {{ request('registration_status') == 'accepted' ? 'active' : '' }}">Diterima</a>
    <a href="{{ route('admin.accounts.index', ['registration_status' => 'rejected']) }}" class="acc-tab doc-tab {{ request('registration_status') == 'rejected' ? 'active' : '' }}">Ditolak</a>
  </div>

  <form id="filterForm" method="GET" action="{{ route('admin.accounts.index') }}">
    <div class="acc-toolbar">
      <div class="acc-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email / NIK / NISN..." autocomplete="off">
      </div>
      <button type="button" class="acc-fbtn" onclick="toggleFilterPanel()"><i class="fa-solid fa-filter" style="font-size:10px"></i> Filter</button>
      <button type="submit" class="acc-gobtn">Cari</button>
    </div>
    <div id="filterPanel" class="acc-filters" style="display:{{ request('registration_status') || request('major_id') ? 'flex' : 'none' }};">
      <div class="acc-field">
        <label>Status Pendaftaran</label>
        <button type="button" class="r-pick" data-picker="reg_status" aria-haspopup="listbox" aria-expanded="false">
          <span class="pick-label {{ request('registration_status') ? '' : 'is-placeholder' }}">{{ request('registration_status') ? ucfirst(request('registration_status')) : 'Semua Status' }}</span>
          <span class="pick-clear" data-clear="reg_status" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
        </button>
        <input type="hidden" name="registration_status" data-picker-input="reg_status" value="{{ request('registration_status') }}">
      </div>
      <div class="acc-field">
        <label>Jurusan</label>
        <button type="button" class="r-pick" data-picker="major" aria-haspopup="listbox" aria-expanded="false">
          <span class="pick-label {{ request('major_id') ? '' : 'is-placeholder' }}">{{ request('major_id') ? ($majors->firstWhere('id', (int)request('major_id'))->name ?? 'Jurusan') : 'Semua Jurusan' }}</span>
          <span class="pick-clear" data-clear="major" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
        </button>
        <input type="hidden" name="major_id" data-picker-input="major" value="{{ request('major_id') }}">
      </div>
      <button type="submit" class="acc-gobtn" style="padding:8px 16px;">Terapkan</button>
      <a href="{{ route('admin.accounts.index') }}" class="acc-btn ghost sm">Reset</a>
    </div>
  </form>

  @if ($accounts->isEmpty())
    <div class="acc-empty"><i class="fa-regular fa-folder-open"></i>Tidak ada akun siswa</div>
  @else
    <div class="acc-list">
      @foreach ($accounts as $account)
        @php
          $fullName = $account->applicant->full_name ?? $account->name;
          $hasAccepted = $account->applicant?->registrations?->contains(fn($r) => $r->isAccepted()) ?? false;
        @endphp
        <div class="acc-row">
          <span class="acc-ic"><i class="fa-solid fa-user"></i></span>
          <div class="acc-body">
            <div class="acc-head">
              <div class="acc-name">{{ $fullName }}</div>
              <span class="acc-count-badge"><b>{{ $account->applicant->registrations_count ?? 0 }}</b> pendaftaran</span>
            </div>
            <div class="acc-meta-grid">
              <span class="acc-meta-item"><i class="fa-regular fa-envelope"></i>{{ $account->email }}</span>
              <span class="acc-meta-item"><b>NIK</b> {{ $account->applicant->nik ?? '-' }}</span>
              <span class="acc-meta-item"><b>NISN</b> {{ $account->applicant->nisn ?? '-' }}</span>
              <span class="acc-meta-item"><i class="fa-regular fa-calendar"></i>Terdaftar {{ $account->created_at->format('d M Y') }}</span>
            </div>
          </div>
          <div class="acc-count"><span><b>{{ $account->applicant->registrations_count ?? 0 }}</b> pendaftaran</span></div>
          <div class="acc-actions">
            <a href="{{ route('admin.accounts.show', $account) }}" class="acc-btn ghost sm"><i class="fa-regular fa-eye" style="font-size:10px;"></i> Detail</a>
            @if (! $hasAccepted)
              <button type="button" class="acc-btn red sm" onclick="openAccDelete({{ $account->id }}, '{{ addslashes($fullName) }}')"><i class="fa-solid fa-trash-can" style="font-size:10px;"></i> Hapus Akun</button>
              <form id="accDeleteForm-{{ $account->id }}" method="POST" action="{{ route('admin.accounts.destroy', $account) }}" style="display:none;">@csrf @method('DELETE')</form>
            @endif
          </div>
        </div>
      @endforeach
    </div>
    <div class="acc-pager">{{ $accounts->appends(request()->query())->links('vendor.pagination.bringova') }}</div>
  @endif

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
  $pickStatus = [['v' => '', 'l' => 'Semua Status'], ['v' => 'pending', 'l' => 'Pending'], ['v' => 'verified', 'l' => 'Terverifikasi'], ['v' => 'accepted', 'l' => 'Diterima'], ['v' => 'rejected', 'l' => 'Ditolak']];
  $pickMajors = [['v' => '', 'l' => 'Semua Jurusan']];
  foreach ($majors as $m) { $pickMajors[] = ['v' => (string)$m->id, 'l' => $m->name]; }
  $pickerJson = ['reg_status' => $pickStatus, 'major' => $pickMajors];
  $pickerLabels = ['reg_status' => 'Pilih Status Pendaftaran', 'major' => 'Pilih Jurusan'];
@endphp
<div id="reg-data" hidden data-picker='@json($pickerJson)' data-picker-labels='@json($pickerLabels)'></div>

{{-- ================== DELETE ACCOUNT CONFIRM MODAL (Bringova) ================== --}}
<div id="accDeleteModal" class="acc-modal-backdrop" aria-hidden="true">
  <div class="acc-modal" role="dialog" aria-modal="true">
    <div class="acc-modal-body">
      <div class="acc-modal-ic"><i class="fa-solid fa-trash-can"></i></div>
      <div style="flex:1;min-width:0">
        <h3 class="acc-modal-title">Hapus akun siswa?</h3>
        <p id="accDeleteMessage" class="acc-modal-msg"></p>
      </div>
    </div>
    <div class="acc-modal-actions">
      <button type="button" onclick="closeAccDelete()" class="acc-btn ghost sm acc-btn-ghost">Batal</button>
      <button type="button" id="accDeleteAction" class="acc-btn red sm">Ya, Hapus</button>
    </div>
  </div>
</div>
</div>

<script>
(function () {
  var pendingForm = null;
  window.openAccDelete = function (id, name) {
    pendingForm = document.getElementById('accDeleteForm-' + id);
    var msg = document.getElementById('accDeleteMessage');
    if (msg) msg.textContent = 'Hapus akun siswa \"' + name + '\"? Seluruh data pendaftaran dan pembayarannya akan ikut terhapus permanen.';
    var m = document.getElementById('accDeleteModal');
    if (m) { m.classList.add('is-open'); m.setAttribute('aria-hidden','false'); }
  };
  window.closeAccDelete = function () {
    var m = document.getElementById('accDeleteModal');
    if (m) { m.classList.remove('is-open'); m.setAttribute('aria-hidden','true'); }
    pendingForm = null;
  };
  document.getElementById('accDeleteAction').addEventListener('click', function () {
    if (pendingForm) pendingForm.submit();
  });
  var bd = document.getElementById('accDeleteModal');
  if (bd) bd.addEventListener('click', function (e) { if (e.target === this) closeAccDelete(); });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      var m = document.getElementById('accDeleteModal');
      if (m && m.classList.contains('is-open')) closeAccDelete();
    }
    if (e.key === 'Enter') {
      var m = document.getElementById('accDeleteModal');
      if (m && m.classList.contains('is-open')) { e.preventDefault(); if (pendingForm) pendingForm.submit(); }
    }
  });
})();
</script>

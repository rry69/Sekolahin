<style>
  /* ===================== REGISTRATIONS INDEX — Bringova (no cards, scoped) ===================== */
  .reg {
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

  /* ---------- header ---------- */
  .reg .r-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
  .reg .r-crumb a { color: var(--coral); text-decoration: none; }
  .reg .r-crumb a:hover { text-decoration: underline; }
  .reg .r-crumb .sep { color: #d3d6de; }
  .reg .r-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 2px; }
  .reg .r-meta { font-size: 13px; color: var(--muted); margin-bottom: 22px; }
  .reg .r-meta b { color: var(--ink); font-weight: 600; }

  /* ---------- alerts (flash) ---------- */
  .reg .r-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px; font-weight: 500; }
  .reg .r-alert i { margin-top: 2px; }
  .reg .r-alert.success { background: var(--green-soft); color: var(--green); }
  .reg .r-alert.error   { background: var(--red-soft);   color: var(--red); }
  .reg .r-alert.info    { background: var(--blue-soft);  color: var(--blue); }

  /* ---------- toolbar: search + filter ---------- */
  .reg .r-toolbar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 16px; }
  .reg .r-search { position: relative; flex: 1; min-width: 200px; }
  .reg .r-search i {
    position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
    color: var(--muted); font-size: 13px; pointer-events: none;
  }
  .reg .r-search input {
    width: 100%; padding: 11px 14px 11px 38px; border: 1px solid rgba(26,26,46,0.14);
    border-radius: 12px; font-size: 13.5px; color: var(--ink); background: rgba(255,255,255,0.55);
    transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
  }
  .reg .r-search input::placeholder { color: var(--muted); }
  .reg .r-search input:focus { outline: none; border-color: var(--coral); box-shadow: 0 0 0 4px rgba(255,107,107,0.14); background: #fff; }
  .reg .r-fbtn, .reg .r-gobtn {
    display: inline-flex; align-items: center; gap: 7px; border: none; cursor: pointer;
    border-radius: 12px; padding: 11px 18px; font-size: 13px; font-weight: 700;
    transition: transform .15s ease, filter .15s ease;
  }
  .reg .r-fbtn { background: rgba(255,255,255,0.7); color: var(--ink); box-shadow: 0 4px 14px -10px rgba(26,26,46,0.3); }
  .reg .r-fbtn:hover { background: #fff; color: var(--coral); }
  .reg .r-gobtn { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 8px 18px -8px rgba(255,107,107,0.6); }
  .reg .r-gobtn:hover { filter: brightness(1.03); transform: translateY(-1px); }

  /* ---------- filter panel ---------- */
  .reg .r-filters {
    display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;
    padding: 18px; margin-bottom: 20px; border: 1px dashed rgba(26,26,46,0.14);
    border-radius: 14px; background: rgba(255,255,255,0.30);
  }
  .reg .r-field { display: flex; flex-direction: column; gap: 5px; }
  .reg .r-field label { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .3px; }
  .reg .r-field input[type="text"]:focus, .reg .r-field input[type="search"]:focus { outline: none; border-color: var(--coral); box-shadow: 0 0 0 3px rgba(255,107,107,0.12); }

  /* ---------- picker trigger (pengganti <select>) ---------- */
  .reg .r-pick {
    display: inline-flex; align-items: center; gap: 8px; flex-wrap: nowrap;
    padding: 9px 4px; border: none; border-bottom: 1px solid rgba(26,26,46,0.18); border-radius: 0;
    font-size: 13px; color: var(--ink); background: transparent; min-width: 150px;
    cursor: pointer; text-align: left; min-height: 38px; max-width: 100%;
    transition: border-color .18s ease, color .18s ease;
  }
  .reg .r-pick:hover { border-bottom-color: var(--coral); }
  .reg .r-pick:focus { outline: none; border-bottom-color: var(--coral); }
  .reg .r-pick .pick-label { flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .reg .r-pick .pick-label.is-placeholder { color: var(--muted); }
  .reg .r-pick .pick-caret { display: none; }
  .reg .r-pick .pick-clear {
    flex: 0 0 auto;
    display: none; align-items: center; justify-content: center;
    width: 18px; height: 18px; border-radius: 6px; background: var(--gray-soft);
    color: var(--gray); cursor: pointer; font-size: 9px; user-select: none;
  }
  .reg .r-pick .pick-clear:hover { background: var(--red-soft); color: var(--red); }
  .reg .r-pick.has-value .pick-clear { display: inline-flex; }
  .reg .r-pick.has-value .pick-label.is-placeholder { display: none; }

  /* ---------- modal picker (Bringova) ---------- */
  .reg .picker-backdrop {
    position: fixed; inset: 0; z-index: 80;
    background: rgba(26,26,46,0.32);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    display: none; align-items: flex-start; justify-content: center;
    padding: 80px 16px 16px;
    animation: rPickerFade .18s ease-out;
  }
  .reg .picker-backdrop.is-open { display: flex; }
  @keyframes rPickerFade { from { opacity: 0; } to { opacity: 1; } }

  .reg .picker-panel {
    width: 100%; max-width: 380px; max-height: min(520px, calc(100vh - 120px));
    display: flex; flex-direction: column;
    background: #fff; border-radius: 18px;
    box-shadow: 0 20px 50px -16px rgba(26,26,46,0.35), 0 0 0 1px rgba(26,26,46,0.06);
    overflow: hidden;
    animation: rPickerPop .22s cubic-bezier(.22,1.2,.36,1);
  }
  @keyframes rPickerPop { from { opacity: 0; transform: translateY(-6px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }

  .reg .picker-head {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 16px; border-bottom: 1px solid var(--divider);
  }
  .reg .picker-head .picker-title { font-size: 14px; font-weight: 700; color: var(--ink); flex: 1; }
  .reg .picker-head .picker-close {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border-radius: 8px; border: none; background: transparent;
    color: var(--muted); cursor: pointer; font-size: 12px;
    transition: background-color .15s ease, color .15s ease;
  }
  .reg .picker-head .picker-close:hover { background: var(--gray-soft); color: var(--ink); }

  .reg .picker-search {
    position: relative; padding: 10px 14px; border-bottom: 1px solid var(--divider);
  }
  .reg .picker-search i {
    position: absolute; left: 24px; top: 50%; transform: translateY(-50%);
    color: var(--muted); font-size: 12px; pointer-events: none;
  }
  .reg .picker-search input {
    width: 100%; padding: 9px 12px 9px 32px;
    border: 1px solid rgba(26,26,46,0.14); border-radius: 10px;
    font-size: 13px; color: var(--ink); background: rgba(255,255,255,0.7);
    transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
  }
  .reg .picker-search input:focus { outline: none; border-color: var(--coral); background: #fff; box-shadow: 0 0 0 3px rgba(255,107,107,0.12); }

  .reg .picker-list { flex: 1; overflow-y: auto; padding: 6px 8px; }
  .reg .picker-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px; border-radius: 10px;
    font-size: 13px; color: var(--ink); cursor: pointer; user-select: none;
    transition: background-color .15s ease, color .15s ease;
  }
  .reg .picker-item:hover, .reg .picker-item.is-active { background: var(--coral-soft); color: var(--coral); }
  .reg .picker-item.is-selected { background: var(--coral); color: #fff; font-weight: 600; }
  .reg .picker-item.is-selected:hover { background: var(--coral); }
  .reg .picker-item .pi-label { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .reg .picker-item .pi-check { font-size: 11px; opacity: 0; }
  .reg .picker-item.is-selected .pi-check { opacity: 1; }
  .reg .picker-empty { padding: 26px 12px; text-align: center; color: var(--muted); font-size: 12.5px; }
  .reg .picker-empty i { display: block; font-size: 20px; margin-bottom: 6px; color: #d3d6de; }

  .reg .picker-foot {
    display: flex; align-items: center; justify-content: space-between; gap: 8px;
    padding: 10px 14px; border-top: 1px solid var(--divider); background: rgba(255,255,255,0.5);
  }
  .reg .picker-foot .picker-clear-all {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 12px; border-radius: 9px; border: none; background: transparent;
    color: var(--muted); font-size: 12px; font-weight: 600; cursor: pointer;
    transition: color .15s ease, background-color .15s ease;
  }
  .reg .picker-foot .picker-clear-all:hover { color: var(--red); background: var(--red-soft); }
  .reg .picker-foot .picker-done {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 16px; border-radius: 9px; border: none;
    background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff;
    font-size: 12px; font-weight: 700; cursor: pointer;
    box-shadow: 0 6px 14px -6px rgba(255,107,107,0.55);
    transition: filter .15s ease, transform .15s ease;
  }
  .reg .picker-foot .picker-done:hover { filter: brightness(1.04); transform: translateY(-1px); }

  /* ---------- tabs (underline, no box) ---------- */
  .reg .r-tabs { display: flex; gap: 18px; border-bottom: 1px solid var(--divider); margin-bottom: 22px; flex-wrap: wrap; }
  .reg .r-tabs a.doc-tab,
  .reg .r-tabs a.r-tab {
    all: unset;
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 2px 11px; font-size: 13px; font-weight: 600; color: var(--muted);
    text-decoration: none; border-bottom: 2.5px solid transparent; margin-bottom: -1px;
    cursor: pointer; white-space: nowrap;
    transition: color .18s ease;
  }
  .reg .r-tabs a.r-tab:hover, .reg .r-tabs a.doc-tab:hover { color: var(--ink); }
  .reg .r-tabs a.r-tab.active, .reg .r-tabs a.doc-tab.active { color: var(--coral); border-bottom-color: var(--coral); }
  .reg .r-tabs a .badge { background: var(--coral-soft); color: var(--coral); border-radius: 20px; padding: 1px 8px; font-size: 10.5px; font-weight: 700; }
  .reg .r-tabs a.active .badge { background: var(--coral); color: #fff; }

  /* ---------- list rows (no card, divider) ---------- */
  .reg .r-list { display: flex; flex-direction: column; }
  .reg .r-row { display: flex; align-items: center; gap: 15px; padding: 16px 4px; border-bottom: 1px solid var(--divider); }
  .reg .r-row:last-child { border-bottom: none; }
  .reg .r-ic {
    flex: 0 0 auto; width: 46px; height: 46px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center; font-size: 17px;
  }
  .reg .r-ic.coral  { background: var(--coral-soft);  color: var(--coral); }
  .reg .r-ic.amber  { background: var(--amber-soft);  color: var(--amber); }
  .reg .r-ic.green  { background: var(--green-soft);  color: var(--green); }
  .reg .r-ic.blue   { background: var(--blue-soft);   color: var(--blue); }
  .reg .r-ic.purple { background: var(--purple-soft); color: var(--purple); }
  .reg .r-ic.red    { background: var(--red-soft);    color: var(--red); }
  .reg .r-body { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px; }
  .reg .r-name { font-size: 14px; font-weight: 700; color: var(--ink); line-height: 1.25; }
  .reg .r-num { font-size: 11px; color: var(--muted); font-weight: 600; letter-spacing: .02em; }
  .reg .r-sub { font-size: 12px; color: var(--muted); margin-top: 4px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
  .reg .r-sub-item { display: inline-flex; align-items: center; gap: 5px; }
  .reg .r-sub-item i { font-size: 11px; opacity: .7; }
  .reg .r-sub-dot { display: none; }
  .reg .r-badges { display: flex; gap: 7px; flex-wrap: wrap; justify-content: flex-end; }
  .reg .r-pill { font-size: 11px; font-weight: 700; padding: 5px 11px; border-radius: 20px; white-space: nowrap; }
  .reg .r-pill.p-pending  { background: var(--amber-soft); color: var(--amber); }
  .reg .r-pill.p-verified { background: var(--blue-soft);   color: var(--blue); }
  .reg .r-pill.p-accepted { background: var(--green-soft);  color: var(--green); }
  .reg .r-pill.p-rejected { background: var(--red-soft);    color: var(--red); }
  .reg .r-pill.p-canceled { background: var(--gray-soft);   color: var(--gray); }
  .reg .r-actions { display: flex; gap: 7px; align-items: center; }
  .reg .r-act {
    display: inline-flex; align-items: center; gap: 5px; padding: 8px 13px; border-radius: 10px;
    font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; border: none;
    transition: filter .15s ease, background-color .15s ease;
  }
  .reg .r-act.detail { background: var(--coral-soft); color: var(--coral); }
  .reg .r-act.detail:hover { filter: brightness(0.97); }
  .reg .r-act.reset { background: var(--gray-soft); color: var(--gray); }
  .reg .r-act.reset:hover { background: var(--red-soft); color: var(--red); }

  .reg .r-empty { text-align: center; padding: 44px 16px; color: var(--muted); font-size: 13.5px; }
  .reg .r-empty i { font-size: 26px; display: block; margin-bottom: 10px; color: #d3d6de; }

  /* ---------- pagination (Bringova view) ---------- */
  .reg .r-pager { margin-top: 22px; display: flex; justify-content: center; }
  .reg .r-pager > nav { display: flex; justify-content: center; }

  /* ---------- responsive: tablet (641-1024) ---------- */
  @media (min-width: 641px) and (max-width: 1024px) {
    .reg .r-row { flex-wrap: wrap; gap: 12px 14px; padding: 16px 4px; }
    .reg .r-body { flex: 1 1 280px; min-width: 220px; }
    .reg .r-badges { flex: 0 1 auto; justify-content: flex-start; }
    .reg .r-actions { flex: 0 0 auto; margin-left: auto; }
    .reg .r-sub { display: flex; flex-wrap: wrap; gap: 4px 8px; align-items: center; }
    .reg .r-sub-item { display: inline-flex; align-items: center; gap: 4px; }
    .reg .r-sub-dot { display: inline; }
  }
  /* ---------- responsive: mobile (≤640px) — hierarchy Nama → No REG → Info → Status → Action ---------- */
  @media (max-width: 640px) {
    .reg { padding: 18px 14px 28px; }
    .reg .r-title { font-size: 22px; }
    .reg .r-meta { font-size: 12.5px; }
    .reg .r-toolbar { gap: 8px; }
    .reg .r-search { min-width: 0; flex: 1 1 100%; }
    .reg .r-fbtn, .reg .r-gobtn { flex: 1 1 auto; justify-content: center; min-height: 42px; }

    .reg .r-row {
      display: grid;
      grid-template-columns: 46px 1fr;
      grid-template-areas:
        "ic body"
        "badges badges"
        "actions actions";
      gap: 0;
      align-items: start;
      padding: 18px 0 16px;
    }
    .reg .r-ic { grid-area: ic; align-self: start; margin-top: 2px; }
    .reg .r-body { grid-area: body; padding-left: 2px; }
    .reg .r-name {
      font-size: 15px;
      line-height: 1.25;
      flex-direction: column;
      align-items: flex-start;
      gap: 2px;
    }
    .reg .r-num {
      display: block;
      font-size: 11.5px;
      font-weight: 600;
      letter-spacing: .02em;
      color: var(--muted);
      margin-top: 1px;
    }
    .reg .r-sub {
      display: flex;
      flex-direction: column;
      gap: 5px;
      margin-top: 10px;
      font-size: 12.5px;
      line-height: 1.45;
    }
    .reg .r-sub-item {
      display: flex;
      align-items: center;
      gap: 6px;
      color: var(--muted);
      word-break: break-all;
    }
    .reg .r-sub-item.major { color: var(--ink); font-weight: 500; word-break: break-word; }
    .reg .r-sub-item.date { font-size: 12px; }
    .reg .r-sub-item i { font-size: 11px; opacity: .7; flex: 0 0 auto; }
    .reg .r-sub-dot { display: none; }

    .reg .r-badges {
      grid-area: badges;
      justify-content: flex-start;
      gap: 7px;
      margin-top: 14px;
    }
    .reg .r-pill { font-size: 11.5px; padding: 6px 12px; }

    .reg .r-actions {
      grid-area: actions;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin-top: 14px;
      width: 100%;
    }
    .reg .r-act {
      justify-content: center;
      min-height: 44px;
      padding: 11px 14px;
      font-size: 13px;
      border-radius: 11px;
      font-weight: 700;
    }
    .reg .r-tabs { gap: 14px; overflow-x: auto; flex-wrap: nowrap; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
    .reg .r-tabs::-webkit-scrollbar { display: none; }
    .reg .r-filters { padding: 14px; }
    .reg .r-field { flex: 1 1 100%; }
    .reg .r-field .r-pick { min-width: 0; width: 100%; }
  }
</style>

<div class="reg">
  <div class="r-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span>Pendaftaran</span>
  </div>
  <h1 class="r-title">Daftar Pendaftaran</h1>
  <p class="r-meta">Kelola & pantau seluruh pendaftaran siswa.</p>

  @if (session('success'))
  <div class="r-alert success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
  @endif
  @if (session('error'))
  <div class="r-alert error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
  @endif

  <form id="filterForm" method="GET" action="{{ route('admin.registrations.index') }}">
    <div class="r-toolbar">
      <div class="r-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, NISN, atau no. pendaftaran..." maxlength="100">
      </div>
      <button type="button" class="r-fbtn" onclick="toggleFilterPanel()"><i class="fa-solid fa-filter" style="font-size:12px"></i> Filter</button>
      <button type="submit" class="r-gobtn"><i class="fa-solid fa-magnifying-glass" style="font-size:12px"></i> Cari</button>
    </div>

    <div id="filterPanel" class="r-filters" style="display:none;">
      @php
        // Sumber data untuk picker
        $pickStatus = [
          ['v' => '',         'l' => 'Semua Status'],
          ['v' => 'pending',  'l' => 'Pending'],
          ['v' => 'verified', 'l' => 'Terverifikasi'],
          ['v' => 'rejected', 'l' => 'Ditolak'],
          ['v' => 'accepted', 'l' => 'Diterima'],
          ['v' => 're_registration_complete', 'l' => 'Daftar Ulang Selesai'],
        ];
        $pickPay = [
          ['v' => '',       'l' => 'Semua Status'],
          ['v' => 'unpaid', 'l' => 'Belum Dibayar'],
          ['v' => 'pending','l' => 'Menunggu Konfirmasi'],
          ['v' => 'paid',   'l' => 'Lunas'],
          ['v' => 'failed', 'l' => 'Gagal'],
        ];
        $pickDeadline = [
          ['v' => '', 'l' => 'Semua'],
          ['v' => '1','l' => 'Ada Batas Waktu'],
        ];
        $pickTracks = [['v' => '', 'l' => 'Semua Jalur']];
        foreach (($tracks ?? collect()) as $trk) {
          $pickTracks[] = ['v' => (string) $trk->id, 'l' => $trk->name];
        }
        $pickMajors = [['v' => '', 'l' => 'Semua Jurusan']];
        foreach (($majors ?? collect()) as $mjr) {
          $pickMajors[] = ['v' => (string) $mjr->id, 'l' => $mjr->name];
        }
      @endphp

      {{-- Trigger: Status --}}
      <div class="r-field">
        <label>Status</label>
        <button type="button" class="r-pick" data-picker="status" aria-haspopup="listbox" aria-expanded="false">
          <span class="pick-label is-placeholder">Pilih status…</span>
          <span class="pick-clear" data-clear="status" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
          <i class="fa-solid fa-chevron-down pick-caret"></i>
        </button>
        <input type="hidden" name="status" data-picker-input="status" value="{{ request('status') }}">
      </div>

      {{-- Trigger: Pembayaran --}}
      <div class="r-field">
        <label>Pembayaran</label>
        <button type="button" class="r-pick" data-picker="payment" aria-haspopup="listbox" aria-expanded="false">
          <span class="pick-label is-placeholder">Pilih pembayaran…</span>
          <span class="pick-clear" data-clear="payment" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
          <i class="fa-solid fa-chevron-down pick-caret"></i>
        </button>
        <input type="hidden" name="payment_status" data-picker-input="payment" value="{{ request('payment_status') }}">
      </div>

      {{-- Trigger: Deadline --}}
      <div class="r-field">
        <label>Deadline</label>
        <button type="button" class="r-pick" data-picker="deadline" aria-haspopup="listbox" aria-expanded="false">
          <span class="pick-label is-placeholder">Pilih deadline…</span>
          <span class="pick-clear" data-clear="deadline" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
          <i class="fa-solid fa-chevron-down pick-caret"></i>
        </button>
        <input type="hidden" name="deadline" data-picker-input="deadline" value="{{ request('deadline') }}">
      </div>

      {{-- Trigger: Jalur --}}
      <div class="r-field">
        <label>Jalur</label>
        <button type="button" class="r-pick" data-picker="track" aria-haspopup="listbox" aria-expanded="false">
          <span class="pick-label is-placeholder">Pilih jalur…</span>
          <span class="pick-clear" data-clear="track" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
          <i class="fa-solid fa-chevron-down pick-caret"></i>
        </button>
        <input type="hidden" name="track_id" data-picker-input="track" value="{{ request('track_id') }}">
      </div>

      {{-- Trigger: Jurusan --}}
      <div class="r-field">
        <label>Jurusan</label>
        <button type="button" class="r-pick" data-picker="major" aria-haspopup="listbox" aria-expanded="false">
          <span class="pick-label is-placeholder">Pilih jurusan…</span>
          <span class="pick-clear" data-clear="major" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
          <i class="fa-solid fa-chevron-down pick-caret"></i>
        </button>
        <input type="hidden" name="major_id" data-picker-input="major" value="{{ request('major_id') }}">
      </div>
    </div>
  </form>

  {{-- ============================================================
       Modal Pickers (Bringova) — satu modal reusable untuk semua picker
       ============================================================ --}}
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
    // Siapkan data JSON untuk picker (di-encode sekali, dipakai oleh JS)
    $pickerJson = [
      'status'   => $pickStatus,
      'payment'  => $pickPay,
      'deadline' => $pickDeadline,
      'track'    => $pickTracks,
      'major'    => $pickMajors,
    ];
    $pickerLabels = [
      'status'   => 'Pilih Status Pendaftaran',
      'payment'  => 'Pilih Status Pembayaran',
      'deadline' => 'Pilih Filter Deadline',
      'track'    => 'Pilih Jalur Pendaftaran',
      'major'    => 'Pilih Jurusan',
    ];
  @endphp

  {{-- Data picker ditempel di kontainer agar JS di layout bisa membacanya saat AJAX --}}
  <div id="reg-data" hidden
       data-picker='@json($pickerJson)'
       data-picker-labels='@json($pickerLabels)'></div>

  <div class="r-tabs">
    <a href="{{ route('admin.registrations.index') }}" class="r-tab doc-tab {{ !request('status') && !request('payment_status') && !request('deadline') && !request('search') && !request('track_id') && !request('major_id') ? 'active' : '' }}">Semua</a>
    <a href="{{ route('admin.registrations.index', ['status' => 'pending']) }}" class="r-tab doc-tab {{ request('status') == 'pending' ? 'active' : '' }}">Pending</a>
    <a href="{{ route('admin.registrations.index', ['status' => 'verified']) }}" class="r-tab doc-tab {{ request('status') == 'verified' ? 'active' : '' }}">Terverifikasi</a>
    <a href="{{ route('admin.registrations.index', ['status' => 'accepted']) }}" class="r-tab doc-tab {{ request('status') == 'accepted' ? 'active' : '' }}">Diterima</a>
    <a href="{{ route('admin.registrations.index', ['status' => 'rejected']) }}" class="r-tab doc-tab {{ request('status') == 'rejected' ? 'active' : '' }}">Ditolak</a>
    <a href="{{ route('admin.registrations.index', ['status' => 're_registration_complete']) }}" class="r-tab doc-tab {{ request('status') == 're_registration_complete' ? 'active' : '' }}">Daftar Ulang</a>
    <a href="{{ route('admin.registrations.index', ['deadline' => 1]) }}" class="r-tab doc-tab {{ request('deadline') ? 'active' : '' }}">Deadline</a>
  </div>

  @if ($registrations->isEmpty())
    <div class="r-empty">
      <i class="fa-regular fa-folder-open"></i>
      Tidak ada pendaftaran yang cocok
    </div>
  @else
    <div class="r-list">
      @foreach ($registrations as $reg)
      @php
        $name = $reg->applicant->full_name ?? '-';
        $email = $reg->applicant->user->email ?? '-';
        $track = $reg->registrationTrack->name ?? '-';
        $major = $reg->major->name ?? '-';
        $smap = [
          'pending' => ['label' => 'Menunggu', 'pill' => 'p-pending'],
          'verified' => ['label' => 'Terverifikasi', 'pill' => 'p-verified'],
          'rejected' => ['label' => 'Ditolak', 'pill' => 'p-rejected'],
          'accepted' => ['label' => 'Diterima', 'pill' => 'p-accepted'],
          're_registration_complete' => ['label' => 'Daftar Ulang', 'pill' => 'p-accepted'],
          'canceled' => ['label' => 'Dibatalkan', 'pill' => 'p-canceled'],
          'withdrawn' => ['label' => 'Mengundurkan', 'pill' => 'p-rejected'],
        ];
        $s = $smap[$reg->status] ?? ['label' => ucfirst(str_replace('_',' ',$reg->status)), 'pill' => 'p-pending'];
        $pm = ['unpaid' => ['label' => 'Belum Bayar', 'pill' => 'p-pending'], 'pending' => ['label' => 'Menunggu', 'pill' => 'p-pending'], 'paid' => ['label' => 'Lunas', 'pill' => 'p-accepted'], 'failed' => ['label' => 'Gagal', 'pill' => 'p-rejected']];
        $pay = $pm[$reg->payment_status] ?? ['label' => ucfirst($reg->payment_status), 'pill' => 'p-pending'];
      @endphp
      <div class="r-row">
        <span class="r-ic {{ $s['pill'] == 'p-rejected' ? 'red' : ($s['pill'] == 'p-pending' ? 'amber' : ($s['pill'] == 'p-accepted' ? 'green' : 'blue')) }}">
          <i class="fa-regular fa-file-lines"></i>
        </span>
        <div class="r-body">
          <div class="r-name">{{ $name }}</div>
          <div class="r-num">{{ $reg->registration_number }}</div>
          <div class="r-sub">
            <span class="r-sub-item email"><i class="fa-regular fa-envelope"></i> {{ $email }}</span>
            <span class="r-sub-item major"><i class="fa-solid fa-graduation-cap"></i> {{ $track }} &middot; {{ $major }}</span>
            <span class="r-sub-item date"><i class="fa-regular fa-clock"></i> {{ $reg->created_at->format('d M Y H:i') }}</span>
          </div>
        </div>
        <div class="r-badges">
          <span class="r-pill {{ $s['pill'] }}">{{ $s['label'] }}</span>
          <span class="r-pill {{ $pay['pill'] }}">{{ $pay['label'] }}</span>
        </div>
        <div class="r-actions">
          <a href="{{ route('admin.registrations.show', $reg) }}" class="r-act detail"><i class="fa-solid fa-eye" style="font-size:11px"></i> Detail</a>
          <button type="button" onclick="openResetModal({{ $reg->id }}, '{{ addslashes($reg->registration_number) }}', '{{ addslashes($name) }}')" class="r-act reset"><i class="fa-solid fa-rotate-left" style="font-size:11px"></i> Reset</button>
        </div>
      </div>
      @endforeach
    </div>

    <div class="r-pager">
      {{ $registrations->appends(request()->query())->links('vendor.pagination.bringova') }}
    </div>
  @endif
</div>

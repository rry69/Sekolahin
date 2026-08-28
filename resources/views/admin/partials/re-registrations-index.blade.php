<style>
  /* ===================== RE-REGISTRATIONS INDEX — Bringova (no cards, scoped) ===================== */
  .rre {
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
    padding: 28px 28px 44px;
    background: #f6f7fb;
  }

  /* ---------- header ---------- */
  .rre .r-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
  .rre .r-crumb a { color: var(--coral); text-decoration: none; }
  .rre .r-crumb a:hover { text-decoration: underline; }
  .rre .r-crumb .sep { color: #d3d6de; }
  .rre .r-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 2px; }
  .rre .r-meta { font-size: 13px; color: var(--muted); margin-bottom: 22px; }

  /* ---------- alerts (flash) ---------- */
  .rre .r-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px; font-weight: 500; }
  .rre .r-alert i { margin-top: 2px; }
  .rre .r-alert.success { background: var(--green-soft); color: var(--green); }
  .rre .r-alert.error   { background: var(--red-soft);   color: var(--red); }
  .rre .r-alert.info    { background: var(--blue-soft);  color: var(--blue); }

  /* ---------- toolbar: search + filter ---------- */
  .rre .r-toolbar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 16px; }
  .rre .r-search { position: relative; flex: 1; min-width: 200px; }
  .rre .r-search i { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 13px; pointer-events: none; }
  .rre .r-search input { width: 100%; padding: 11px 14px 11px 38px; border: 1px solid rgba(26,26,46,0.14); border-radius: 12px; font-size: 13.5px; color: var(--ink); background: rgba(255,255,255,0.55); transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
  .rre .r-search input::placeholder { color: var(--muted); }
  .rre .r-search input:focus { outline: none; border-color: var(--coral); box-shadow: 0 0 0 4px rgba(255,107,107,0.14); background: #fff; }
  .rre .r-fbtn, .rre .r-gobtn { display: inline-flex; align-items: center; gap: 7px; border: none; cursor: pointer; border-radius: 12px; padding: 11px 18px; font-size: 13px; font-weight: 700; transition: transform .15s ease, filter .15s ease; }
  .rre .r-fbtn { background: rgba(255,255,255,0.7); color: var(--ink); box-shadow: 0 4px 14px -10px rgba(26,26,46,0.3); }
  .rre .r-fbtn:hover { background: #fff; color: var(--coral); }
  .rre .r-gobtn { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 8px 18px -8px rgba(255,107,107,0.6); }
  .rre .r-gobtn:hover { filter: brightness(1.03); transform: translateY(-1px); }

  /* ---------- filter panel ---------- */
  .rre .r-filters { display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end; padding: 18px; margin-bottom: 20px; border: 1px dashed rgba(26,26,46,0.14); border-radius: 14px; background: rgba(255,255,255,0.30); }
  .rre .r-field { display: flex; flex-direction: column; gap: 5px; }

  /* ---------- picker trigger (pengganti <select>) ---------- */
  .rre .r-pick {
    display: inline-flex; align-items: center; gap: 8px; flex-wrap: nowrap;
    padding: 9px 4px; border: none; border-bottom: 1px solid rgba(26,26,46,0.18); border-radius: 0;
    font-size: 13px; color: var(--ink); background: transparent; min-width: 180px;
    cursor: pointer; text-align: left; min-height: 38px; max-width: 100%;
    transition: border-color .18s ease, color .18s ease;
  }
  .rre .r-pick:hover { border-bottom-color: var(--coral); }
  .rre .r-pick:focus { outline: none; border-bottom-color: var(--coral); }
  .rre .r-pick .pick-label { flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .rre .r-pick .pick-label.is-placeholder { color: var(--muted); }
  .rre .r-pick .pick-caret { display: none; }
  .rre .r-pick .pick-clear { flex: 0 0 auto; display: none; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 6px; background: var(--gray-soft); color: var(--gray); cursor: pointer; font-size: 9px; user-select: none; }
  .rre .r-pick .pick-clear:hover { background: var(--red-soft); color: var(--red); }
  .rre .r-pick.has-value .pick-clear { display: inline-flex; }
  .rre .r-pick.has-value .pick-label.is-placeholder { display: none; }

  /* ---------- modal picker (Bringova) ---------- */
  .rre .picker-backdrop {
    position: fixed; inset: 0; z-index: 80;
    background: rgba(26,26,46,0.32);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    display: none; align-items: flex-start; justify-content: center;
    padding: 80px 16px 16px;
    animation: rrePickerFade .18s ease-out;
  }
  .rre .picker-backdrop.is-open { display: flex; }
  @keyframes rrePickerFade { from { opacity: 0; } to { opacity: 1; } }

  .rre .picker-panel {
    width: 100%; max-width: 380px; max-height: min(520px, calc(100vh - 120px));
    display: flex; flex-direction: column;
    background: #fff; border-radius: 18px;
    box-shadow: 0 20px 50px -16px rgba(26,26,46,0.35), 0 0 0 1px rgba(26,26,46,0.06);
    overflow: hidden;
    animation: rrePickerPop .22s cubic-bezier(.22,1.2,.36,1);
  }
  @keyframes rrePickerPop { from { opacity: 0; transform: translateY(-6px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }

  .rre .picker-head { display: flex; align-items: center; gap: 10px; padding: 14px 16px; border-bottom: 1px solid var(--divider); }
  .rre .picker-head .picker-title { font-size: 14px; font-weight: 700; color: var(--ink); flex: 1; }
  .rre .picker-head .picker-close { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 8px; border: none; background: transparent; color: var(--muted); cursor: pointer; font-size: 12px; transition: background-color .15s ease, color .15s ease; }
  .rre .picker-head .picker-close:hover { background: var(--gray-soft); color: var(--ink); }

  .rre .picker-search { position: relative; padding: 10px 14px; border-bottom: 1px solid var(--divider); }
  .rre .picker-search i { position: absolute; left: 24px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 12px; pointer-events: none; }
  .rre .picker-search input { width: 100%; padding: 9px 12px 9px 32px; border: 1px solid rgba(26,26,46,0.14); border-radius: 10px; font-size: 13px; color: var(--ink); background: rgba(255,255,255,0.7); transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
  .rre .picker-search input:focus { outline: none; border-color: var(--coral); background: #fff; box-shadow: 0 0 0 3px rgba(255,107,107,0.12); }

  .rre .picker-list { flex: 1; overflow-y: auto; padding: 6px 8px; }
  .rre .picker-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; font-size: 13px; color: var(--ink); cursor: pointer; user-select: none; transition: background-color .15s ease, color .15s ease; }
  .rre .picker-item:hover, .rre .picker-item.is-active { background: var(--coral-soft); color: var(--coral); }
  .rre .picker-item.is-selected { background: var(--coral); color: #fff; font-weight: 600; }
  .rre .picker-item.is-selected:hover { background: var(--coral); }
  .rre .picker-item .pi-label { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .rre .picker-item .pi-check { font-size: 11px; opacity: 0; }
  .rre .picker-item.is-selected .pi-check { opacity: 1; }
  .rre .picker-empty { padding: 26px 12px; text-align: center; color: var(--muted); font-size: 12.5px; }
  .rre .picker-empty i { display: block; font-size: 20px; margin-bottom: 6px; color: #d3d6de; }

  .rre .picker-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 10px 14px; border-top: 1px solid var(--divider); background: rgba(255,255,255,0.5); }
  .rre .picker-foot .picker-clear-all { display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px; border-radius: 9px; border: none; background: transparent; color: var(--muted); font-size: 12px; font-weight: 600; cursor: pointer; transition: color .15s ease, background-color .15s ease; }
  .rre .picker-foot .picker-clear-all:hover { color: var(--red); background: var(--red-soft); }
  .rre .picker-foot .picker-done { display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 9px; border: none; background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; font-size: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 6px 14px -6px rgba(255,107,107,0.55); transition: filter .15s ease, transform .15s ease; }
  .rre .picker-foot .picker-done:hover { filter: brightness(1.04); transform: translateY(-1px); }

  /* ---------- tabs (underline, no box) ---------- */
  .rre .r-tabs { display: flex; gap: 18px; border-bottom: 1px solid var(--divider); margin-bottom: 22px; flex-wrap: wrap; align-items: center; }
  .rre .r-tabs a.doc-tab, .rre .r-tabs a.r-tab {
    all: unset;
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 2px 11px; font-size: 13px; font-weight: 600; color: var(--muted);
    text-decoration: none; border-bottom: 2.5px solid transparent; margin-bottom: -1px;
    cursor: pointer; white-space: nowrap; transition: color .18s ease;
  }
  .rre .r-tabs a.r-tab:hover, .rre .r-tabs a.doc-tab:hover { color: var(--ink); }
  .rre .r-tabs a.r-tab.active, .rre .r-tabs a.doc-tab.active { color: var(--coral); border-bottom-color: var(--coral); }
  .rre .r-tabs a .badge { background: var(--coral-soft); color: var(--coral); border-radius: 20px; padding: 1px 8px; font-size: 10.5px; font-weight: 700; }
  .rre .r-tabs a.active .badge { background: var(--coral); color: #fff; }
  .rre .r-tabs-spacer { flex: 1; }

  /* ---------- list rows (no card, divider) ---------- */
  .rre .r-list { display: flex; flex-direction: column; }
  .rre .r-row { display: flex; align-items: center; gap: 15px; padding: 16px 4px; border-bottom: 1px solid var(--divider); }
  .rre .r-row:last-child { border-bottom: none; }
  .rre .r-ic { flex: 0 0 auto; width: 46px; height: 46px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 17px; }
  .rre .r-ic.amber  { background: var(--amber-soft);  color: #b45309; }
  .rre .r-ic.green  { background: var(--green-soft);  color: var(--green); }
  .rre .r-ic.red    { background: var(--red-soft);    color: var(--red); }
  .rre .r-ic.coral  { background: var(--coral-soft);  color: var(--coral); }
  .rre .r-body { flex: 1; min-width: 0; }
  .rre .r-name { font-size: 14px; font-weight: 600; color: var(--ink); }
  .rre .r-sub { font-size: 12px; color: var(--muted); margin-top: 2px; }

  /* ---------- pills ---------- */
  .rre .r-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 700; white-space: nowrap; }
  .rre .r-pill.green  { background: var(--green-soft);  color: var(--green); }
  .rre .r-pill.amber  { background: var(--amber-soft);  color: #b45309; }
  .rre .r-pill.red    { background: var(--red-soft);    color: var(--red); }

  /* ---------- buttons ---------- */
  .rre .r-act { display: inline-flex; align-items: center; gap: 6px; border: none; cursor: pointer; border-radius: 9px; padding: 7px 13px; font-size: 11.5px; font-weight: 700; transition: transform .15s ease, filter .15s ease, background-color .15s ease, color .15s ease; }
  .rre .r-act:hover { transform: translateY(-1px); }
  .rre .r-act.detail { background: var(--coral-soft); color: var(--coral); text-decoration: none; }
  .rre .r-act.detail:hover { background: var(--coral); color: #fff; }
  .rre .r-act.verify { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 6px 14px -8px rgba(255,107,107,0.55); }
  .rre .r-act.verify:hover { filter: brightness(1.04); }
  .rre .r-act.reject { background: var(--red-soft); color: var(--red); }
  .rre .r-act.reject:hover { background: var(--red); color: #fff; }

  /* ---------- empty ---------- */
  .rre .r-empty { text-align: center; color: var(--muted); font-size: 13px; padding: 30px 0; }
  .rre .r-empty i { display: block; font-size: 26px; margin-bottom: 8px; color: #d3d6de; }

  /* ---------- pagination ---------- */
  .rre .r-pager { margin-top: 22px; display: flex; justify-content: center; }
  .rre .r-pager > nav { display: flex; justify-content: center; }

  /* ---------- reject modal (Bringova) ---------- */
  .rre .r-modal-backdrop { position: fixed; inset: 0; z-index: 90; background: rgba(26,26,46,0.36); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; padding: 16px; }
  .rre .r-modal-backdrop.is-open { display: flex; }
  .rre .r-modal { width: 100%; max-width: 420px; background: #fff; border-radius: 18px; padding: 22px; box-shadow: 0 24px 60px -18px rgba(26,26,46,0.4); animation: rreModalPop .2s cubic-bezier(.22,1.2,.36,1); }
  @keyframes rreModalPop { from { opacity: 0; transform: scale(0.97) translateY(4px); } to { opacity: 1; transform: scale(1) translateY(0); } }
  .rre .r-modal-title { display: flex; align-items: center; gap: 9px; font-size: 15px; font-weight: 700; color: var(--ink); margin-bottom: 14px; }
  .rre .r-modal-title i { color: var(--red); }
  .rre .r-modal label { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
  .rre .r-modal textarea { width: 100%; padding: 9px 12px; border: 1px solid rgba(26,26,46,0.14); border-radius: 10px; font-size: 13px; font-family: inherit; color: var(--ink); background: rgba(255,255,255,0.55); transition: border-color .18s ease, box-shadow .18s ease; resize: vertical; }
  .rre .r-modal textarea:focus { outline: none; border-color: var(--red); box-shadow: 0 0 0 3px rgba(239,68,68,0.12); background: #fff; }
  .rre .r-modal-foot { display: flex; gap: 8px; justify-content: flex-end; margin-top: 16px; }

  /* ---------- responsive ---------- */
  @media (max-width: 620px) {
    .rre { padding: 20px 16px 32px; }
    .rre .r-row { flex-wrap: wrap; }
    .rre .r-actions { width: 100%; justify-content: flex-end; }
  }
</style>

<div class="rre">
  <div class="r-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span>Daftar Ulang</span>
  </div>
  <h1 class="r-title">Daftar Ulang Pendaftar</h1>
  <p class="r-meta">Kelola pengajuan daftar ulang siswa</p>

  @if (session('success'))
    <div class="r-alert success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
  @endif
  @if (session('error'))
    <div class="r-alert error"><i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span></div>
  @endif

  <form id="filterForm" method="GET" action="{{ route('admin.re-registrations.index') }}">
    <div class="r-toolbar">
      <div class="r-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor registrasi atau nama siswa...">
      </div>
      <button type="button" class="r-fbtn" onclick="toggleFilterPanel()"><i class="fa-solid fa-filter"></i> Filter</button>
      <button type="submit" class="r-gobtn"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
    </div>

    <div id="filterPanel" class="r-filters" style="{{ request('level') ? 'display:flex' : 'display:none' }}">
      <div class="r-field">
        <label>Jenjang</label>
        <button type="button" class="r-pick" data-picker="level" aria-haspopup="listbox" aria-expanded="false">
          <span class="pick-label is-placeholder">Pilih jenjang…</span>
          <span class="pick-clear" data-clear="level" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
          <i class="fa-solid fa-chevron-down pick-caret"></i>
        </button>
        <input type="hidden" name="level" data-picker-input="level" value="{{ request('level') }}">
      </div>
    </div>
  </form>

  <div class="r-tabs">
    <a href="{{ route('admin.re-registrations.index') }}" class="r-tab doc-tab {{ !request('status') && !request('level') && !request('search') ? 'active' : '' }}">Semua</a>
    <a href="{{ route('admin.re-registrations.index', ['status' => 'pending']) }}" class="r-tab doc-tab {{ request('status') == 'pending' ? 'active' : '' }}">Pending</a>
    <a href="{{ route('admin.re-registrations.index', ['status' => 'completed']) }}" class="r-tab doc-tab {{ request('status') == 'completed' ? 'active' : '' }}">Selesai</a>
  </div>

  @if ($reRegistrations->isEmpty())
    <div class="r-empty"><i class="fa-regular fa-folder-open"></i>Tidak ada data daftar ulang</div>
  @else
    <div class="r-list">
      @foreach ($reRegistrations as $reReg)
        @php
          $reg = $reReg->registration;
          $ic = $reReg->status === 'completed' ? 'green' : ($reReg->status === 'rejected' ? 'red' : 'amber');
          $icIcon = $reReg->status === 'completed' ? 'fa-clipboard-check' : ($reReg->status === 'rejected' ? 'fa-clipboard-xmark' : 'fa-hourglass-half');
          $pill = $reReg->status === 'completed' ? 'green' : ($reReg->status === 'rejected' ? 'red' : 'amber');
          $pillLabel = $reReg->status === 'completed' ? 'Selesai' : ($reReg->status === 'rejected' ? 'Ditolak' : 'Pending');
        @endphp
        <div class="r-row">
          <span class="r-ic {{ $ic }}"><i class="fa-solid {{ $icIcon }}"></i></span>
          <div class="r-body">
            <div class="r-name">{{ $reg->applicant->full_name }}</div>
            <div class="r-sub">{{ $reg->registration_number }} · {{ $reg->applicant->user->email }}</div>
            <div class="r-sub" style="margin-top:3px">
              <i class="fa-regular fa-calendar"></i> {{ $reReg->submitted_at ? $reReg->submitted_at->format('d M Y H:i') : '-' }}
            </div>
          </div>
          <span class="r-pill {{ $pill }}">{{ $pillLabel }}</span>
          <div class="r-actions" style="display:flex;gap:6px;">
            <a href="{{ route('admin.re-registrations.show', $reReg) }}" class="r-act detail"><i class="fa-solid fa-eye"></i> Detail</a>
            @if ($reReg->status === 'pending')
              <button type="button" onclick="openReRegVerify({{ $reReg->id }}, '{{ $reg->registration_number }}')" class="r-act verify"><i class="fa-solid fa-check"></i> Verifikasi</button>
              <button type="button" onclick="showReRegRejectModal({{ $reReg->id }})" class="r-act reject"><i class="fa-solid fa-xmark"></i> Tolak</button>
            @endif
          </div>
        </div>
      @endforeach
    </div>

    <div class="r-pager">
      {{ $reRegistrations->appends(request()->query())->links('vendor.pagination.bringova') }}
    </div>
  @endif

  {{-- ============================================================
       Modal Pickers (Bringova) — reuse global picker system (level)
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
    $pickLevels = [['v' => '', 'l' => 'Semua Jenjang']];
    foreach (($schoolLevels ?? []) as $lv) {
      $pickLevels[] = ['v' => (string) $lv->id, 'l' => $lv->name];
    }
    $pickerJson = ['level' => $pickLevels];
    $pickerLabels = ['level' => 'Pilih Jenjang'];
  @endphp

  <div id="reg-data" hidden data-picker='@json($pickerJson)' data-picker-labels='@json($pickerLabels)'></div>

  {{-- Modal Tolak Daftar Ulang (Bringova) --}}
  <div id="reRegRejectModal" class="r-modal-backdrop" style="display:none">
    <div class="r-modal" role="dialog" aria-modal="true">
      <div class="r-modal-title"><i class="fa-solid fa-circle-exclamation"></i> Tolak Daftar Ulang</div>
      <form id="reRegRejectForm" method="POST">
        @csrf
        <label>Catatan / Alasan Penolakan</label>
        <textarea name="notes" rows="4" placeholder="Alasan penolakan (wajib)" required></textarea>
        <div class="r-modal-foot">
          <button type="button" onclick="hideReRegRejectModal()" class="r-act detail">Batal</button>
          <button type="submit" class="r-act reject"><i class="fa-solid fa-xmark"></i> Tolak</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Modal Konfirmasi Verifikasi (Bringova) --}}
  <div id="reRegVerifyModal" class="r-modal-backdrop" style="display:none">
    <div class="r-modal" role="dialog" aria-modal="true">
      <div class="r-modal-title" style="color:var(--green)"><i class="fa-solid fa-circle-check"></i> Verifikasi Daftar Ulang</div>
      <p style="font-size:13px;color:var(--muted);line-height:1.6">Verifikasi daftar ulang <b id="reRegVerifyNumber" style="color:var(--ink)"></b>? Pendaftaran akan ditandai <b style="color:var(--ink)">Daftar Ulang Selesai</b>.</p>
      <form id="reRegVerifyForm" method="POST" style="margin-top:0">
        @csrf
      </form>
      <div class="r-modal-foot">
        <button type="button" onclick="closeReRegVerify()" class="r-act detail">Batal</button>
        <button type="button" onclick="submitReRegVerify()" class="r-act verify"><i class="fa-solid fa-check"></i> Ya, Verifikasi</button>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  var pendingReRegId = null;

  window.openReRegVerify = function (id, regNumber) {
    pendingReRegId = id;
    var num = document.getElementById('reRegVerifyNumber');
    if (num) num.textContent = regNumber;
    var form = document.getElementById('reRegVerifyForm');
    if (form) form.action = '/admin/re-registrations/' + id + '/verify';
    var m = document.getElementById('reRegVerifyModal');
    if (m) { m.style.display = 'flex'; m.classList.add('is-open'); }
  };

  window.closeReRegVerify = function () {
    var m = document.getElementById('reRegVerifyModal');
    if (m) { m.style.display = 'none'; m.classList.remove('is-open'); }
    pendingReRegId = null;
  };

  window.submitReRegVerify = function () {
    var form = document.getElementById('reRegVerifyForm');
    if (form) form.submit();
  };

  var vm = document.getElementById('reRegVerifyModal');
  if (vm) vm.addEventListener('click', function (e) { if (e.target === this) closeReRegVerify(); });

  var rm = document.getElementById('reRegRejectModal');
  if (rm) rm.addEventListener('click', function (e) { if (e.target === this) hideReRegRejectModal(); });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      var vm2 = document.getElementById('reRegVerifyModal');
      if (vm2 && vm2.style.display === 'flex') closeReRegVerify();
    }
  });
})();
</script>

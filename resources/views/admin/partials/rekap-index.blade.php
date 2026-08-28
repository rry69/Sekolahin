<style>
  /* ===================== REKAP SISWA DITERIMA — Bringova (no cards, scoped) ===================== */
  .rkp {
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
  .rkp .k-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
  .rkp .k-crumb a { color: var(--coral); text-decoration: none; }
  .rkp .k-crumb a:hover { text-decoration: underline; }
  .rkp .k-crumb .sep { color: #d3d6de; }
  .rkp .k-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 2px; }
  .rkp .k-meta { font-size: 13px; color: var(--muted); margin-bottom: 20px; }
  .rkp .k-meta b { color: var(--ink); font-weight: 600; }

  /* ---------- alerts (flash) ---------- */
  .rkp .k-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px; font-weight: 500; }
  .rkp .k-alert i { margin-top: 2px; }
  .rkp .k-alert.success { background: var(--green-soft); color: var(--green); }
  .rkp .k-alert.error   { background: var(--red-soft);   color: var(--red); }
  .rkp .k-alert.info    { background: var(--blue-soft);  color: var(--blue); }

  /* ---------- summary badge (no card) ---------- */
  .rkp .k-summary { display: flex; align-items: center; gap: 14px; padding: 14px 2px; margin-bottom: 6px; }
  .rkp .k-sum-ic { flex: 0 0 auto; width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 20px; background: var(--green-soft); color: var(--green); }
  .rkp .k-sum-lbl { font-size: 12px; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: .3px; }
  .rkp .k-sum-val { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; line-height: 1.1; }

  /* ---------- tabs (underline) + export actions ---------- */
  .rkp .k-tabs-row { display: flex; align-items: flex-end; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-top: 6px; }
  .rkp .k-tabs { display: flex; gap: 16px; border-bottom: 1px solid var(--divider); flex-wrap: wrap; min-width: 0; }
  .rkp .k-tabs a.k-tab, .rkp .k-tabs a.doc-tab {
    all: unset;
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 2px 11px; font-size: 13px; font-weight: 600; color: var(--muted);
    text-decoration: none; border-bottom: 2.5px solid transparent; margin-bottom: -1px;
    cursor: pointer; white-space: nowrap;
    transition: color .18s ease;
  }
  .rkp .k-tabs a.k-tab:hover, .rkp .k-tabs a.doc-tab:hover { color: var(--ink); }
  .rkp .k-tabs a.k-tab.active, .rkp .k-tabs a.doc-tab.active { color: var(--coral); border-bottom-color: var(--coral); }
  .rkp .k-tabs a .badge { background: var(--coral-soft); color: var(--coral); border-radius: 20px; padding: 1px 8px; font-size: 10.5px; font-weight: 700; }
  .rkp .k-tabs a.active .badge { background: var(--coral); color: #fff; }
  .rkp .k-tools { display: flex; align-items: center; gap: 8px; flex-shrink: 0; padding-bottom: 10px; }

  /* ---------- buttons ---------- */
  .rkp .k-btn { display: inline-flex; align-items: center; gap: 7px; border: none; cursor: pointer; border-radius: 11px; padding: 10px 16px; font-size: 12.5px; font-weight: 700; transition: transform .15s ease, filter .15s ease, background-color .15s ease, color .15s ease; text-decoration: none; }
  .rkp .k-btn:hover { transform: translateY(-1px); }
  .rkp .k-btn.sm { padding: 7px 12px; font-size: 11.5px; border-radius: 9px; }
  .rkp .k-btn.coral { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 6px 16px -8px rgba(255,107,107,0.6); }
  .rkp .k-btn.coral:hover { filter: brightness(1.04); }
  .rkp .k-btn.green { background: var(--green); color: #fff; }
  .rkp .k-btn.green:hover { background: #059669; }
  .rkp .k-btn.red { background: var(--red); color: #fff; }
  .rkp .k-btn.red:hover { background: #dc2626; }
  .rkp .k-btn.ghost { background: rgba(255,255,255,0.7); color: var(--ink); box-shadow: 0 2px 10px -8px rgba(26,26,46,0.3); }
  .rkp .k-btn.ghost:hover { background: #fff; color: var(--coral); }
  .rkp .k-btn.coral-soft { background: var(--coral-soft); color: var(--coral); }
  .rkp .k-btn.coral-soft:hover { background: var(--coral); color: #fff; }

  /* ---------- toolbar: search + filter ---------- */
  .rkp .k-toolbar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin: 18px 0 16px; }
  .rkp .k-search { position: relative; flex: 1; min-width: 200px; }
  .rkp .k-search i { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 13px; pointer-events: none; }
  .rkp .k-search input { width: 100%; padding: 11px 14px 11px 38px; border: 1px solid rgba(26,26,46,0.14); border-radius: 12px; font-size: 13.5px; color: var(--ink); background: rgba(255,255,255,0.55); transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
  .rkp .k-search input::placeholder { color: var(--muted); }
  .rkp .k-search input:focus { outline: none; border-color: var(--coral); box-shadow: 0 0 0 4px rgba(255,107,107,0.14); background: #fff; }
  .rkp .k-fbtn, .rkp .k-gobtn { display: inline-flex; align-items: center; gap: 7px; border: none; cursor: pointer; border-radius: 12px; padding: 11px 18px; font-size: 13px; font-weight: 700; transition: transform .15s ease, filter .15s ease; }
  .rkp .k-fbtn { background: rgba(255,255,255,0.7); color: var(--ink); box-shadow: 0 4px 14px -10px rgba(26,26,46,0.3); }
  .rkp .k-fbtn:hover { background: #fff; color: var(--coral); }
  .rkp .k-gobtn { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 8px 18px -8px rgba(255,107,107,0.6); }
  .rkp .k-gobtn:hover { filter: brightness(1.03); transform: translateY(-1px); }

  /* ---------- filter panel ---------- */
  .rkp .k-filters { display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end; padding: 18px; margin-bottom: 20px; border: 1px dashed rgba(26,26,46,0.14); border-radius: 14px; background: rgba(255,255,255,0.30); }
  .rkp .k-field { display: flex; flex-direction: column; gap: 5px; }
  .rkp .k-field label { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .3px; }

  /* ---------- picker trigger (pengganti <select>) ---------- */
  .rkp .r-pick {
    display: inline-flex; align-items: center; gap: 8px; flex-wrap: nowrap;
    padding: 9px 4px; border: none; border-bottom: 1px solid rgba(26,26,46,0.18); border-radius: 0;
    font-size: 13px; color: var(--ink); background: transparent; min-width: 180px;
    cursor: pointer; text-align: left; min-height: 38px; max-width: 100%;
    transition: border-color .18s ease, color .18s ease;
  }
  .rkp .r-pick:hover { border-bottom-color: var(--coral); }
  .rkp .r-pick:focus { outline: none; border-bottom-color: var(--coral); }
  .rkp .r-pick .pick-label { flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .rkp .r-pick .pick-label.is-placeholder { color: var(--muted); }
  .rkp .r-pick .pick-caret { display: none; }
  .rkp .r-pick .pick-clear { flex: 0 0 auto; display: none; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 6px; background: var(--gray-soft); color: var(--gray); cursor: pointer; font-size: 9px; user-select: none; }
  .rkp .r-pick .pick-clear:hover { background: var(--red-soft); color: var(--red); }
  .rkp .r-pick.has-value .pick-clear { display: inline-flex; }
  .rkp .r-pick.has-value .pick-label.is-placeholder { display: none; }

  /* ---------- modal picker (Bringova) ---------- */
  .rkp .picker-backdrop {
    position: fixed; inset: 0; z-index: 80; background: rgba(26,26,46,0.32);
    backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
    display: none; align-items: flex-start; justify-content: center; padding: 80px 16px 16px;
    animation: kPickerFade .18s ease-out;
  }
  .rkp .picker-backdrop.is-open { display: flex; }
  @keyframes kPickerFade { from { opacity: 0; } to { opacity: 1; } }
  .rkp .picker-panel {
    width: 100%; max-width: 380px; max-height: min(520px, calc(100vh - 120px));
    display: flex; flex-direction: column; background: #fff; border-radius: 18px;
    box-shadow: 0 20px 50px -16px rgba(26,26,46,0.35), 0 0 0 1px rgba(26,26,46,0.06);
    overflow: hidden; animation: kPickerPop .22s cubic-bezier(.22,1.2,.36,1);
  }
  @keyframes kPickerPop { from { opacity: 0; transform: translateY(-6px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
  .rkp .picker-head { display: flex; align-items: center; gap: 10px; padding: 14px 16px; border-bottom: 1px solid var(--divider); }
  .rkp .picker-head .picker-title { font-size: 14px; font-weight: 700; color: var(--ink); flex: 1; }
  .rkp .picker-head .picker-close { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 8px; border: none; background: transparent; color: var(--muted); cursor: pointer; font-size: 12px; transition: background-color .15s ease, color .15s ease; }
  .rkp .picker-head .picker-close:hover { background: var(--gray-soft); color: var(--ink); }
  .rkp .picker-search { position: relative; padding: 10px 14px; border-bottom: 1px solid var(--divider); }
  .rkp .picker-search i { position: absolute; left: 24px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 12px; pointer-events: none; }
  .rkp .picker-search input { width: 100%; padding: 9px 12px 9px 32px; border: 1px solid rgba(26,26,46,0.14); border-radius: 10px; font-size: 13px; color: var(--ink); background: rgba(255,255,255,0.7); transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
  .rkp .picker-search input:focus { outline: none; border-color: var(--coral); background: #fff; box-shadow: 0 0 0 3px rgba(255,107,107,0.12); }
  .rkp .picker-list { flex: 1; overflow-y: auto; padding: 6px 8px; }
  .rkp .picker-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; font-size: 13px; color: var(--ink); cursor: pointer; user-select: none; transition: background-color .15s ease, color .15s ease; }
  .rkp .picker-item:hover, .rkp .picker-item.is-active { background: var(--coral-soft); color: var(--coral); }
  .rkp .picker-item.is-selected { background: var(--coral); color: #fff; font-weight: 600; }
  .rkp .picker-item.is-selected:hover { background: var(--coral); }
  .rkp .picker-item .pi-label { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .rkp .picker-item .pi-check { font-size: 11px; opacity: 0; }
  .rkp .picker-item.is-selected .pi-check { opacity: 1; }
  .rkp .picker-empty { padding: 26px 12px; text-align: center; color: var(--muted); font-size: 12.5px; }
  .rkp .picker-empty i { display: block; font-size: 20px; margin-bottom: 6px; color: #d3d6de; }
  .rkp .picker-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 10px 14px; border-top: 1px solid var(--divider); background: rgba(255,255,255,0.5); }
  .rkp .picker-foot .picker-clear-all { display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px; border-radius: 9px; border: none; background: transparent; color: var(--muted); font-size: 12px; font-weight: 600; cursor: pointer; transition: color .15s ease, background-color .15s ease; }
  .rkp .picker-foot .picker-clear-all:hover { color: var(--red); background: var(--red-soft); }
  .rkp .picker-foot .picker-done { display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 9px; border: none; background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; font-size: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 6px 14px -6px rgba(255,107,107,0.55); transition: filter .15s ease, transform .15s ease; }
  .rkp .picker-foot .picker-done:hover { filter: brightness(1.04); transform: translateY(-1px); }

  /* ---------- list rows (no card, divider) ---------- */
  .rkp .k-list { display: flex; flex-direction: column; }
  .rkp .k-row { display: flex; align-items: center; gap: 15px; padding: 16px 4px; border-bottom: 1px solid var(--divider); }
  .rkp .k-row:last-child { border-bottom: none; }
  .rkp .k-ic { flex: 0 0 auto; width: 46px; height: 46px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 17px; background: var(--green-soft); color: var(--green); }
  .rkp .k-body { flex: 1; min-width: 0; }
  .rkp .k-name { font-size: 14px; font-weight: 700; color: var(--ink); }
  .rkp .k-sub { font-size: 12px; color: var(--muted); margin-top: 2px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
  .rkp .k-sub .k-reg { color: var(--ink); font-weight: 700; font-size: 11.5px; letter-spacing: .02em; }
  .rkp .k-sub .k-dot { color: var(--muted); }
  .rkp .k-sub .k-email { color: var(--muted); word-break: break-all; }
  .rkp .k-mid { flex: 0 0 140px; min-width: 140px; }
  .rkp .k-mid .k-mid-label { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: .3px; font-weight: 600; }
  .rkp .k-mid .k-mid-val { font-size: 13px; color: var(--ink); font-weight: 600; }
  .rkp .k-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 5px; }
  .rkp .k-pill { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; white-space: nowrap; }
  .rkp .k-pill.green { background: var(--green-soft); color: var(--green); }
  .rkp .k-pill.blue  { background: var(--blue-soft);  color: var(--blue); }
  .rkp .k-pill.coral { background: var(--coral-soft); color: var(--coral); }
  .rkp .k-pill.amber { background: var(--amber-soft); color: #b45309; }
  .rkp .k-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; min-width: 200px; justify-content: flex-end; }
  /* mobile tabs -> dropdown */
  .rkp .k-tabs-mobile { display: none; }

  /* ---------- empty ---------- */
  .rkp .k-empty { text-align: center; color: var(--muted); font-size: 13.5px; padding: 40px 0; }
  .rkp .k-empty i { display: block; font-size: 28px; margin-bottom: 8px; color: #d3d6de; }

  /* ---------- pagination ---------- */
  .rkp .k-pager { margin-top: 22px; display: flex; justify-content: center; }
  .rkp .k-pager > nav { display: flex; justify-content: center; }

  /* ---------- responsive: tablet (641-1024) ---------- */
  @media (min-width: 641px) and (max-width: 1024px) {
    .rkp .k-row { flex-wrap: wrap; gap: 12px 14px; padding: 16px 4px; }
    .rkp .k-body { flex: 1 1 280px; min-width: 220px; }
    .rkp .k-mid { flex: 0 0 120px; min-width: 100px; }
    .rkp .k-actions { flex: 0 0 auto; margin-left: auto; min-width: 0; }
    .rkp .k-sub { flex-wrap: wrap; gap: 4px 8px; }
    .rkp .k-tabs-row { gap: 12px; }
  }
  /* ---------- responsive: mobile (≤640px) — hierarchy Nama → REG/Email → Tags → NIS → Status+Action ---------- */
  @media (max-width: 640px) {
    .rkp { padding: 18px 14px 28px; }
    .rkp .k-title { font-size: 22px; }
    .rkp .k-meta { font-size: 12.5px; }
    .rkp .k-summary { padding: 12px 0; }
    .rkp .k-tabs { display: none; }
    .rkp .k-tabs-mobile { display: flex; align-items: center; width: 100%; }
    .rkp .k-tabs-mobile .r-pick { width: 100%; min-width: 0; background: rgba(255,255,255,.65); border: 1px solid rgba(26,26,46,.08); border-bottom: 1px solid rgba(26,26,46,.12); border-radius: 12px; padding: 11px 14px; font-weight: 600; }
    .rkp .k-tabs-mobile .r-pick .pick-label { font-weight: 700; }
    .rkp .k-tabs-row { flex-direction: column; align-items: stretch; gap: 12px; }
    .rkp .k-tools { padding-bottom: 0; flex-wrap: wrap; }
    .rkp .k-toolbar { gap: 8px; }
    .rkp .k-search { min-width: 0; flex: 1 1 100%; }
    .rkp .k-fbtn, .rkp .k-gobtn { flex: 1 1 auto; justify-content: center; min-height: 42px; }
    .rkp .k-filters { padding: 14px; }
    .rkp .k-field { flex: 1 1 100%; }
    .rkp .k-field .r-pick { min-width: 0; width: 100%; }

    .rkp .k-list { gap: 0; }
    .rkp .k-row {
      display: grid;
      grid-template-columns: 46px 1fr;
      grid-template-areas:
        "ic body"
        "mid mid"
        "actions actions";
      gap: 0;
      align-items: start;
      padding: 18px 0 16px;
    }
    .rkp .k-ic { grid-area: ic; align-self: start; margin-top: 2px; }
    .rkp .k-body { grid-area: body; padding-left: 2px; min-width: 0; }
    .rkp .k-name { font-size: 15px; line-height: 1.25; }
    .rkp .k-sub {
      display: flex; flex-wrap: wrap; gap: 4px 6px;
      font-size: 12.5px; line-height: 1.45; margin-top: 4px;
      word-break: break-word;
    }
    .rkp .k-sub b { font-size: 11.5px; letter-spacing: .02em; }
    .rkp .k-tags { margin-top: 10px; gap: 7px; }
    .rkp .k-pill { font-size: 11.5px; padding: 5px 11px; }
    .rkp .k-mid {
      grid-area: mid;
      min-width: 0;
      flex: none;
      display: flex; align-items: baseline; gap: 8px;
      margin-top: 14px;
      background: rgba(255,255,255,.50);
      border: 1px solid rgba(26,26,46,.06);
      border-radius: 10px;
      padding: 10px 12px;
    }
    .rkp .k-mid .k-mid-label { font-size: 10.5px; white-space: nowrap; }
    .rkp .k-mid .k-mid-val { font-size: 13.5px; font-weight: 700; word-break: break-all; }
    .rkp .k-actions {
      grid-area: actions;
      width: 100%; min-width: 0;
      display: flex; align-items: center; justify-content: space-between; gap: 10px;
      margin-top: 14px; padding-top: 14px;
      border-top: 1px solid var(--divider);
    }
    .rkp .k-actions .k-pill.green { font-size: 12px; padding: 6px 14px; border-radius: 20px; }
    .rkp .k-actions .k-btn.coral-soft {
      flex: 0 0 auto;
      min-height: 42px; padding: 10px 18px;
      font-size: 13px; font-weight: 700; border-radius: 11px;
      justify-content: center;
    }
  }
</style>

<div class="rkp">
  <div class="k-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span>Rekap Siswa Diterima</span>
  </div>
  <h1 class="k-title">Rekap Siswa Diterima</h1>
  <p class="k-meta">Daftar siswa yang telah <b>diterima</b> dan <b>melengkapi daftar ulang</b>.</p>

  @if (session('success'))
    <div class="k-alert success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
  @endif
  @if (session('error'))
    <div class="k-alert error"><i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span></div>
  @endif

  {{-- Summary badge (no card) --}}
  <div class="k-summary">
    <span class="k-sum-ic"><i class="fa-solid fa-user-check"></i></span>
    <div>
      <div class="k-sum-lbl">Total Siswa Diterima</div>
      <div class="k-sum-val">{{ $registrations->total() }}</div>
    </div>
  </div>

  {{-- Tabs (per jurusan) + export actions di kanan --}}
  <div class="k-tabs-row">
    <div class="k-tabs">
      <a href="{{ route('admin.rekap.index', request()->only(['period_id','search'])) }}" class="k-tab doc-tab {{ !request('major_id') ? 'active' : '' }}">
        Semua <span class="badge">{{ $registrations->total() }}</span>
      </a>
      @foreach ($majors as $major)
        <a href="{{ route('admin.rekap.index', ['major_id' => $major->id] + request()->only(['period_id','search'])) }}" class="k-tab doc-tab {{ request('major_id') == $major->id ? 'active' : '' }}">
          {{ $major->name }} <span class="badge">{{ $statsPerMajor[$major->id] ?? 0 }}</span>
        </a>
      @endforeach
    </div>
    {{-- Mobile: dropdown picker pengganti tabs --}}
    <div class="k-tabs-mobile">
      <button type="button" class="r-pick" data-picker="majorTab" aria-haspopup="listbox" aria-expanded="false">
        <i class="fa-solid fa-layer-group" style="color:var(--muted);font-size:12px"></i>
        <span class="pick-label is-placeholder">Semua Jurusan</span>
        <span class="pick-clear" data-clear="majorTab" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
      </button>
      <input type="hidden" data-picker-input="majorTab" value="{{ request('major_id') }}">
    </div>
    <div class="k-tools">
      <button class="k-btn ghost sm" onclick="toggleFilterForm()"><i class="fa-solid fa-filter" style="font-size:10px"></i> Filter</button>
      <a href="{{ route('admin.rekap.export.xlsx', request()->only(['major_id','period_id','search'])) }}" class="k-btn green sm" title="Export Excel"><i class="fa-solid fa-file-excel" style="font-size:10px"></i> Export Excel</a>
      <a href="{{ route('admin.rekap.export.pdf', request()->only(['major_id','period_id','search'])) }}" class="k-btn red sm" title="Export PDF"><i class="fa-solid fa-file-pdf" style="font-size:10px"></i> Export PDF</a>
    </div>
  </div>

  {{-- Toolbar: search + filter toggle --}}
  <form id="filterForm" method="GET" action="{{ route('admin.rekap.index') }}" style="display:none;margin-bottom:16px;">
    <div class="k-toolbar">
      <div class="k-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NIS / NISN / No. Reg…">
      </div>
      <button type="button" class="k-fbtn" onclick="toggleFilterPanel()"><i class="fa-solid fa-sliders"></i> Periode</button>
      <button type="submit" class="k-gobtn"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
    </div>

    <div id="filterPanel" class="k-filters" style="display:{{ request('period_id') ? 'flex' : 'none' }}">
      <div class="k-field">
        <label>Periode</label>
        <button type="button" class="r-pick" data-picker="period" aria-haspopup="listbox" aria-expanded="false">
          <span class="pick-label is-placeholder">Pilih periode…</span>
          <span class="pick-clear" data-clear="period" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
          <i class="fa-solid fa-chevron-down pick-caret"></i>
        </button>
        <input type="hidden" name="period_id" data-picker-input="period" value="{{ request('period_id') }}">
      </div>
      <button type="submit" class="k-gobtn"><i class="fa-solid fa-check"></i> Terapkan</button>
    </div>
  </form>

  @if ($registrations->isEmpty())
    <div class="k-empty"><i class="fa-regular fa-folder-open"></i>Belum ada siswa yang diterima</div>
  @else
    <div class="k-list">
      @foreach ($registrations as $reg)
      <div class="k-row">
        <span class="k-ic"><i class="fa-solid fa-graduation-cap"></i></span>
        <div class="k-body">
          <div class="k-name">{{ $reg->applicant->full_name ?? '-' }}</div>
          <div class="k-sub">
            <span class="k-reg">{{ $reg->registration_number }}</span>
            @if ($reg->applicant->user?->email)<span class="k-dot">·</span><span class="k-email">{{ $reg->applicant->user->email }}</span>@endif
          </div>
          <div class="k-tags">
            <span class="k-pill coral"><i class="fa-solid fa-layer-group"></i> {{ $reg->finalMajor->name ?? '-' }}</span>
            <span class="k-pill blue">{{ $reg->registrationPeriod->name ?? '-' }}</span>
          </div>
        </div>
        <div class="k-mid">
          <div class="k-mid-label">NIS</div>
          <div class="k-mid-val">{{ $reg->applicant->student_number ?? '-' }}</div>
        </div>
        <div class="k-actions">
          <span class="k-pill green">{{ \App\Models\Registration::statusLabel($reg->status) }}</span>
          <a href="{{ route('admin.registrations.show', $reg) }}" class="k-btn coral-soft sm"><i class="fa-solid fa-eye"></i> Detail</a>
        </div>
      </div>
      @endforeach
    </div>

    <div class="k-pager">
      {{ $registrations->appends(request()->query())->links('vendor.pagination.bringova') }}
    </div>
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
  $pickPeriods = [['v' => '', 'l' => 'Semua Periode']];
  foreach ($periods as $period) {
    $pickPeriods[] = ['v' => $period->id, 'l' => $period->name];
  }
  $pickMajorsTab = [['v' => '', 'l' => 'Semua Jurusan']];
  foreach ($majors as $mj) {
    $pickMajorsTab[] = ['v' => (string) $mj->id, 'l' => $mj->name . ' (' . ($statsPerMajor[$mj->id] ?? 0) . ')'];
  }
  $pickerJson = ['period' => $pickPeriods, 'majorTab' => $pickMajorsTab];
  $pickerLabels = ['period' => 'Pilih Periode', 'majorTab' => 'Pilih Jurusan'];
@endphp

<div id="reg-data" hidden data-picker='@json($pickerJson)' data-picker-labels='@json($pickerLabels)'></div>

<script>
(function(){
  function goMajorTab(v){
    var params = new URLSearchParams(window.location.search);
    if(v) params.set('major_id', v); else params.delete('major_id');
    params.delete('page');
    // keep period_id & search if present
    var base = "{{ route('admin.rekap.index') }}";
    var q = params.toString();
    var url = base + (q ? '?' + q : '');
    if(window.loadContent){
      // AJAX partial
      window.loadContent(url);
      history.pushState({}, '', url);
    } else {
      window.location.href = url;
    }
    if(window.closePicker) window.closePicker();
  }
  function bindMajorTab(){
    var input = document.querySelector('[data-picker-input="majorTab"]');
    if(!input || input.__boundMajorTab) return;
    input.__boundMajorTab = true;
    var last = input.value;
    // observe change dari picker
    input.addEventListener('change', function(){
      if(this.value !== last){
        last = this.value;
        goMajorTab(this.value);
      }
    });
    // clear button (×) juga navigasi ke Semua
    var pick = document.querySelector('.r-pick[data-picker="majorTab"] .pick-clear');
    if(pick){
      pick.addEventListener('click', function(e){
        e.preventDefault(); e.stopPropagation();
        input.value = '';
        if(window.clearPicker) window.clearPicker('majorTab');
        goMajorTab('');
      });
    }
  }
  if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', bindMajorTab);
  else bindMajorTab();
  // re-bind after AJAX loadContent
  var origPickerInit = window.pickerInitAll;
  window.pickerInitAll = function(){
    if(origPickerInit) origPickerInit();
    setTimeout(bindMajorTab, 50);
  };
})();
</script>
</div>

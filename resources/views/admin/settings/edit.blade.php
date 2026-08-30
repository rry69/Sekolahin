@extends('layouts.dashboard')
@section('title', 'Pengaturan')
@php
    $errorTabs = [
        'pembayaran'    => ['bank_name', 'bank_account_number', 'bank_account_name', 'payment_note'],
        'biaya'         => ['fees', 'notes'],
        'batas-waktu'   => ['registration_deadline_hours', 'payment_deadline_hours'],
        'daftar-ulang'  => ['re_registration_start', 're_registration_end', 'rereg_notif_enabled', 'rereg_notif_title', 'rereg_notif_body', 'rereg_notif_cta', 'rereg_notif_h2'],
        'jenjang'       => ['age_min'],
    ];
    $activeTab = request()->query('tab');
    $__errBag = $errors ?? new \Illuminate\Support\ViewErrorBag;
    if (!array_key_exists($activeTab, $errorTabs)) {
        $activeTab = null;
        foreach ($errorTabs as $tabKey => $fields) {
            foreach ($fields as $field) {
                if ($__errBag->has($field) || $__errBag->has("{$field}.*")) { $activeTab = $tabKey; break 2; }
            }
        }
        $activeTab = $activeTab ?? 'pembayaran';
    }
@endphp
@section('content')
<style>
  .ste { --coral:#FF6B6B; --coral-soft:#FFE5E3; --coral-2:#FF8E6E; --amber:#F59E0B; --amber-soft:#FEF3C7; --green:#10B981; --green-soft:#D1FAE5; --blue:#3B82F6; --blue-soft:#DBEAFE; --purple:#8B5CF6; --purple-soft:#EDE9FE; --red:#EF4444; --red-soft:#FEE2E2; --gray:#6b7280; --gray-soft:#F3F4F6; --ink:#1a1a2e; --muted:#8a8f9d; --divider:rgba(26,26,46,0.10); position:relative; border-radius:24px; padding:28px 28px 40px; background:#f6f7fb; max-width:100%; overflow:hidden; box-sizing:border-box; }
  .ste .ste-crumb { display:flex; align-items:center; gap:8px; font-size:12.5px; color:var(--muted); margin-bottom:6px; font-weight:500; }
  .ste .ste-crumb a { color:var(--coral); text-decoration:none; }
  .ste .ste-crumb a:hover { text-decoration:underline; }
  .ste .ste-crumb .sep { color:#d3d6de; }
  .ste .ste-title { font-size:26px; font-weight:800; color:var(--ink); letter-spacing:-0.01em; margin-bottom:2px; }
  .ste .ste-meta { font-size:13px; color:var(--muted); margin-bottom:14px; }
  .ste .ste-alert { display:flex; align-items:flex-start; gap:10px; padding:12px 16px; border-radius:12px; font-size:13px; margin-bottom:16px; font-weight:500; }
  .ste .ste-alert i { margin-top:2px; }
  .ste .ste-alert.success { background:var(--green-soft); color:var(--green); }
  .ste .ste-alert.error { background:var(--red-soft); color:var(--red); }
  /* tabs — scrollable Bringova */
  .ste .ste-tabs { display:flex; gap:4px; flex-wrap:nowrap; overflow-x:auto; overflow-y:hidden; -webkit-overflow-scrolling:touch; scrollbar-width:none; border-bottom:1px solid var(--divider); margin-bottom:22px; }
  .ste .ste-tabs::-webkit-scrollbar{ display:none; }
  .ste .settings-tab { all:unset; flex:0 0 auto; display:inline-flex; align-items:center; gap:7px; padding:10px 14px 11px; font-size:13px; font-weight:600; color:var(--muted); border-bottom:2.5px solid transparent; margin-bottom:-1px; cursor:pointer; white-space:nowrap; transition:color .18s, border-color .18s; }
  .ste .settings-tab i{ font-size:12px; opacity:.9; }
  .ste .settings-tab:hover { color:var(--ink); }
  .ste .settings-tab.active { color:var(--coral); border-bottom-color:var(--coral); }
  /* section header with icon */
  .ste .ste-sec { padding:18px 0 6px; border-top:1px solid var(--divider); }
  .ste .ste-sec:first-of-type{ border-top:none; padding-top:4px; }
  .ste .ste-sec-head{ display:flex; align-items:center; gap:12px; margin-bottom:12px; }
  .ste .ste-sec-ic{ flex:0 0 auto; width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:16px; }
  .ste .ste-sec-ic.coral{ background:var(--coral-soft); color:var(--coral); }
  .ste .ste-sec-ic.blue{ background:var(--blue-soft); color:var(--blue); }
  .ste .ste-sec-ic.amber{ background:var(--amber-soft); color:#b45309; }
  .ste .ste-sec-ic.green{ background:var(--green-soft); color:var(--green); }
  .ste .ste-sec-ic.purple{ background:var(--purple-soft); color:var(--purple); }
  .ste .ste-sec-title { font-size:13px; font-weight:700; color:var(--ink); text-transform:uppercase; letter-spacing:.4px; margin:0; }
  .ste .ste-sec-desc { font-size:12.5px; color:var(--muted); margin:2px 0 0; line-height:1.5; }
  /* grid & field */
  .ste .ste-grid2 { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; }
  .ste .ste-field { display:flex; flex-direction:column; gap:6px; }
  .ste .ste-label { font-size:12px; font-weight:600; color:var(--ink); }
  .ste .ste-hint { font-size:11px; color:var(--muted); margin-top:2px; }
  .ste .ste-input-line { width:100%; padding:9px 4px; border:none; border-bottom:1px solid rgba(26,26,46,0.18); border-radius:0; font-size:13px; color:var(--ink); background:transparent; box-sizing:border-box; outline:none; -webkit-tap-highlight-color:transparent; transition:border-color .18s; }
  .ste .ste-input-line:focus { outline:none; box-shadow:none; border-bottom-color:var(--coral); }
  .ste .ste-input-line:focus-visible { outline:none; box-shadow:none; border-bottom-color:var(--coral); }
  .ste .ste-input-box { width:100%; padding:9px 12px; border:1px solid rgba(26,26,46,0.14); border-radius:10px; font-size:13px; color:var(--ink); background:rgba(255,255,255,0.55); box-sizing:border-box; outline:none; -webkit-tap-highlight-color:transparent; transition:border-color .18s, background .18s, box-shadow .18s; }
  .ste .ste-input-box:focus { outline:none; border-color:var(--coral); background:#fff; box-shadow:0 0 0 3px rgba(255,107,107,0.12); }
  /* jaga-jaga: hilangkan ring biru bawaan browser di semua input settings */
  .ste input[type="text"], .ste input[type="number"], .ste input[type="date"], .ste input[type="email"], .ste input[type="password"] { outline:none; -webkit-tap-highlight-color:transparent; }
  .ste input[type="text"]:focus, .ste input[type="number"]:focus, .ste input[type="date"]:focus, .ste input[type="email"]:focus, .ste input[type="password"]:focus { outline:none; box-shadow:none; }
  .ste input[type="text"]:focus-visible, .ste input[type="number"]:focus-visible, .ste input[type="date"]:focus-visible, .ste input[type="email"]:focus-visible, .ste input[type="password"]:focus-visible { outline:none; box-shadow:none; }
  /* biaya — card grid per jenjang */
  .ste .ste-biaya-grid{ display:grid; grid-template-columns:repeat(2,1fr); gap:14px; margin-bottom:18px; }
  .ste .ste-biaya-card{ background:rgba(255,255,255,.55); border:1px solid rgba(26,26,46,.08); border-radius:14px; padding:14px 14px 12px; }
  .ste .ste-biaya-card-head{ display:flex; align-items:center; gap:10px; margin-bottom:10px; }
  .ste .ste-biaya-ic{ width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:14px; background:var(--purple-soft); color:var(--purple); flex:0 0 auto; }
  .ste .ste-biaya-name{ font-size:13px; font-weight:800; color:var(--ink); }
  .ste .ste-biaya-desc{ font-size:11.5px; color:var(--muted); }
  .ste .ste-track-row{ display:flex; align-items:center; justify-content:space-between; gap:10px; padding:8px 0; border-top:1px solid var(--divider); }
  .ste .ste-track-row:first-of-type{ border-top:none; }
  .ste .ste-track-label{ font-size:12px; font-weight:600; color:var(--ink); display:flex; align-items:center; gap:6px; }
  .ste .ste-track-pill{ font-size:10px; font-weight:700; padding:2px 7px; border-radius:20px; background: transparent; border: 1px solid currentColor; color:var(--gray); }
  .ste .ste-track-pill.coral{ background: transparent; border: 1px solid currentColor; color:var(--coral); }
  .ste .ste-track-input{ width:140px; }
  .ste .ste-track-manual{ font-size:11px; color:var(--muted); font-style:italic; text-align:right; line-height:1.3; }
  /* notes per jalur */
  .ste .ste-notes{ display:flex; flex-direction:column; gap:10px; }
  .ste .ste-note-card{ display:flex; gap:12px; padding:12px 0; border-bottom:1px solid var(--divider); }
  .ste .ste-note-card:last-child{ border-bottom:none; }
  .ste .ste-note-ic{ width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:13px; flex:0 0 auto; }
  /* batas waktu — stat cards */
  .ste .ste-deadline-grid{ display:grid; grid-template-columns:repeat(2,1fr); gap:14px; }
  .ste .ste-dl-card{ background:rgba(255,255,255,.55); border:1px solid rgba(26,26,46,.08); border-radius:14px; padding:16px; }
  .ste .ste-dl-head{ display:flex; align-items:center; gap:10px; margin-bottom:10px; }
  .ste .ste-dl-ic{ width:38px; height:38px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:15px; }
  .ste .ste-dl-ic.amber{ background:var(--amber-soft); color:#b45309; }
  .ste .ste-dl-ic.blue{ background:var(--blue-soft); color:var(--blue); }
  /* daftar ulang — list cards */
  .ste .ste-rereg-list{ display:flex; flex-direction:column; }
  .ste .ste-rereg-card{ display:flex; gap:14px; padding:14px 4px; border-bottom:1px solid var(--divider); align-items:flex-start; }
  .ste .ste-rereg-card:last-child{ border-bottom:none; }
  .ste .ste-rereg-ic{ width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:15px; background:var(--green-soft); color:var(--green); flex:0 0 auto; }
  .ste .ste-rereg-body{ flex:1; min-width:0; }
  .ste .ste-rereg-name{ font-size:13px; font-weight:800; color:var(--ink); }
  .ste .ste-rereg-sub{ font-size:11.5px; color:var(--muted); margin-top:2px; }
  .ste .ste-rereg-dates{ display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:10px; }
  /* jenjang — age grid */
  .ste .ste-age-grid{ display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
  .ste .ste-age-card{ background:rgba(255,255,255,.55); border:1px solid rgba(26,26,46,.08); border-radius:14px; padding:14px; text-align:center; }
  .ste .ste-age-name{ font-size:12.5px; font-weight:800; color:var(--ink); }
  .ste .ste-age-desc{ font-size:11px; color:var(--muted); margin-bottom:8px; }
  .ste .ste-age-input{ text-align:center; font-weight:700; font-size:18px; }
  /* toggle switch Bringova */
  .ste .ste-toggle{ position:relative; width:44px; height:26px; flex:0 0 auto; }
  .ste .ste-toggle input{ opacity:0; width:0; height:0; }
  .ste .ste-toggle-slider{ position:absolute; inset:0; background:var(--gray-soft); border-radius:20px; cursor:pointer; transition:.2s; border:1px solid rgba(26,26,46,.08); }
  .ste .ste-toggle-slider::before{ content:""; position:absolute; width:20px; height:20px; left:2px; top:2px; background:#fff; border-radius:50%; box-shadow:0 1px 4px rgba(0,0,0,.15); transition:.2s; }
  .ste .ste-toggle input:checked + .ste-toggle-slider{ background:var(--green); border-color:var(--green); }
  .ste .ste-toggle input:checked + .ste-toggle-slider::before{ transform:translateX(18px); }
  .ste .ste-level-row { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 4px; border-bottom:1px solid var(--divider); }
  .ste .ste-level-row:last-child { border-bottom:none; }
  /* foot per tab */
  .ste .ste-foot{ display:flex; justify-content:flex-end; gap:10px; margin-top:18px; padding-top:16px; border-top:1px solid var(--divider); }
  /* modal */
  .ste .ste-modal-backdrop { position:fixed; inset:0; z-index:90; background:rgba(26,26,46,0.36); backdrop-filter:blur(3px); display:none; align-items:center; justify-content:center; padding:16px; }
  .ste .ste-modal-backdrop.is-open { display:flex; }
  .ste .ste-modal { width:100%; max-width:420px; background:#fff; border-radius:18px; padding:22px; box-shadow:0 24px 60px -18px rgba(26,26,46,0.4); animation:stePop .2s cubic-bezier(.22,1.2,.36,1); }
  @keyframes stePop { from{opacity:0; transform:scale(0.97) translateY(4px)} to{opacity:1; transform:scale(1) translateY(0)} }
  .ste .ste-modal h3 { font-size:15px; font-weight:700; color:var(--ink); margin-bottom:8px; }
  .ste .ste-modal p { font-size:13px; color:var(--muted); margin-bottom:16px; line-height:1.5; }
  .ste .ste-modal-foot { display:flex; justify-content:flex-end; gap:8px; }
  .ste .ste-btn { display:inline-flex; align-items:center; gap:6px; border:none; cursor:pointer; border-radius:11px; padding:10px 18px; font-size:13px; font-weight:700; text-decoration:none; transition:transform .15s, filter .15s; }
  .ste .ste-btn:hover { transform:translateY(-1px); }
  .ste .ste-btn.coral { background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; box-shadow:0 8px 18px -8px rgba(255,107,107,0.6); }
  .ste .ste-btn.coral:hover { filter:brightness(1.04); }
  .ste .ste-btn.ghost { background:rgba(255,255,255,0.6); color:var(--ink); }
  .ste .ste-btn.ghost:hover { background:#fff; color:var(--coral); }
  .ste .ste-btn.sm{ padding:7px 14px; font-size:12.5px; border-radius:9px; }
  .ste .ste-tabs-mobile{ display:none; }
  .ste .ste-tabs-mobile .r-pick{ display:flex; align-items:center; gap:10px; width:100%; background:#fff; border:1px solid rgba(26,26,46,.12); border-radius:12px; padding:11px 14px; font:700 13px var(--ink); color:var(--ink); cursor:pointer; text-align:left; box-shadow:0 2px 10px -8px rgba(26,26,46,.18); transition:border-color .18s; }
  .ste .ste-tabs-mobile .r-pick:hover{ border-color:var(--coral); }
  .ste .ste-tabs-mobile .r-pick .pick-label{ flex:1 1 auto; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .ste .ste-tabs-mobile .r-pick .pick-caret{ flex:0 0 auto; color:var(--muted); font-size:12px; transition:transform .15s; }
  .ste .ste-tabs-mobile .r-pick[aria-expanded="true"] .pick-caret{ transform:rotate(180deg); }
  /* picker modal for tab selection */
  .ste .ste-picker-backdrop{ position:fixed; inset:0; z-index:85; background:rgba(26,26,46,.32); backdrop-filter:blur(4px); -webkit-backdrop-filter:blur(4px); display:none; align-items:flex-start; justify-content:center; padding:80px 16px 16px; }
  .ste .ste-picker-backdrop.is-open{ display:flex; }
  .ste .ste-picker-panel{ width:100%; max-width:380px; max-height:min(520px, calc(100vh - 120px)); display:flex; flex-direction:column; background:#fff; border-radius:18px; box-shadow:0 20px 50px -16px rgba(26,26,46,.35), 0 0 0 1px rgba(26,26,46,.06); overflow:hidden; animation:stePop .22s cubic-bezier(.22,1.2,.36,1); }
  .ste .ste-picker-head{ display:flex; align-items:center; gap:10px; padding:14px 16px; border-bottom:1px solid var(--divider); }
  .ste .ste-picker-title{ font-size:14px; font-weight:700; color:var(--ink); flex:1; }
  .ste .ste-picker-close{ display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:8px; border:none; background:transparent; color:var(--muted); cursor:pointer; font-size:12px; }
  .ste .ste-picker-close:hover{ background:var(--gray-soft); color:var(--ink); }
  .ste .ste-picker-list{ flex:1; overflow-y:auto; padding:6px 8px; }
  .ste .ste-picker-item{ display:flex; align-items:center; gap:10px; padding:11px 12px; border-radius:10px; font-size:13px; color:var(--ink); cursor:pointer; user-select:none; transition:background .15s, color .15s; }
  .ste .ste-picker-item:hover{ background:var(--coral-soft); color:var(--coral); }
  .ste .ste-picker-item.is-active{ background:var(--coral); color:#fff; font-weight:700; }
  .ste .ste-picker-item.is-active i{ color:#fff; }
  .ste .ste-picker-foot{ display:flex; justify-content:flex-end; padding:10px 14px; border-top:1px solid var(--divider); background:rgba(255,255,255,.5); }
  .ste .ste-picker-foot .ste-btn{ padding:7px 16px; font-size:12px; }
  /* responsive */
  @media (max-width:1024px){
    .ste .ste-biaya-grid{ grid-template-columns:1fr 1fr; }
    .ste .ste-age-grid{ grid-template-columns:repeat(2,1fr); }
    .ste .ste-rereg-dates{ grid-template-columns:1fr 1fr; }
  }
  /* Bringova override for date-picker inside Daftar Ulang */
  .ste [data-datepicker-trigger]{ border-bottom-color:rgba(26,26,46,.18) !important; background:transparent !important; }
  .ste [data-datepicker-trigger]:hover{ border-bottom-color:var(--coral) !important; }
  .ste [data-datepicker-display]{ color:var(--ink) !important; }
  @media (max-width:640px){
    .ste{ padding:18px 14px 28px; }
    .ste .ste-crumb{ margin-top:8px; padding-left:48px; box-sizing:border-box; }
    .ste .ste-title{ font-size:22px; }
    .ste .ste-tabs{ display:none !important; }
    .ste .ste-tabs-mobile{ display:block; margin-bottom:18px; }
    .ste .ste-grid2{ grid-template-columns:1fr; }
    .ste .ste-biaya-grid{ grid-template-columns:1fr; }
    .ste .ste-deadline-grid{ grid-template-columns:1fr; }
    .ste .ste-rereg-list{ gap:12px; }
    .ste .ste-rereg-card{ flex-direction:row; align-items:flex-start; gap:12px; background:rgba(255,255,255,.62); border:1px solid rgba(26,26,46,.08); border-radius:14px; padding:14px 14px 12px; border-bottom:1px solid rgba(26,26,46,.08); }
    .ste .ste-rereg-ic{ width:38px; height:38px; border-radius:11px; font-size:14px; }
    .ste .ste-rereg-name{ font-size:13px; line-height:1.3; }
    .ste .ste-rereg-sub{ font-size:11px; margin-top:4px; line-height:1.4; }
    .ste .ste-rereg-dates{ grid-template-columns:1fr 1fr; gap:10px; }
    .ste .ste-rereg-dates .ste-field{ gap:5px; }
    .ste .ste-rereg-dates .ste-label{ font-size:11px; }
    .ste .ste-rereg-dates [data-datepicker-trigger]{ height:40px; }
    .ste .ste-age-grid{ grid-template-columns:repeat(2,1fr); gap:10px; }
    .ste .ste-note-card{ flex-direction:column; }
    .ste .ste-foot{ justify-content:stretch; }
    .ste .ste-foot .ste-btn{ width:100%; justify-content:center; min-height:44px; }
  }
  @media (max-width:380px){
    .ste .ste-rereg-card{ flex-direction:column; }
    .ste .ste-rereg-ic{ width:36px; height:36px; }
    .ste .ste-rereg-dates{ grid-template-columns:1fr; gap:12px; }
  }
  @media (max-width:360px){ .ste .ste-age-grid{ grid-template-columns:1fr; } }
</style>

<div class="ste">
  <div class="ste-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span>Pengaturan</span>
  </div>
  <h1 class="ste-title">Pengaturan</h1>
  <p class="ste-meta">Kelola konfigurasi sistem SPMB — pembayaran, biaya, batas waktu, daftar ulang, dan jenjang.</p>

  @if (session('success'))
    <div class="ste-alert success"><x-hi name="checkmark-circle-02" /><span>{{ session('success') }}</span></div>
  @endif
  @if (($__errBag ?? $errors ?? new \Illuminate\Support\ViewErrorBag)->any())
    <div class="ste-alert error"><x-hi name="alert-02" /><span>Ada {{ ($__errBag ?? $errors ?? new \Illuminate\Support\ViewErrorBag)->count() }} kesalahan validasi — tab yang bermasalah sudah dibuka otomatis.</span></div>
  @endif

  <div class="ste-tabs" id="settings-tabs">
    <button type="button" data-tab-btn="pembayaran" class="settings-tab"><x-hi name="credit-card" /> Pembayaran</button>
    <button type="button" data-tab-btn="biaya" class="settings-tab"><x-hi name="coins-01" /> Biaya &amp; Jalur</button>
    <button type="button" data-tab-btn="batas-waktu" class="settings-tab"><x-hi name="clock-01" /> Batas Waktu</button>
    <button type="button" data-tab-btn="daftar-ulang" class="settings-tab"><x-hi name="calendar-check-in-01" /> Daftar Ulang</button>
    <button type="button" data-tab-btn="jenjang" class="settings-tab"><x-hi name="layers-01" /> Jenjang</button>
  </div>
  {{-- Mobile: dropdown picker menggantikan tabs --}}
  <div class="ste-tabs-mobile" id="steTabsMobile">
    <button type="button" class="r-pick" id="steTabPickBtn" aria-haspopup="listbox" aria-expanded="false">
      <span class="pick-label" id="steTabPickLabel">Pilih Halaman</span>
      <span class="pick-caret"><x-hi name="arrow-down-01" /></span>
    </button>
  </div>
  <div id="stePickerBackdrop" class="ste-picker-backdrop" aria-hidden="true">
    <div class="ste-picker-panel" role="dialog" aria-modal="true" aria-labelledby="stePickerTitle">
      <div class="ste-picker-head">
        <div class="ste-picker-title" id="stePickerTitle">Pilih Halaman Pengaturan</div>
        <button type="button" class="ste-picker-close" onclick="closeStePicker()" aria-label="Tutup"><x-hi name="cancel-01" /></button>
      </div>
      <div class="ste-picker-list" id="stePickerList" role="listbox"></div>
      <div class="ste-picker-foot">
        <button type="button" class="ste-btn coral sm" onclick="closeStePicker()">Selesai</button>
      </div>
    </div>
  </div>

  <form action="{{ route('admin.settings.update') }}" method="POST" id="steMainForm">
    @csrf

    <!-- ================= TAB: PEMBAYARAN ================= -->
    <div data-tab-panel="pembayaran" class="{{ $activeTab === 'pembayaran' ? '' : 'hidden' }}">
      <div class="ste-sec-head">
        <span class="ste-sec-ic coral"><x-hi name="bank" /></span>
        <div>
          <h4 class="ste-sec-title">Rekening Pembayaran</h4>
          <p class="ste-sec-desc">Rekening manual yang ditampilkan kepada siswa saat instruksi pembayaran.</p>
        </div>
      </div>
      <div class="ste-grid2" style="margin-bottom:16px;">
        <div class="ste-field">
          <label class="ste-label">Nama Bank</label>
          <input type="text" name="bank_name" value="{{ old('bank_name', App\Models\Setting::get('bank_name', 'BCA')) }}" required class="ste-input-line" placeholder="BCA">
          @error('bank_name')<p style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
        </div>
        <div class="ste-field">
          <label class="ste-label">Nomor Rekening</label>
          <input type="text" name="bank_account_number" id="bank_account_number" inputmode="numeric" pattern="\d{6,30}" value="{{ old('bank_account_number', App\Models\Setting::get('bank_account_number')) }}" required class="ste-input-line" placeholder="1234567890">
          @error('bank_account_number')<p style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
        </div>
      </div>
      <div class="ste-field" style="margin-bottom:16px;">
        <label class="ste-label">Atas Nama</label>
        <input type="text" name="bank_account_name" value="{{ old('bank_account_name', App\Models\Setting::get('bank_account_name')) }}" required class="ste-input-line" placeholder="Yayasan Sekolahin">
        @error('bank_account_name')<p style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
      </div>
      <div class="ste-field">
        <label class="ste-label">Catatan Pembayaran</label>
        <textarea name="payment_note" rows="3" class="ste-input-box" placeholder="Transfer sesuai nominal tertera, konfirmasi via dashboard...">{{ old('payment_note', App\Models\Setting::get('payment_note')) }}</textarea>
        @error('payment_note')<p style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
        <span class="ste-hint">Tampil di halaman instruksi pembayaran siswa.</span>
      </div>
      <div class="ste-foot">
        <button type="button" class="ste-btn coral btn-save-tab" data-save-msg="Simpan rekening pembayaran?"><x-hi name="save" /> Simpan Pembayaran</button>
      </div>
    </div>

    <!-- ================= TAB: BIAYA & JALUR ================= -->
    <div data-tab-panel="biaya" class="{{ $activeTab === 'biaya' ? '' : 'hidden' }}">
      <div class="ste-sec-head">
        <span class="ste-sec-ic purple"><x-hi name="coins-01" /></span>
        <div>
          <h4 class="ste-sec-title">Biaya Pendaftaran per Jenjang</h4>
          <p class="ste-sec-desc">Biaya <strong>Reguler</strong> diatur di sini. <strong>Prestasi &amp; Beasiswa</strong> diinput manual panitia setelah verifikasi.</p>
        </div>
      </div>
      <div class="ste-biaya-grid">
        @foreach($levels as $level)
          <div class="ste-biaya-card">
            <div class="ste-biaya-card-head">
              <span class="ste-biaya-ic"><x-hi name="mortarboard-01" /></span>
              <div>
                <div class="ste-biaya-name">{{ $level->name }}</div>
                <div class="ste-biaya-desc">{{ $level->description }}</div>
              </div>
            </div>
            @foreach($tracks as $track)
              @php $feeKey = "fee_{$level->id}_{$track->id}"; $isReguler = strtolower($track->name) === 'reguler'; @endphp
              <div class="ste-track-row">
                <span class="ste-track-label">{{ $track->name }} @if($isReguler)<span class="ste-track-pill coral">Reguler</span>@else<span class="ste-track-pill">Manual</span>@endif</span>
                @if($isReguler)
                  <input type="number" min="0" max="1000000000" step="1000" name="fees[{{ $level->id }}][{{ $track->id }}]" value="{{ App\Models\Setting::get($feeKey) }}" class="ste-input-line ste-track-input" style="text-align:right;" placeholder="500000">
                @else
                  <span class="ste-track-manual">Input manual<br>setelah verifikasi</span>
                @endif
              </div>
            @endforeach
          </div>
        @endforeach
      </div>

      <div class="ste-sec" style="margin-top:6px;">
        <div class="ste-sec-head">
          <span class="ste-sec-ic blue" style="width:38px;height:38px;font-size:14px;"><x-hi name="sticky-note-01" /></span>
          <div>
            <h4 class="ste-sec-title">Keterangan Biaya per Jalur</h4>
            <p class="ste-sec-desc">Penjelasan tampil di form pendaftaran siswa — apa saja yang dibayarkan.</p>
          </div>
        </div>
        <div class="ste-notes">
          @foreach($tracks as $track)
            <div class="ste-note-card">
              <span class="ste-note-ic" style="background:transparent; @if(strtolower($track->name)==='reguler') color:var(--coral) @elseif(strtolower($track->name)==='prestasi') color:#b45309 @else color:var(--green) @endif"><x-hi :name="strtolower($track->name)==='reguler' ? 'ticket-01' : (strtolower($track->name)==='prestasi' ? 'award-01' : 'hand-helping')" style="width:26px;height:26px;" /></span>
              <div class="ste-field" style="flex:1;">
                <label class="ste-label">{{ $track->name }}</label>
                <textarea name="notes[{{ $track->id }}]" rows="2" placeholder="Apa saja yang dibayarkan pada jalur {{ $track->name }}" class="ste-input-box">{{ App\Models\Setting::get('note_' . $track->id) }}</textarea>
                @error('notes.' . $track->id)<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror
              </div>
            </div>
          @endforeach
        </div>
      </div>
      <div class="ste-foot">
        <button type="button" class="ste-btn coral btn-save-tab" data-save-msg="Simpan biaya pendaftaran?"><x-hi name="save" /> Simpan Biaya</button>
      </div>
    </div>

    <!-- ================= TAB: BATAS WAKTU ================= -->
    <div data-tab-panel="batas-waktu" class="{{ $activeTab === 'batas-waktu' ? '' : 'hidden' }}">
      <div class="ste-sec-head">
        <span class="ste-sec-ic amber"><x-hi name="clock-01" /></span>
        <div>
          <h4 class="ste-sec-title">Batas Waktu Pendaftaran &amp; Pembayaran</h4>
          <p class="ste-sec-desc">Jika melebihi batas, status otomatis “Dibatalkan” dan kuota dibuka kembali.</p>
        </div>
      </div>
      <div class="ste-deadline-grid">
        <div class="ste-dl-card">
          <div class="ste-dl-head">
            <span class="ste-dl-ic amber"><x-hi name="file-01" /></span>
            <div>
              <div style="font-size:13px;font-weight:800;color:var(--ink);">Upload Berkas</div>
              <div style="font-size:11.5px;color:var(--muted);">Batas upload dokumen</div>
            </div>
          </div>
          <div class="ste-field">
            <label class="ste-label">Durasi (jam)</label>
            <input type="number" min="1" max="720" name="registration_deadline_hours" value="{{ old('registration_deadline_hours', App\Models\Setting::get('registration_deadline_hours', '72')) }}" class="ste-input-line" placeholder="72" style="font-size:18px;font-weight:800;text-align:center;">
            @error('registration_deadline_hours')<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror
            <span class="ste-hint" style="text-align:center;">Default: 72 jam (3 hari)</span>
          </div>
        </div>
        <div class="ste-dl-card">
          <div class="ste-dl-head">
            <span class="ste-dl-ic blue"><x-hi name="money-02" /></span>
            <div>
              <div style="font-size:13px;font-weight:800;color:var(--ink);">Pembayaran</div>
              <div style="font-size:11.5px;color:var(--muted);">Batas konfirmasi bayar</div>
            </div>
          </div>
          <div class="ste-field">
            <label class="ste-label">Durasi (jam)</label>
            <input type="number" min="1" max="720" name="payment_deadline_hours" value="{{ old('payment_deadline_hours', App\Models\Setting::get('payment_deadline_hours', '72')) }}" class="ste-input-line" placeholder="72" style="font-size:18px;font-weight:800;text-align:center;">
            @error('payment_deadline_hours')<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror
            <span class="ste-hint" style="text-align:center;">Default: 72 jam (3 hari)</span>
          </div>
        </div>
      </div>
      <input type="hidden" name="re_registration_type" value="offline">
      <div class="ste-foot">
        <button type="button" class="ste-btn coral btn-save-tab" data-save-msg="Simpan batas waktu?"><x-hi name="save" /> Simpan Batas Waktu</button>
      </div>
    </div>

    <!-- ================= TAB: DAFTAR ULANG ================= -->
    <div data-tab-panel="daftar-ulang" class="{{ $activeTab === 'daftar-ulang' ? '' : 'hidden' }}">
      <div class="ste-sec-head">
        <span class="ste-sec-ic green"><x-hi name="calendar-check-in-01" /></span>
        <div>
          <h4 class="ste-sec-title">Jadwal Daftar Ulang per Jenjang</h4>
          <p class="ste-sec-desc">Offline di sekolah — wajib setelah periode pendaftaran jenjang berakhir.</p>
        </div>
      </div>
      <div class="ste-rereg-list">
        @foreach($levels as $level)
          @php
            $sKey = "re_registration_start_{$level->id}"; $eKey = "re_registration_end_{$level->id}";
            $sVal = old("re_registration_start.{$level->id}", App\Models\Setting::get($sKey, App\Models\Setting::get('re_registration_start')));
            $eVal = old("re_registration_end.{$level->id}", App\Models\Setting::get($eKey, App\Models\Setting::get('re_registration_end')));
            $reRegMin = $reRegMinByLevel[$level->id] ?? null;
            $periodEndLabel = $periodEndByLevel[$level->id] ?? null;
          @endphp
          <div class="ste-rereg-card">
            <span class="ste-rereg-ic"><x-hi name="school" /></span>
            <div class="ste-rereg-body">
              <div class="ste-rereg-name">{{ $level->name }} <span style="font-weight:500;color:var(--muted);font-size:11.5px;">— {{ $level->description }}</span></div>
              @if($periodEndLabel)<div class="ste-rereg-sub"><x-hi name="clock-01" style="font-size:10px;" /> Periode berakhir {{ $periodEndLabel }} @if($reRegMin)· paling awal {{ $reRegMin }}@endif</div>@elseif($reRegMin)<div class="ste-rereg-sub">Paling awal {{ $reRegMin }}</div>@endif
              <div class="ste-rereg-dates">
                <div class="ste-field">
                  <label class="ste-label">Mulai</label>
                  <x-date-picker name="re_registration_start[{{ $level->id }}]" id="re_reg_start_{{ $level->id }}" :value="$sVal" :min="$reRegMin" label="Mulai" />
                  @error("re_registration_start.{$level->id}")<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror
                </div>
                <div class="ste-field">
                  <label class="ste-label">Selesai</label>
                  <x-date-picker name="re_registration_end[{{ $level->id }}]" id="re_reg_end_{{ $level->id }}" :value="$eVal" :min="$reRegMin" label="Selesai" />
                  @error("re_registration_end.{$level->id}")<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
      <p class="ste-hint" style="margin-top:8px;">Kosongkan tanggal = tanpa batas. Jika tidak diatur, fallback ke pengaturan lama.</p>

      <div class="ste-sec" style="margin-top:10px;">
        <div class="ste-sec-head">
          <span class="ste-sec-ic blue" style="width:38px;height:38px;"><x-hi name="notification-01" /></span>
          <div>
            <h4 class="ste-sec-title">Notifikasi Daftar Ulang</h4>
            <p class="ste-sec-desc">Pengingat di dashboard siswa diterima. Dukung <code style="background:rgba(255,255,255,0.6);padding:1px 4px;border-radius:4px;">{tanggal}</code> & <code style="background:rgba(255,255,255,0.6);padding:1px 4px;border-radius:4px;">{tanggal_selesai}</code>.</p>
          </div>
        </div>
        <div style="display:flex;flex-direction:column;gap:14px;">
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;font-weight:600;color:var(--ink); background:rgba(255,255,255,.5); border:1px solid rgba(26,26,46,.08); border-radius:12px; padding:10px 14px;"><input type="checkbox" name="rereg_notif_enabled" id="rereg_notif_enabled" value="1" {{ old('rereg_notif_enabled', App\Models\Setting::get('rereg_notif_enabled')) ? 'checked' : '' }} style="accent-color:var(--coral); width:18px; height:18px;"> Aktifkan notifikasi daftar ulang untuk siswa</label>
          <div class="ste-field">
            <label class="ste-label">Judul Notifikasi</label>
            <input type="text" name="rereg_notif_title" value="{{ old('rereg_notif_title', App\Models\Setting::get('rereg_notif_title', 'Daftar Ulang Segera Dimulai')) }}" maxlength="80" class="ste-input-line" placeholder="Daftar Ulang Segera Dimulai">
            @error('rereg_notif_title')<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror
          </div>
          <div class="ste-field">
            <label class="ste-label">Isi Notifikasi</label>
            <textarea name="rereg_notif_body" rows="3" class="ste-input-box" placeholder="Halo! Kamu sudah diterima...">{{ old('rereg_notif_body', App\Models\Setting::get('rereg_notif_body', 'Halo! Kabar baik — kamu sudah diterima sebagai calon siswa. Daftar ulang akan dibuka pada {tanggal} dan berlangsung hingga {tanggal_selesai}, jadi persiapkan berkas asli dan diri kamu untuk hadir ke sekolah.')) }}</textarea>
            @error('rereg_notif_body')<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror
            <span class="ste-hint">Maks 3–4 kalimat. Gunakan {tanggal} dan {tanggal_selesai}.</span>
          </div>
          <div class="ste-grid2">
            <div class="ste-field">
              <label class="ste-label">Teks Tombol (CTA)</label>
              <input type="text" name="rereg_notif_cta" value="{{ old('rereg_notif_cta', App\Models\Setting::get('rereg_notif_cta', 'Lihat Detail Pendaftaran')) }}" maxlength="60" class="ste-input-line" placeholder="Lihat Detail Pendaftaran">
              @error('rereg_notif_cta')<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror
            </div>
            <div class="ste-field">
              <label class="ste-label">Maju Berapa Hari (H-?)</label>
              <input type="number" name="rereg_notif_h2" min="1" max="14" value="{{ old('rereg_notif_h2', App\Models\Setting::get('rereg_notif_h2', '2')) }}" class="ste-input-line" placeholder="2">
              @error('rereg_notif_h2')<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror
              <span class="ste-hint">Mulai tampil H-<span id="rereg_notif_h2_label">2</span> sebelum tanggal mulai.</span>
            </div>
          </div>
        </div>
      </div>
      <div class="ste-foot">
        <button type="button" class="ste-btn coral btn-save-tab" data-save-msg="Simpan jadwal daftar ulang?"><x-hi name="save" /> Simpan Daftar Ulang</button>
      </div>
    </div>

    <!-- ================= TAB: JENJANG ================= -->
    <div data-tab-panel="jenjang" class="{{ $activeTab === 'jenjang' ? '' : 'hidden' }}">
      <div class="ste-sec-head">
        <span class="ste-sec-ic purple"><x-hi name="layers-01" /></span>
        <div>
          <h4 class="ste-sec-title">Batas Usia Minimal per Jenjang</h4>
          <p class="ste-sec-desc">Kosongkan untuk menonaktifkan batas. Rekomendasi: TK 4, SD 6, SMP 12, SMA/SMK 15</p>
        </div>
      </div>
      <div class="ste-age-grid">
        @foreach($levels as $level)
          @php $key = "age_min_{$level->id}"; $val = old("age_min.{$level->id}", App\Models\Setting::get($key)); @endphp
          <div class="ste-age-card">
            <div class="ste-age-name">{{ $level->name }}</div>
            <div class="ste-age-desc">{{ $level->description }}</div>
            <input type="number" min="0" max="30" name="age_min[{{ $level->id }}]" value="{{ $val }}" placeholder="—" class="ste-input-line ste-age-input" style="border-bottom-style:dashed;">
            @error("age_min.{$level->id}")<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror
            <span class="ste-hint">tahun</span>
          </div>
        @endforeach
      </div>

      <div class="ste-sec" style="margin-top:10px;">
        <div class="ste-sec-head">
          <span class="ste-sec-ic green"><x-hi name="toggle-on" /></span>
          <div>
            <h4 class="ste-sec-title">Status Pendaftaran per Jenjang</h4>
            <p class="ste-sec-desc">Nonaktif = tidak muncul di form siswa.</p>
          </div>
        </div>
        <div style="display:flex;flex-direction:column;">
          @foreach($levels as $level)
            <div class="ste-level-row">
              <div style="display:flex; gap:12px; align-items:center;">
                <span style="width:38px;height:38px;border-radius:11px; display:flex;align-items:center;justify-content:center; font-size:14px; background:transparent; @if($level->is_active) color:var(--green) @else color:var(--red) @endif"><x-hi :name="$level->is_active ? 'checkmark-circle-02' : 'cancel-circle'" style="width:26px;height:26px;" /></span>
                <div>
                  <p style="font-weight:700;color:var(--ink);font-size:13px; margin:0;">{{ $level->name }}</p>
                  <p style="font-size:12px;color:var(--muted); margin:0;">{{ $level->description }}</p>
                </div>
              </div>
              <label class="ste-toggle" title="{{ $level->is_active ? 'Aktif' : 'Nonaktif' }}">
                <input type="checkbox" name="is_active[{{ $level->id }}]" value="1" {{ $level->is_active ? 'checked' : '' }}>
                <span class="ste-toggle-slider"></span>
              </label>
            </div>
          @endforeach
        </div>
        <p class="ste-hint" style="margin-top:8px;">Matikan jenjang yang tidak menerima pendaftaran.</p>
      </div>
      <div class="ste-foot" style="flex-wrap:wrap;">
        <button type="button" id="btn-save-levels" class="ste-btn ghost" style="border:1px solid var(--divider);"><x-hi name="toggle-on" /> Simpan Status Jenjang Saja</button>
        <button type="button" class="ste-btn coral btn-save-tab" data-save-msg="Simpan pengaturan jenjang &amp; batas usia?"><x-hi name="save" /> Simpan Jenjang</button>
      </div>
    </div>

  </form>

  {{-- Confirm modal --}}
  <div id="steConfirmModal" class="ste-modal-backdrop" aria-hidden="true">
    <div class="ste-modal" role="dialog" aria-modal="true">
      <h3 id="steConfirmTitle"></h3>
      <p id="steConfirmMsg"></p>
      <div class="ste-modal-foot">
        <button type="button" class="ste-btn ghost sm" onclick="closeSteConfirm()">Batal</button>
        <button type="button" class="ste-btn coral sm" id="steConfirmAction">Ya, Simpan</button>
      </div>
    </div>
  </div>
</div>

<script>
    var tabsRoot = document.getElementById('settings-tabs');
    var tabButtons = tabsRoot.querySelectorAll('[data-tab-btn]');
    var tabPanels = document.querySelectorAll('[data-tab-panel]');
    var steTabItems = [
        {v:'pembayaran', l:'Pembayaran', i:'credit-card'},
        {v:'biaya', l:'Biaya & Jalur', i:'coins-01'},
        {v:'batas-waktu', l:'Batas Waktu', i:'clock-01'},
        {v:'daftar-ulang', l:'Daftar Ulang', i:'calendar-check-in-01'},
        {v:'jenjang', l:'Jenjang', i:'layers-01'}
    ];
    var steActiveTab = '{{ $activeTab }}';
    function syncStePickerLabel(key){
        var el = document.getElementById('steTabPickLabel');
        if(!el) return;
        var found = steTabItems.find(function(x){ return x.v===key; });
        el.textContent = found ? found.l : 'Pilih Halaman';
    }
    function activateTab(key, updateUrl) {
        steActiveTab = key;
        tabButtons.forEach(function (btn) {
            var on = btn.getAttribute('data-tab-btn') === key;
            btn.classList.toggle('active', on);
        });
        tabPanels.forEach(function (panel) {
            panel.classList.toggle('hidden', panel.getAttribute('data-tab-panel') !== key);
        });
        syncStePickerLabel(key);
        renderStePicker();
        if (updateUrl && history.replaceState) {
            history.replaceState(null, '', '{{ url('/admin/settings') }}?tab=' + key);
        }
    }
    tabButtons.forEach(function (btn) {
        btn.addEventListener('click', function () { activateTab(btn.getAttribute('data-tab-btn'), true); });
    });
    // picker render & toggle
    function renderStePicker(){
        var list = document.getElementById('stePickerList');
        if(!list) return;
        list.innerHTML = '';
        steTabItems.forEach(function(it){
            var div = document.createElement('div');
            div.className = 'ste-picker-item' + (it.v===steActiveTab ? ' is-active' : '');
            div.setAttribute('role','option');
            div.innerHTML = hiSvg(it.i, 'font-size:15px;') + '<span style="flex:1">'+it.l+'</span>' + (it.v===steActiveTab ? hiSvg('checkmark', 'font-size:12px;') : '');
            div.addEventListener('click', function(){ activateTab(it.v,true); closeStePicker(); });
            list.appendChild(div);
        });
    }
    window.openStePicker = function(){
        renderStePicker();
        var bd = document.getElementById('stePickerBackdrop');
        if(bd){ bd.classList.add('is-open'); bd.setAttribute('aria-hidden','false'); document.getElementById('steTabPickBtn').setAttribute('aria-expanded','true'); }
    };
    window.closeStePicker = function(){
        var bd = document.getElementById('stePickerBackdrop');
        if(bd){ bd.classList.remove('is-open'); bd.setAttribute('aria-hidden','true'); document.getElementById('steTabPickBtn').setAttribute('aria-expanded','false'); }
    };
    var pickBtn = document.getElementById('steTabPickBtn');
    if(pickBtn){ pickBtn.addEventListener('click', openStePicker); }
    var pickBd = document.getElementById('stePickerBackdrop');
    if(pickBd){ pickBd.addEventListener('click', function(e){ if(e.target===this) closeStePicker(); }); }
    document.addEventListener('keydown', function(e){ if(e.key==='Escape'){ var bd=document.getElementById('stePickerBackdrop'); if(bd&&bd.classList.contains('is-open')) closeStePicker(); }});
    activateTab('{{ $activeTab }}', false);
    var notifH2 = document.querySelector('input[name="rereg_notif_h2"]');
    var notifH2Label = document.getElementById('rereg_notif_h2_label');
    if (notifH2 && notifH2Label) { notifH2.addEventListener('input', function(){ notifH2Label.textContent = this.value || '2'; }); if(notifH2.value) notifH2Label.textContent = notifH2.value; }
    var bankAcc = document.getElementById('bank_account_number');
    if (bankAcc) { bankAcc.addEventListener('input', function(){ this.value = this.value.replace(/\D/g,'').slice(0,30); }); }
    var pendingForm = null;
    var pendingBtn = null;
    function openSteConfirm(title, msg, form, btn) {
        document.getElementById('steConfirmTitle').textContent = title;
        document.getElementById('steConfirmMsg').textContent = msg;
        pendingForm = form;
        pendingBtn = btn;
        var m = document.getElementById('steConfirmModal');
        m.classList.add('is-open'); m.setAttribute('aria-hidden','false');
    }
    window.closeSteConfirm = function(){
        var m = document.getElementById('steConfirmModal');
        m.classList.remove('is-open'); m.setAttribute('aria-hidden','true');
        pendingForm = null; pendingBtn = null;
    };
    document.getElementById('steConfirmAction').addEventListener('click', function(){
        if (pendingForm && pendingBtn) {
            pendingBtn.disabled = true;
            pendingBtn.textContent = 'Menyimpan...';
            pendingForm.submit();
        }
        closeSteConfirm();
    });
    document.getElementById('steConfirmModal').addEventListener('click', function(e){ if(e.target===this) closeSteConfirm(); });
    document.addEventListener('keydown', function(e){ if(e.key==='Escape'){ var m=document.getElementById('steConfirmModal'); if(m&&m.classList.contains('is-open')) closeSteConfirm(); }});
    // per-tab save buttons
    document.querySelectorAll('.btn-save-tab').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.preventDefault();
            var msg = btn.getAttribute('data-save-msg') || 'Simpan perubahan?';
            openSteConfirm(msg, 'Perubahan akan langsung berlaku untuk pendaftaran.', document.getElementById('steMainForm'), btn);
        });
    });
    var btnLevels = document.getElementById('btn-save-levels');
    if (btnLevels) {
        btnLevels.addEventListener('click', function(e){
            e.preventDefault();
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.schools.levels.update') }}';
            var csrf = document.createElement('input'); csrf.type='hidden'; csrf.name='_token'; csrf.value='{{ csrf_token() }}';
            form.appendChild(csrf);
            document.querySelectorAll('input[name^="is_active"]:checked').forEach(function(el){
                var i = document.createElement('input'); i.type='hidden'; i.name=el.name; i.value='1'; form.appendChild(i);
            });
            document.body.appendChild(form);
            openSteConfirm('Simpan status jenjang?', 'Jenjang nonaktif tidak akan muncul di form pendaftaran siswa.', form, btnLevels);
        });
    }
</script>
@endsection
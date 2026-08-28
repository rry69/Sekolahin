@extends('layouts.dashboard')
@section('title', 'Tambah Periode Pendaftaran')
@section('content')

<style>
  .ped {
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
  .ped .ped-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
  .ped .ped-crumb a { color: var(--coral); text-decoration: none; }
  .ped .ped-crumb a:hover { text-decoration: underline; }
  .ped .ped-crumb .sep { color: #d3d6de; }
  .ped .ped-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 2px; }
  .ped .ped-meta { font-size: 13px; color: var(--muted); }
  .ped .ped-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px; font-weight: 500; }
  .ped .ped-alert i { margin-top: 2px; }
  .ped .ped-alert.success { background: var(--green-soft); color: var(--green); }
  .ped .ped-alert.error { background: var(--red-soft); color: var(--red); }
  .ped .ped-sec { border-top: 1px solid var(--divider); padding: 22px 0 6px; }
  .ped .ped-sec:first-of-type { border-top: none; padding-top: 8px; }
  .ped .ped-sec-title { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--ink); text-transform: uppercase; letter-spacing: .4px; margin-bottom: 16px; }
  .ped .ped-sec-title i { color: var(--coral); font-size: 13px; }
  .ped .ped-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
  .ped .ped-grid .full { grid-column: 1 / -1; }
  .ped .ped-field { display: flex; flex-direction: column; gap: 5px; }
  .ped .ped-field label { display: block; font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; }
  .ped .ped-field label .req { color: var(--red); }
  .ped .ped-input-line { width: 100%; padding: 9px 4px; border: none; border-bottom: 1px solid rgba(26,26,46,0.18); border-radius: 0; font-size: 13px; background: transparent; color: var(--ink); box-sizing: border-box; transition: border-color .18s ease; }
  .ped .ped-input-line::placeholder { color: #b8bcc9; }
  .ped .ped-input-line:focus { outline: none; border-bottom-color: var(--coral); }
  .ped .ped-input-box { width: 100%; padding: 10px 14px; border: 1px solid rgba(26,26,46,0.14); border-radius: 11px; font-size: 13px; background: rgba(255,255,255,0.55); color: var(--ink); box-sizing: border-box; transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
  .ped .ped-input-box::placeholder { color: var(--muted); }
  .ped .ped-input-box:focus { outline: none; border-color: var(--coral); box-shadow: 0 0 0 4px rgba(255,107,107,0.14); background: #fff; }
  .ped textarea.ped-input-box { resize: vertical; min-height: 80px; }
  .ped .ped-hint { font-size: 11px; color: var(--muted); margin-top: 5px; line-height: 1.4; }
  .ped .ped-err { font-size: 12px; color: var(--red); margin-top: 5px; }
  .ped .ped-duration { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 9999px; font-size: 12px; font-weight: 500; background: rgba(255,255,255,0.6); border: 1px solid var(--divider); color: var(--muted); }
  .ped .ped-duration strong { color: var(--ink); }
  .ped .ped-toggle { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; color: var(--ink); }
  .ped .ped-toggle input { width: 16px; height: 16px; accent-color: var(--coral); }
  .ped .ped-submit { display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; flex-wrap: wrap; padding-top: 18px; border-top: 1px solid var(--divider); }
  .ped .ped-btn { display: inline-flex; align-items: center; gap: 7px; border: none; cursor: pointer; border-radius: 11px; padding: 10px 17px; font-size: 13px; font-weight: 700; text-decoration: none; transition: transform .15s ease, filter .15s ease, background-color .15s ease; }
  .ped .ped-btn:hover { transform: translateY(-1px); }
  .ped .ped-btn.coral { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 8px 18px -8px rgba(255,107,107,0.6); }
  .ped .ped-btn.coral:hover { filter: brightness(1.04); }
  .ped .ped-btn.ghost { background: rgba(255,255,255,0.65); color: var(--ink); box-shadow: 0 2px 10px -8px rgba(26,26,46,0.3); }
  .ped .ped-btn.ghost:hover { background: #fff; color: var(--coral); }
  .ped .r-pick { display: inline-flex; align-items: center; gap: 8px; flex-wrap: nowrap; padding: 9px 4px; border: none; border-bottom: 1px solid rgba(26,26,46,0.18); border-radius: 0; font-size: 13px; color: var(--ink); background: transparent; min-width: 160px; cursor: pointer; text-align: left; min-height: 38px; transition: border-color .18s ease, color .18s ease; width: 100%; }
  .ped .r-pick:hover { border-bottom-color: var(--coral); }
  .ped .r-pick:focus { outline: none; border-bottom-color: var(--coral); }
  .ped .r-pick .pick-label { flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .ped .r-pick .pick-label.is-placeholder { color: #b8bcc9; }
  .ped .r-pick .pick-caret { display: none; }
  .ped .r-pick .pick-clear { flex: 0 0 auto; display: none; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 6px; background: var(--gray-soft); color: var(--gray); cursor: pointer; font-size: 9px; user-select: none; }
  .ped .r-pick .pick-clear:hover { background: var(--red-soft); color: var(--red); }
  .ped .r-pick.has-value .pick-clear { display: inline-flex; }
  .ped .r-pick.has-value .pick-label.is-placeholder { display: none; }
  .ped .picker-backdrop { position: fixed; inset: 0; z-index: 80; background: rgba(26,26,46,0.32); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); display: none; align-items: flex-start; justify-content: center; padding: 80px 16px 16px; animation: pedPickerFade .18s ease-out; }
  .ped .picker-backdrop.is-open { display: flex; }
  @keyframes pedPickerFade { from { opacity: 0; } to { opacity: 1; } }
  .ped .picker-panel { width: 100%; max-width: 380px; max-height: min(520px, calc(100vh - 120px)); display: flex; flex-direction: column; background: #fff; border-radius: 18px; box-shadow: 0 20px 50px -16px rgba(26,26,46,0.35), 0 0 0 1px rgba(26,26,46,0.06); overflow: hidden; animation: pedPickerPop .22s cubic-bezier(.22,1.2,.36,1); }
  @keyframes pedPickerPop { from { opacity: 0; transform: translateY(-6px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
  .ped .picker-head { display: flex; align-items: center; gap: 10px; padding: 14px 16px; border-bottom: 1px solid var(--divider); }
  .ped .picker-head .picker-title { font-size: 14px; font-weight: 700; color: var(--ink); flex: 1; }
  .ped .picker-head .picker-close { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 8px; border: none; background: transparent; color: var(--muted); cursor: pointer; font-size: 12px; transition: background-color .15s ease, color .15s ease; }
  .ped .picker-head .picker-close:hover { background: var(--gray-soft); color: var(--ink); }
  .ped .picker-search { position: relative; padding: 10px 14px; border-bottom: 1px solid var(--divider); }
  .ped .picker-search i { position: absolute; left: 24px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 12px; pointer-events: none; }
  .ped .picker-search input { width: 100%; padding: 9px 12px 9px 32px; border: 1px solid rgba(26,26,46,0.14); border-radius: 10px; font-size: 13px; color: var(--ink); background: rgba(255,255,255,0.7); transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
  .ped .picker-search input:focus { outline: none; border-color: var(--coral); background: #fff; box-shadow: 0 0 0 3px rgba(255,107,107,0.12); }
  .ped .picker-list { flex: 1; overflow-y: auto; padding: 6px 8px; }
  .ped .picker-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; font-size: 13px; color: var(--ink); cursor: pointer; user-select: none; transition: background-color .15s ease, color .15s ease; }
  .ped .picker-item:hover, .ped .picker-item.is-active { background: var(--coral-soft); color: var(--coral); }
  .ped .picker-item.is-selected { background: var(--coral); color: #fff; font-weight: 600; }
  .ped .picker-item.is-selected:hover { background: var(--coral); }
  .ped .picker-item .pi-label { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .ped .picker-item .pi-check { font-size: 11px; opacity: 0; }
  .ped .picker-item.is-selected .pi-check { opacity: 1; }
  .ped .picker-empty { padding: 26px 12px; text-align: center; color: var(--muted); font-size: 12.5px; }
  .ped .picker-empty i { display: block; font-size: 20px; margin-bottom: 6px; color: #d3d6de; }
  .ped .picker-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 10px 14px; border-top: 1px solid var(--divider); background: rgba(255,255,255,0.5); }
  .ped .picker-foot .picker-clear-all { display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px; border-radius: 9px; border: none; background: transparent; color: var(--muted); font-size: 12px; font-weight: 600; cursor: pointer; transition: color .15s ease, background-color .15s ease; }
  .ped .picker-foot .picker-clear-all:hover { color: var(--red); background: var(--red-soft); }
  .ped .picker-foot .picker-done { display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 9px; border: none; background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; font-size: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 6px 14px -6px rgba(255,107,107,0.55); transition: filter .15s ease, transform .15s ease; }
  .ped .picker-foot .picker-done:hover { filter: brightness(1.04); transform: translateY(-1px); }
  /* date-picker kotak override */
  .ped [data-datepicker-trigger] { border: 1px solid rgba(26,26,46,0.14) !important; border-radius: 11px !important; background: rgba(255,255,255,0.55) !important; padding: 0 12px !important; height: 42px !important; }
  .ped [data-datepicker-trigger]:hover { border-color: rgba(26,26,46,0.22) !important; }
  .ped [data-datepicker-trigger]:focus,
  .ped [data-datepicker-trigger][aria-expanded="true"] { border-color: var(--coral) !important; box-shadow: 0 0 0 4px rgba(255,107,107,0.14); background: #fff !important; }
  @media (max-width: 700px) { .ped .ped-grid { grid-template-columns: 1fr; } .ped { padding: 20px 16px 32px; } }
</style>

<div class="ped">
  <div class="ped-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <a href="{{ route('admin.periods.index') }}">Periode Pendaftaran</a>
    <span class="sep">/</span>
    <span>Tambah Periode</span>
  </div>

  <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:2px;">
    <div>
      <h1 class="ped-title">Tambah Periode Pendaftaran</h1>
      <p class="ped-meta">Buat jendela pendaftaran baru. Status dihitung otomatis dari tanggal dan flag aktif.</p>
    </div>
    <a href="{{ route('admin.periods.index') }}" class="ped-btn ghost"><i class="fa-solid fa-arrow-left" style="font-size:10px;"></i> Kembali</a>
  </div>

@if ($errors->any())
<div class="ped-alert error" style="flex-direction:column;align-items:stretch;">
  <div style="display:flex;gap:10px;"><i class="fa-solid fa-circle-exclamation"></i><strong>Periksa kembali isian Anda:</strong></div>
  <ul style="margin:6px 0 0 18px;list-style:disc;">
    @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

<form action="{{ route('admin.periods.store') }}" method="POST" id="periodForm">
  @csrf
  <div class="ped-sec">
    <div class="ped-sec-title"><i class="fa-solid fa-calendar-days"></i> Informasi Periode</div>
    <div class="ped-grid">
      <div class="ped-field">
        <label>Jenjang <span class="req">*</span></label>
        <button type="button" class="r-pick" data-picker="jenjang" aria-haspopup="listbox" aria-expanded="false">
          <span class="pick-label is-placeholder">-- Pilih Jenjang --</span>
          <span class="pick-clear" data-clear="jenjang" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
          <i class="fa-solid fa-chevron-down pick-caret"></i>
        </button>
        <input type="hidden" name="school_level_id" data-picker-input="jenjang" value="{{ old('school_level_id') }}" required>
        @error('school_level_id')<p class="ped-err">{{ $message }}</p>@enderror
      </div>

      <div class="ped-field">
        <label>Tahun Ajaran</label>
        <input type="text" name="academic_year" value="{{ old('academic_year') }}" placeholder="Contoh: 2026/2027" maxlength="9" class="ped-input-line">
        <p class="ped-hint">Format 2026/2027. Kosongkan jika tidak perlu.</p>
        @error('academic_year')<p class="ped-err">{{ $message }}</p>@enderror
      </div>

      <div class="ped-field full">
        <label>Nama Periode <span class="req">*</span></label>
        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: SPMB 2026/2027 Gelombang 1" class="ped-input-line">
        @error('name')<p class="ped-err">{{ $message }}</p>@enderror
      </div>

      <div class="ped-field">
        <label>Gelombang</label>
        <input type="number" name="wave" value="{{ old('wave') }}" min="1" max="10" placeholder="Contoh: 1" class="ped-input-line">
        <p class="ped-hint">Opsional. Isi 1, 2, 3, dst.</p>
        @error('wave')<p class="ped-err">{{ $message }}</p>@enderror
      </div>

      <div class="ped-field">
        <label>Maksimal Pendaftar</label>
        <input type="number" name="max_applicants" value="{{ old('max_applicants') }}" min="1" placeholder="Kosongkan untuk tak terbatas" class="ped-input-line">
        <p class="ped-hint">Kosongkan = kuota tak terbatas.</p>
        @error('max_applicants')<p class="ped-err">{{ $message }}</p>@enderror
      </div>
    </div>
  </div>

  <div class="ped-sec">
    <div class="ped-sec-title"><i class="fa-solid fa-clock"></i> Jadwal</div>
    <div class="ped-grid">
      <div class="ped-field">
        <label>Tanggal Mulai <span class="req">*</span></label>
        <x-date-picker name="start_date" :required="true" label="Tanggal Mulai" />
        @error('start_date')<p class="ped-err">{{ $message }}</p>@enderror
      </div>

      <div class="ped-field">
        <label>Tanggal Selesai <span class="req">*</span></label>
        <x-date-picker name="end_date" :required="true" label="Tanggal Selesai" />
        @error('end_date')<p class="ped-err">{{ $message }}</p>@enderror
      </div>

      <div class="ped-field full" style="margin-top:2px;">
        <span class="ped-duration" id="durationBadge"><i class="fa-regular fa-clock" style="font-size:11px;"></i> <span id="durationText">Pilih tanggal mulai dan selesai</span></span>
        <p class="ped-hint" id="dateOrderHint" style="display:none;color:var(--red);"></p>
      </div>
    </div>
  </div>

  <div class="ped-sec">
    <div class="ped-sec-title"><i class="fa-solid fa-note-sticky"></i> Catatan & Status</div>
    <div class="ped-grid">
      <div class="ped-field full">
        <label>Catatan / Deskripsi</label>
        <textarea name="description" rows="3" class="ped-input-box" placeholder="Catatan internal untuk periode ini (opsional)">{{ old('description') }}</textarea>
        @error('description')<p class="ped-err">{{ $message }}</p>@enderror
      </div>

      <div class="ped-field full">
        <label class="ped-toggle" style="padding-top:4px;">
          <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
          <span>Periode aktif</span>
        </label>
        <p class="ped-hint">Jika dimatikan, status menjadi <strong>Nonaktif</strong> dan pendaftaran tertutup meski tanggal masih berlaku.</p>
      </div>
    </div>
  </div>

  <div class="ped-submit">
    <a href="{{ route('admin.periods.index') }}" class="ped-btn ghost">Batal</a>
    <button type="submit" class="ped-btn coral" id="saveBtn">
      <i class="fa-solid fa-floppy-disk" style="font-size:11px;"></i> <span id="saveBtnText">Simpan Periode</span>
    </button>
  </div>
</form>

{{-- Picker modal --}}
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
  $pickJenjang = [];
  foreach ($schoolLevels as $lv) { $pickJenjang[] = ['v'=>(string)$lv->id, 'l'=>$lv->name]; }
  $pickerJson = ['jenjang'=>$pickJenjang];
  $pickerLabels = ['jenjang'=>'Pilih Jenjang'];
@endphp
<div id="reg-data" hidden data-picker='@json($pickerJson)' data-picker-labels='@json($pickerLabels)'></div>
</div>

<script>
(function () {
  var form = document.getElementById('periodForm');
  var saveBtn = document.getElementById('saveBtn');
  var saveBtnText = document.getElementById('saveBtnText');

  function getVal(name) {
    var el = form.querySelector('[name=\"' + name + '\"]');
    return el ? el.value : '';
  }

  function updateDuration() {
    var start = getVal('start_date');
    var end = getVal('end_date');
    var textEl = document.getElementById('durationText');
    var hintEl = document.getElementById('dateOrderHint');
    if (!textEl) return;
    if (!start || !end) {
      textEl.textContent = 'Pilih tanggal mulai dan selesai';
      if (hintEl) hintEl.style.display = 'none';
      return;
    }
    var d1 = new Date(start);
    var d2 = new Date(end);
    if (isNaN(d1) || isNaN(d2)) {
      textEl.textContent = 'Pilih tanggal mulai dan selesai';
      return;
    }
    if (d2 < d1) {
      textEl.textContent = 'Tanggal selesai sebelum tanggal mulai';
      if (hintEl) { hintEl.textContent = 'Tanggal Selesai tidak boleh sebelum Tanggal Mulai.'; hintEl.style.display = 'block'; }
      return;
    }
    if (hintEl) hintEl.style.display = 'none';
    var diff = Math.round((d2 - d1) / 86400000) + 1;
    textEl.innerHTML = 'Berlangsung selama <strong>' + diff + ' hari</strong>';
  }

  var observer = new MutationObserver(updateDuration);
  form.querySelectorAll('[name=\"start_date\"],[name=\"end_date\"]').forEach(function (el) {
    observer.observe(el, { attributes: true, attributeFilter: ['value'] });
    el.addEventListener('change', updateDuration);
  });
  setInterval(updateDuration, 400);
  updateDuration();

  form.addEventListener('submit', function () {
    if (saveBtn) saveBtn.disabled = true;
    if (saveBtnText) saveBtnText.textContent = 'Menyimpan...';
  });
})();
</script>
@endsection

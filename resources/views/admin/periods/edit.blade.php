@extends('layouts.dashboard')
@section('title', 'Edit Periode Pendaftaran')
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
  .ped .ped-warn { display: flex; gap: 10px; align-items: flex-start; padding: 12px 14px; border-radius: 12px; background: var(--amber-soft); border: 1px solid rgba(245,158,11,0.22); color: #92400e; font-size: 12px; line-height: 1.5; margin-bottom: 16px; }
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
  .ped .ped-input-line:disabled { color: var(--muted); cursor: not-allowed; }
  .ped .ped-input-box { width: 100%; padding: 10px 14px; border: 1px solid rgba(26,26,46,0.14); border-radius: 11px; font-size: 13px; background: rgba(255,255,255,0.55); color: var(--ink); box-sizing: border-box; transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
  .ped .ped-input-box::placeholder { color: var(--muted); }
  .ped .ped-input-box:focus { outline: none; border-color: var(--coral); box-shadow: 0 0 0 4px rgba(255,107,107,0.14); background: #fff; }
  .ped textarea.ped-input-box { resize: vertical; min-height: 80px; }
  .ped .ped-hint { font-size: 11px; color: var(--muted); margin-top: 5px; line-height: 1.4; }
  .ped .ped-err { font-size: 12px; color: var(--red); margin-top: 5px; }
  .ped .ped-locked { display: flex; align-items: center; gap: 8px; padding: 9px 4px; border-bottom: 1px solid rgba(26,26,46,0.12); font-size: 13px; color: var(--ink); font-weight: 600; }
  .ped .ped-locked i { color: var(--muted); font-size: 11px; }
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
    <span>Edit Periode</span>
  </div>

  <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:2px;">
    <div>
      <h1 class="ped-title">Edit Periode Pendaftaran</h1>
      <p class="ped-meta">Perbarui periode. Jenjang dikunci agar data historis tidak berubah.</p>
    </div>
    <a href="{{ route('admin.periods.index') }}" class="ped-btn ghost"><i class="fa-solid fa-arrow-left" style="font-size:10px;"></i> Kembali</a>
  </div>

@php
  $isRunning = $registrationPeriod->isCurrentlyRunning();
  $computed = $registrationPeriod->computedStatus();
  $statusLabel = \App\Models\RegistrationPeriod::statusLabel($computed);
@endphp

@if ($isRunning)
<div class="ped-warn">
  <i class="fa-solid fa-triangle-exclamation" style="margin-top:2px;"></i>
  <div>
    <strong>Periode sedang berlangsung ({{ $statusLabel }})</strong> — perubahan tanggal akan langsung memengaruhi pendaftaran yang berjalan. Pastikan rentang baru tidak bertabrakan dengan periode aktif lain di jenjang yang sama.
  </div>
</div>
@endif

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

<form action="{{ route('admin.periods.update', $registrationPeriod) }}" method="POST" id="periodForm">
  @csrf
  @method('PATCH')

  <div class="ped-sec">
    <div class="ped-sec-title"><i class="fa-solid fa-calendar-days"></i> Informasi Periode</div>
    <div class="ped-grid">
      <div class="ped-field">
        <label>Jenjang <span class="req">*</span></label>
        <div class="ped-locked"><i class="fa-solid fa-lock"></i> {{ $schoolLevels->firstWhere('id', old('school_level_id', $registrationPeriod->school_level_id))?->name ?? '-' }}</div>
        <input type="hidden" name="school_level_id" value="{{ old('school_level_id', $registrationPeriod->school_level_id) }}">
        <p class="ped-hint">Jenjang dikunci saat edit untuk menjaga konsistensi data pendaftaran.</p>
        @error('school_level_id')<p class="ped-err">{{ $message }}</p>@enderror
      </div>

      <div class="ped-field">
        <label>Tahun Ajaran</label>
        <input type="text" name="academic_year" value="{{ old('academic_year', $registrationPeriod->academic_year) }}" placeholder="Contoh: 2026/2027" maxlength="9" class="ped-input-line">
        <p class="ped-hint">Format 2026/2027. Kosongkan jika tidak perlu.</p>
        @error('academic_year')<p class="ped-err">{{ $message }}</p>@enderror
      </div>

      <div class="ped-field full">
        <label>Nama Periode <span class="req">*</span></label>
        <input type="text" name="name" value="{{ old('name', $registrationPeriod->name) }}" required class="ped-input-line" placeholder="Contoh: PPDB SMA Gelombang 1">
        @error('name')<p class="ped-err">{{ $message }}</p>@enderror
      </div>

      <div class="ped-field">
        <label>Gelombang</label>
        <input type="number" name="wave" value="{{ old('wave', $registrationPeriod->wave) }}" min="1" max="10" placeholder="Contoh: 1" class="ped-input-line">
        <p class="ped-hint">Opsional. Isi 1, 2, 3, dst.</p>
        @error('wave')<p class="ped-err">{{ $message }}</p>@enderror
      </div>

      <div class="ped-field">
        <label>Maksimal Pendaftar</label>
        <input type="number" name="max_applicants" value="{{ old('max_applicants', $registrationPeriod->max_applicants) }}" min="1" placeholder="Kosongkan untuk tak terbatas" class="ped-input-line">
        <p class="ped-hint">Saat ini {{ $registrationPeriod->registrations_count }} pendaftar. Tidak boleh dikecilkan di bawah jumlah tersebut.</p>
        @error('max_applicants')<p class="ped-err">{{ $message }}</p>@enderror
      </div>
    </div>
  </div>

  <div class="ped-sec">
    <div class="ped-sec-title"><i class="fa-solid fa-clock"></i> Jadwal</div>
    <div class="ped-grid">
      <div class="ped-field">
        <label>Tanggal Mulai <span class="req">*</span></label>
        <x-date-picker name="start_date" :required="true" :value="$registrationPeriod->start_date?->format('Y-m-d')" label="Tanggal Mulai" />
        @error('start_date')<p class="ped-err">{{ $message }}</p>@enderror
      </div>

      <div class="ped-field">
        <label>Tanggal Selesai <span class="req">*</span></label>
        <x-date-picker name="end_date" :required="true" :value="$registrationPeriod->end_date?->format('Y-m-d')" label="Tanggal Selesai" />
        @error('end_date')<p class="ped-err">{{ $message }}</p>@enderror
      </div>

      <div class="ped-field full" style="margin-top:2px;">
        <span class="ped-duration" id="durationBadge"><i class="fa-regular fa-clock" style="font-size:11px;"></i> <span id="durationText">{{ $registrationPeriod->durationLabel() }}</span></span>
        <p class="ped-hint" id="dateOrderHint" style="display:none;color:var(--red);"></p>
      </div>
    </div>
  </div>

  <div class="ped-sec">
    <div class="ped-sec-title"><i class="fa-solid fa-note-sticky"></i> Catatan & Status</div>
    <div class="ped-grid">
      <div class="ped-field full">
        <label>Catatan / Deskripsi</label>
        <textarea name="description" rows="3" class="ped-input-box" placeholder="Catatan internal untuk periode ini (opsional)">{{ old('description', $registrationPeriod->description) }}</textarea>
        @error('description')<p class="ped-err">{{ $message }}</p>@enderror
      </div>

      <div class="ped-field full">
        <label class="ped-toggle" style="padding-top:4px;">
          <input type="checkbox" name="is_active" value="1" {{ old('is_active', $registrationPeriod->is_active) ? 'checked' : '' }}>
          <span>Periode aktif</span>
        </label>
        <p class="ped-hint">Jika dimatikan, status menjadi <strong>Nonaktif</strong> dan pendaftaran tertutup.</p>
      </div>
    </div>
  </div>

  <div class="ped-submit">
    <a href="{{ route('admin.periods.index') }}" class="ped-btn ghost">Batal</a>
    <button type="submit" class="ped-btn coral" id="saveBtn">
      <i class="fa-solid fa-floppy-disk" style="font-size:11px;"></i> <span id="saveBtnText">Simpan Perubahan</span>
    </button>
  </div>
</form>
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
    if (saveBtn) { saveBtn.disabled = true; }
    if (saveBtnText) saveBtnText.textContent = 'Menyimpan...';
  });
})();
</script>
@endsection

@extends('layouts.dashboard')
@section('title', 'Edit Periode Pendaftaran')
@section('content')

<style>
  .prd-card { background: var(--panel); border: 1px solid var(--border); border-radius: 12px; }
  .prd-card-head { padding: 18px 22px; border-bottom: 1px solid var(--hairline); }
  .prd-card-body { padding: 22px; }
  .prd-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
  .prd-grid .full { grid-column: 1 / -1; }
  .prd-field label { display: block; font-size: 13px; font-weight: 500; color: var(--tx1); margin-bottom: 6px; }
  .prd-field .req { color: var(--danger); }
  .prd-input { width: 100%; padding: 8px 12px; border: 1px solid var(--input-border); border-radius: 8px; font-size: 13px; background: var(--input-bg); color: var(--tx-body); box-sizing: border-box; }
  .prd-input:focus { outline: none; border-color: var(--accent); }
  .prd-input:disabled { background: var(--panel-2); color: var(--tx3); cursor: not-allowed; }
  textarea.prd-input { resize: vertical; min-height: 80px; }
  .prd-hint { font-size: 11px; color: var(--tx4); margin-top: 5px; line-height: 1.4; }
  .prd-err { font-size: 12px; color: var(--error-fg); margin-top: 5px; }
  .prd-submit { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; flex-wrap: wrap; }
  .prd-toggle { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; color: var(--tx-body); }
  .prd-toggle input { width: 16px; height: 16px; accent-color: var(--accent); }
  .prd-duration { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 9999px; font-size: 12px; font-weight: 500; background: var(--panel-2); border: 1px solid var(--border); color: var(--tx2); }
  .prd-duration strong { color: var(--tx1); }
  .prd-running-warning { display: flex; gap: 10px; align-items: flex-start; padding: 12px 14px; border-radius: 8px; background: #fef3c7; border: 1px solid #fde68a; color: #92400e; font-size: 12px; line-height: 1.5; margin-bottom: 16px; }
  @media (max-width: 700px) { .prd-grid { grid-template-columns: 1fr; } }
</style>

<div class="breadcrumb">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <span class="sep">/</span>
  <a href="{{ route('admin.periods.index') }}">Periode Pendaftaran</a>
  <span class="sep">/</span>
  <span>Edit Periode</span>
</div>

<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:18px;">
  <div>
    <h1 class="page-title" style="margin-bottom:2px;">Edit Periode Pendaftaran</h1>
    <p style="font-size:13px;color:var(--tx2);">Perbarui periode. Jenjang dikunci agar data historis tidak berubah.</p>
  </div>
  <a href="{{ route('admin.periods.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left" style="font-size:10px;"></i> Kembali</a>
</div>

@php
  $isRunning = $registrationPeriod->isCurrentlyRunning();
  $computed = $registrationPeriod->computedStatus();
  $statusLabel = \App\Models\RegistrationPeriod::statusLabel($computed);
@endphp

@if ($isRunning)
<div class="prd-running-warning">
  <i class="fa-solid fa-triangle-exclamation" style="margin-top:2px;"></i>
  <div>
    <strong>Periode sedang berlangsung ({{ $statusLabel }})</strong> — perubahan tanggal akan langsung memengaruhi pendaftaran yang berjalan. Pastikan rentang baru tidak bertabrakan dengan periode aktif lain di jenjang yang sama.
  </div>
</div>
@endif

@if ($errors->any())
<div class="alert alert-error">
  <strong>Periksa kembali isian Anda:</strong>
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
  <div class="prd-card">
    <div class="prd-card-head">
      <h4 style="margin:0;font-size:15px;font-weight:600;color:var(--tx1);"><i class="fa-solid fa-calendar-days" style="margin-right:6px;color:var(--accent);"></i> Data Periode</h4>
      <p style="margin:6px 0 0;font-size:12px;color:var(--tx3);">Status saat ini: <strong style="color:var(--tx1);">{{ $statusLabel }}</strong> · {{ $registrationPeriod->durationLabel() }} · {{ $registrationPeriod->registrations_count }} pendaftar</p>
    </div>
    <div class="prd-card-body">
      <div class="prd-grid">
        <div class="prd-field">
          <label>Jenjang <span class="req">*</span></label>
          {{-- Disabled agar data historis tidak berubah; tetap kirim via hidden --}}
          <select class="prd-input" disabled>
            @foreach ($schoolLevels as $level)
              <option value="{{ $level->id }}" {{ (old('school_level_id', $registrationPeriod->school_level_id) == $level->id) ? 'selected' : '' }}>{{ $level->name }}</option>
            @endforeach
          </select>
          <input type="hidden" name="school_level_id" value="{{ old('school_level_id', $registrationPeriod->school_level_id) }}">
          <p class="prd-hint">Jenjang dikunci saat edit untuk menjaga konsistensi data pendaftaran.</p>
          @error('school_level_id')<p class="prd-err">{{ $message }}</p>@enderror
        </div>

        <div class="prd-field">
          <label>Tahun Ajaran</label>
          <input type="text" name="academic_year" value="{{ old('academic_year', $registrationPeriod->academic_year) }}" placeholder="Contoh: 2026/2027" maxlength="9" class="prd-input">
          <p class="prd-hint">Format 2026/2027. Kosongkan jika tidak perlu.</p>
          @error('academic_year')<p class="prd-err">{{ $message }}</p>@enderror
        </div>

        <div class="prd-field full">
          <label>Nama Periode <span class="req">*</span></label>
          <input type="text" name="name" value="{{ old('name', $registrationPeriod->name) }}" required class="prd-input">
          @error('name')<p class="prd-err">{{ $message }}</p>@enderror
        </div>

        <div class="prd-field">
          <label>Gelombang</label>
          <input type="number" name="wave" value="{{ old('wave', $registrationPeriod->wave) }}" min="1" max="10" placeholder="Contoh: 1" class="prd-input">
          <p class="prd-hint">Opsional. Isi 1, 2, 3, dst.</p>
          @error('wave')<p class="prd-err">{{ $message }}</p>@enderror
        </div>

        <div class="prd-field">
          <label>Maksimal Pendaftar</label>
          <input type="number" name="max_applicants" value="{{ old('max_applicants', $registrationPeriod->max_applicants) }}" min="1" placeholder="Kosongkan untuk tak terbatas" class="prd-input">
          <p class="prd-hint">Saat ini {{ $registrationPeriod->registrations_count }} pendaftar. Tidak boleh dikecilkan di bawah jumlah tersebut.</p>
          @error('max_applicants')<p class="prd-err">{{ $message }}</p>@enderror
        </div>

        <div class="prd-field">
          <label>Tanggal Mulai <span class="req">*</span></label>
          <x-date-picker name="start_date" :required="true" :value="$registrationPeriod->start_date?->format('Y-m-d')" label="Tanggal Mulai" />
          @error('start_date')<p class="prd-err">{{ $message }}</p>@enderror
        </div>

        <div class="prd-field">
          <label>Tanggal Selesai <span class="req">*</span></label>
          <x-date-picker name="end_date" :required="true" :value="$registrationPeriod->end_date?->format('Y-m-d')" label="Tanggal Selesai" />
          @error('end_date')<p class="prd-err">{{ $message }}</p>@enderror
        </div>

        <div class="prd-field full" style="margin-top:2px;">
          <span class="prd-duration" id="durationBadge"><i class="fa-regular fa-clock" style="font-size:11px;"></i> <span id="durationText">{{ $registrationPeriod->durationLabel() }}</span></span>
          <p class="prd-hint" id="dateOrderHint" style="display:none;color:var(--danger);"></p>
        </div>

        <div class="prd-field full">
          <label>Catatan / Deskripsi</label>
          <textarea name="description" rows="3" class="prd-input" placeholder="Catatan internal untuk periode ini (opsional)">{{ old('description', $registrationPeriod->description) }}</textarea>
          @error('description')<p class="prd-err">{{ $message }}</p>@enderror
        </div>

        <div class="prd-field full">
          <label class="prd-toggle" style="padding-top:4px;">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $registrationPeriod->is_active) ? 'checked' : '' }}>
            <span>Periode aktif</span>
          </label>
          <p class="prd-hint">Jika dimatikan, status menjadi <strong>Nonaktif</strong> dan pendaftaran tertutup.</p>
        </div>
      </div>

      <div class="prd-submit">
        <a href="{{ route('admin.periods.index') }}" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary" id="saveBtn">
          <i class="fa-solid fa-floppy-disk" style="font-size:11px;"></i> <span id="saveBtnText">Simpan Perubahan</span>
        </button>
      </div>
    </div>
  </div>
</form>

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
    saveBtn.disabled = true;
    saveBtnText.textContent = 'Menyimpan...';
  });
})();
</script>
@endsection

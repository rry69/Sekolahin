@extends('layouts.dashboard')
@section('title', 'Edit Jurusan')
@section('content')

<style>
  .mjr-card { background: var(--panel); border: 1px solid var(--border); border-radius: 12px; }
  .mjr-card-head { padding: 18px 22px; border-bottom: 1px solid var(--hairline); }
  .mjr-card-body { padding: 22px; }
  .mjr-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
  .mjr-grid .full { grid-column: 1 / -1; }
  .mjr-field label { display: block; font-size: 13px; font-weight: 500; color: var(--tx1); margin-bottom: 6px; }
  .mjr-field .req { color: var(--danger); }
  .mjr-input { width: 100%; padding: 8px 12px; border: 1px solid var(--input-border); border-radius: 8px; font-size: 13px; background: var(--input-bg); color: var(--tx-body); box-sizing: border-box; }
  .mjr-input:focus { outline: none; border-color: var(--accent); }
  textarea.mjr-input { resize: vertical; min-height: 80px; }
  .mjr-hint { font-size: 11px; color: var(--tx4); margin-top: 5px; }
  .mjr-err { font-size: 12px; color: var(--error-fg); margin-top: 5px; }
  .mjr-total { background: var(--panel-2); border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px; font-size: 13px; color: var(--tx2); }
  .mjr-total strong { font-size: 18px; color: var(--accent); }
  .mjr-quota-box { border: 1px solid var(--border); border-radius: 10px; padding: 14px; background: var(--panel-2); }
  .mjr-submit { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; flex-wrap: wrap; }
  .mjr-toggle { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; color: var(--tx-body); }
  .mjr-toggle input { width: 16px; height: 16px; accent-color: var(--accent); }
  @media (max-width: 700px) { .mjr-grid { grid-template-columns: 1fr; } }
</style>

<div class="breadcrumb">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <span class="sep">/</span>
  <a href="{{ route('admin.majors.index') }}">Kelola Jurusan</a>
  <span class="sep">/</span>
  <span>Edit Jurusan</span>
</div>

<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:18px;">
  <div>
    <h1 class="page-title" style="margin-bottom:2px;">Edit Jurusan</h1>
    <p style="font-size:13px;color:var(--tx2);">Perbarui data jurusan dan kuota per jalur.</p>
  </div>
  <a href="{{ route('admin.majors.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left" style="font-size:10px;"></i> Kembali</a>
</div>

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

<form action="{{ route('admin.majors.update', $major) }}" method="POST" id="majorForm">
  @csrf
  @method('PATCH')
  <div class="mjr-card">
    <div class="mjr-card-head">
      <h4 style="margin:0;font-size:15px;font-weight:600;color:var(--tx1);"><i class="fa-solid fa-graduation-cap" style="margin-right:6px;color:var(--accent);"></i> Data Jurusan</h4>
    </div>
    <div class="mjr-card-body">
      <div class="mjr-grid">
        <div class="mjr-field">
          <label>Jenjang <span class="req">*</span></label>
          <select name="school_level_id" id="school_level_id" required class="mjr-input" onchange="filterSchools()">
            @foreach($levels as $level)
              <option value="{{ $level->id }}" {{ old('school_level_id', $major->school_level_id) == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
            @endforeach
          </select>
          @error('school_level_id')<p class="mjr-err">{{ $message }}</p>@enderror
        </div>

        <div class="mjr-field">
          <label>Sekolah <span class="req">*</span></label>
          <select name="school_id" id="school_id" required class="mjr-input">
            @foreach($schools as $school)
              <option value="{{ $school->id }}" data-levels="{{ $school->schoolLevels->pluck('id')->join(',') }}" {{ old('school_id', $major->school_id) == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
            @endforeach
          </select>
          @error('school_id')<p class="mjr-err">{{ $message }}</p>@enderror
        </div>

        <div class="mjr-field">
          <label>Nama Jurusan <span class="req">*</span></label>
          <input type="text" name="name" value="{{ old('name', $major->name) }}" required maxlength="255" class="mjr-input">
          @error('name')<p class="mjr-err">{{ $message }}</p>@enderror
        </div>

        <div class="mjr-field">
          <label>Kode <span class="req">*</span></label>
          <input type="text" name="code" value="{{ old('code', $major->code) }}" required maxlength="50" class="mjr-input" style="text-transform:uppercase;">
          <p class="mjr-hint">Kode harus unik dalam satu sekolah.</p>
          @error('code')<p class="mjr-err">{{ $message }}</p>@enderror
        </div>

        <div class="mjr-field">
          <label>Status</label>
          <div style="display:flex;gap:18px;align-items:center;padding-top:8px;">
            <label class="mjr-toggle">
              <input type="checkbox" name="is_active" value="1" {{ old('is_active', $major->is_active) ? 'checked' : '' }}>
              <span>Aktif (menerima pendaftaran)</span>
            </label>
          </div>
          @error('is_active')<p class="mjr-err">{{ $message }}</p>@enderror
        </div>

        <div class="mjr-field">
          <label>Urutan Tampil</label>
          <input type="number" name="order" value="{{ old('order', $major->order) }}" min="0" class="mjr-input" placeholder="e.g. 1">
          <p class="mjr-hint">Opsional. Nilai lebih kecil tampil lebih dulu.</p>
          @error('order')<p class="mjr-err">{{ $message }}</p>@enderror
        </div>

        <div class="mjr-field full">
          <label>Kuota per Jalur</label>
          <div class="mjr-quota-box">
            <div class="mjr-grid" style="grid-template-columns:repeat(3,1fr);">
              @foreach($tracks as $t)
                @php $q = $major->trackQuotas->firstWhere('registration_track_id', $t->id)?->quota; @endphp
                <div class="mjr-field">
                  <label>{{ $t->name }}</label>
                  <input type="number" name="quota_track_{{ $t->id }}" value="{{ old('quota_track_'.$t->id, $q) }}" min="0" class="mjr-input quota-input" data-track="{{ $t->id }}" placeholder="0">
                  @error('quota_track_'.$t->id)<p class="mjr-err">{{ $message }}</p>@enderror
                </div>
              @endforeach
            </div>
            <div class="mjr-total" style="margin-top:14px;">
              Total Kuota: <strong id="totalQuota">{{ $major->totalQuotaByTracks() ?: $major->quota }}</strong>
              <span style="display:block;font-size:11px;color:var(--tx4);">Total otomatis = jumlah kuota semua jalur.</span>
            </div>
          </div>
        </div>

        <div class="mjr-field full">
          <label>Deskripsi</label>
          <textarea name="description" rows="3" class="mjr-input">{{ old('description', $major->description) }}</textarea>
          @error('description')<p class="mjr-err">{{ $message }}</p>@enderror
        </div>
      </div>

      <div class="mjr-submit">
        <a href="{{ route('admin.majors.index') }}" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary" id="saveBtn">
          <i class="fa-solid fa-floppy-disk" style="font-size:11px;"></i> <span id="saveBtnText">Simpan Perubahan</span>
        </button>
      </div>
    </div>
  </div>
</form>

<script>
  function filterSchools() {
    const levelId = document.getElementById('school_level_id').value;
    const schoolSelect = document.getElementById('school_id');
    const options = schoolSelect.querySelectorAll('option[data-levels]');
    options.forEach(opt => {
      const levels = (opt.dataset.levels || '').split(',').map(v => v.trim());
      opt.style.display = (!levelId || levels.includes(levelId)) ? '' : 'none';
    });
    const selected = schoolSelect.options[schoolSelect.selectedIndex];
    if (selected && selected.hasAttribute('data-levels') && !selected.dataset.levels.split(',').includes(levelId)) {
      schoolSelect.value = '';
    }
  }
  window.addEventListener('DOMContentLoaded', filterSchools);

  (function () {
    var quotaInputs = document.querySelectorAll('.quota-input');
    var totalEl = document.getElementById('totalQuota');
    function updateTotal() {
      var sum = 0;
      quotaInputs.forEach(function (inp) {
        var v = parseInt(inp.value, 10);
        if (!isNaN(v) && v > 0) sum += v;
      });
      totalEl.textContent = sum;
    }
    quotaInputs.forEach(function (inp) { inp.addEventListener('input', updateTotal); });

    var form = document.getElementById('majorForm');
    var saveBtn = document.getElementById('saveBtn');
    var saveBtnText = document.getElementById('saveBtnText');
    form.addEventListener('submit', function () {
      saveBtn.disabled = true;
      saveBtnText.textContent = 'Menyimpan...';
    });
  })();
</script>
@endsection

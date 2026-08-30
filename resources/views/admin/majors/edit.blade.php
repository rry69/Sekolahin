@extends('layouts.dashboard')
@section('title', 'Edit Jurusan')
@section('content')

@php $proLocked = ! ($_pv['licensed'] ?? true); @endphp

<style>
  /* ===================== EDIT JURUSAN — Bringova (no cards, scoped) ===================== */
  .emjr {
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
    max-width: 100%;
    overflow: hidden;
    box-sizing: border-box;
  }

  /* ---------- header ---------- */
  .emjr .e-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
  .emjr .e-crumb a { color: var(--coral); text-decoration: none; }
  .emjr .e-crumb a:hover { text-decoration: underline; }
  .emjr .e-crumb .sep { color: #d3d6de; }
  .emjr .e-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 2px; }
  .emjr .e-meta { font-size: 13px; color: var(--muted); margin-bottom: 18px; }
  .emjr .e-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 22px; }

  /* ---------- alerts ---------- */
  .emjr .e-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 18px; font-weight: 500; }
  .emjr .e-alert i { margin-top: 2px; }
  .emjr .e-alert.error { background: var(--red-soft); color: var(--red); }
  .emjr .e-alert.error ul { margin: 6px 0 0 18px; list-style: disc; font-weight: 400; }

  /* ---------- section (divider, no card) ---------- */
  .emjr .e-sec { border-top: 1px solid var(--divider); padding: 22px 0 6px; }
  .emjr .e-sec:first-of-type { border-top: none; padding-top: 6px; }
  .emjr .e-sec-head { display: flex; align-items: center; gap: 10px; margin-bottom: 18px; }
  .emjr .e-sec-ic { flex: 0 0 auto; width: auto; height: auto; display: inline-flex; align-items: center; justify-content: center; font-size: 22px; line-height: 1; background: none; color: var(--coral); }
  .emjr .e-sec-title { font-size: 15px; font-weight: 700; color: var(--ink); }

  /* ---------- grid & fields ---------- */
  .emjr .e-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px 18px; }
  .emjr .e-grid .full { grid-column: 1 / -1; }
  .emjr .e-field label { display: block; font-size: 13px; font-weight: 500; color: var(--ink); margin-bottom: 7px; }
  .emjr .e-field .req { color: var(--red); }
  .emjr .e-input { width: 100%; padding: 11px 13px; border: 1px solid rgba(26,26,46,0.14); border-radius: 11px; font-size: 13px; color: var(--ink); background: rgba(255,255,255,0.5); box-sizing: border-box; transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
  .emjr .e-input::placeholder { color: var(--muted); }
  .emjr .e-input:focus { outline: none; border-color: var(--coral); box-shadow: 0 0 0 4px rgba(255,107,107,0.14); background: #fff; }
  .emjr .e-input[type="number"] { -moz-appearance: textfield; }
  .emjr .e-input[type="number"]::-webkit-outer-spin-button,
  .emjr .e-input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
  textarea.e-input { resize: vertical; min-height: 80px; }
  .emjr select.e-input { appearance: none; -webkit-appearance: none; background-image: linear-gradient(45deg, transparent 50%, var(--muted) 50%), linear-gradient(135deg, var(--muted) 50%, transparent 50%); background-position: calc(100% - 18px) 50%, calc(100% - 13px) 50%; background-size: 5px 5px; background-repeat: no-repeat; padding-right: 34px; cursor: pointer; }
  .emjr .e-hint { font-size: 11px; color: var(--muted); margin-top: 6px; }
  .emjr .e-err { font-size: 12px; color: var(--red); margin-top: 6px; }

  /* ---------- toggle ---------- */
  .emjr .e-toggle { display: inline-flex; align-items: center; gap: 9px; cursor: pointer; font-size: 13px; color: var(--ink); padding-top: 8px; }
  .emjr .e-switch { position: relative; width: 44px; height: 24px; flex: 0 0 auto; background: transparent; border: 1px solid var(--gray); border-radius: 9999px; transition: background .2s, border-color .2s; }
  .emjr .e-switch::after { content: ''; position: absolute; top: 2px; left: 2px; width: 18px; height: 18px; background: #fff; border: 1px solid var(--gray); border-radius: 9999px; transition: left .2s, border-color .2s; box-shadow: 0 1px 2px rgba(0,0,0,0.2); }
  .emjr .e-toggle input { position: absolute; opacity: 0; pointer-events: none; }
  .emjr .e-toggle input:checked + .e-switch { background: var(--green); border-color: var(--green); }
  .emjr .e-toggle input:checked + .e-switch::after { left: 22px; border-color: #fff; }

  /* ---------- quota box ---------- */
  .emjr .e-quota-box { border-top: 1px solid var(--divider); padding-top: 18px; }
  .emjr .e-quota-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
  .emjr .e-total { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; padding: 12px 16px; border: 1px solid var(--divider); border-radius: 12px; background: rgba(255,255,255,0.4); font-size: 13px; color: var(--ink); margin-top: 16px; }
  .emjr .e-total strong { font-size: 18px; color: var(--coral); font-weight: 800; }
  .emjr .e-total span { display: block; width: 100%; font-size: 11px; color: var(--muted); }

  /* ---------- submit ---------- */
  .emjr .e-submit { display: flex; justify-content: flex-end; gap: 10px; margin-top: 26px; padding-top: 22px; border-top: 1px solid var(--divider); flex-wrap: wrap; }
  .emjr .e-btn { display: inline-flex; align-items: center; gap: 7px; border: none; cursor: pointer; border-radius: 11px; padding: 10px 17px; font-size: 13px; font-weight: 700; text-decoration: none; transition: transform .15s ease, filter .15s ease, background-color .15s ease, color .15s ease; }
  .emjr .e-btn:hover { transform: translateY(-1px); }
  .emjr .e-btn.coral { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 8px 18px -8px rgba(255,107,107,0.6); }
  .emjr .e-btn.coral:hover { filter: brightness(1.04); }
  .emjr .e-btn.coral:disabled { opacity: .6; cursor: wait; transform: none; }
  .emjr .e-btn.ghost { background: rgba(255,255,255,0.6); color: var(--ink); box-shadow: 0 2px 10px -8px rgba(26,26,46,0.3); }
  .emjr .e-btn.ghost:hover { background: #fff; color: var(--coral); }

  /* ---------- responsive ---------- */
  @media (max-width: 720px) { .emjr .e-grid, .emjr .e-quota-grid { grid-template-columns: 1fr; } }
</style>

<div class="emjr">
  <div class="e-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <a href="{{ route('admin.majors.index') }}">Kelola Jurusan</a>
    <span class="sep">/</span>
    <span>Edit Jurusan</span>
  </div>

  <div class="e-head">
    <div>
      <h1 class="e-title">Edit Jurusan @if($proLocked) <span class="pl-pro-badge"><x-hi name="lock" /> Fitur PRO</span> @endif</h1>
      <p class="e-meta">Perbarui data jurusan dan kuota per jalur.</p>
    </div>
    <a href="{{ route('admin.majors.index') }}" class="e-btn ghost"><x-hi name="arrow-left-01" style="font-size:11px;color:var(--coral);" /> Kembali</a>
  </div>

  @if ($errors->any())
    <div class="e-alert error">
      <x-hi name="alert-02" />
      <div>
        <strong>Periksa kembali isian Anda:</strong>
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  <form action="{{ route('admin.majors.update', $major) }}" method="POST" id="majorForm">
    @csrf
    @method('PATCH')

    @if($proLocked)
    <div class="pl-lock-box">
      <div class="pl-lock-fields">
    @endif
    <div class="e-sec">
      <div class="e-sec-head">
        <span class="e-sec-ic"><x-hi name="mortarboard-01" /></span>
        <span class="e-sec-title">Data Jurusan</span>
      </div>
      <div class="e-grid">
        <div class="e-field">
          <label>Jenjang <span class="req">*</span></label>
          <select name="school_level_id" id="school_level_id" required class="e-input" onchange="filterSchools()">
            @foreach($levels as $level)
              <option value="{{ $level->id }}" {{ old('school_level_id', $major->school_level_id) == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
            @endforeach
          </select>
          @error('school_level_id')<p class="e-err">{{ $message }}</p>@enderror
        </div>

        <div class="e-field">
          <label>Sekolah <span class="req">*</span></label>
          <select name="school_id" id="school_id" required class="e-input">
            @foreach($schools as $school)
              <option value="{{ $school->id }}" data-levels="{{ $school->schoolLevels->pluck('id')->join(',') }}" {{ old('school_id', $major->school_id) == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
            @endforeach
          </select>
          @error('school_id')<p class="e-err">{{ $message }}</p>@enderror
        </div>

        <div class="e-field">
          <label>Nama Jurusan <span class="req">*</span></label>
          <input type="text" name="name" value="{{ old('name', $major->name) }}" required maxlength="255" class="e-input">
          @error('name')<p class="e-err">{{ $message }}</p>@enderror
        </div>

        <div class="e-field">
          <label>Kode <span class="req">*</span></label>
          <input type="text" name="code" value="{{ old('code', $major->code) }}" required maxlength="50" class="e-input" style="text-transform:uppercase;">
          <p class="e-hint">Kode harus unik dalam satu sekolah.</p>
          @error('code')<p class="e-err">{{ $message }}</p>@enderror
        </div>

        <div class="e-field">
          <label>Status</label>
          <label class="e-toggle">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $major->is_active) ? 'checked' : '' }}>
            <span class="e-switch"></span>
            <span>Aktif (menerima pendaftaran)</span>
          </label>
          @error('is_active')<p class="e-err">{{ $message }}</p>@enderror
        </div>

        <div class="e-field">
          <label>Urutan Tampil</label>
          <input type="number" name="order" value="{{ old('order', $major->order) }}" min="0" class="e-input" placeholder="e.g. 1">
          <p class="e-hint">Opsional. Nilai lebih kecil tampil lebih dulu.</p>
          @error('order')<p class="e-err">{{ $message }}</p>@enderror
        </div>

        <div class="e-field full">
          <label>Kuota per Jalur</label>
          <div class="e-quota-box">
            <div class="e-quota-grid">
              @foreach($tracks as $t)
                @php $q = $major->trackQuotas->firstWhere('registration_track_id', $t->id)?->quota; @endphp
                <div class="e-field">
                  <label>{{ $t->name }}</label>
                  <input type="number" name="quota_track_{{ $t->id }}" value="{{ old('quota_track_'.$t->id, $q) }}" min="0" class="e-input quota-input" data-track="{{ $t->id }}" placeholder="0">
                  @error('quota_track_'.$t->id)<p class="e-err">{{ $message }}</p>@enderror
                </div>
              @endforeach
            </div>
            <div class="e-total">
              Total Kuota: <strong id="totalQuota">{{ $major->totalQuotaByTracks() ?: $major->quota }}</strong>
              <span>Total otomatis = jumlah kuota semua jalur.</span>
            </div>
          </div>
        </div>

        <div class="e-field full">
          <label>Deskripsi</label>
          <textarea name="description" rows="3" class="e-input">{{ old('description', $major->description) }}</textarea>
          @error('description')<p class="e-err">{{ $message }}</p>@enderror
        </div>
      </div>

      <div class="e-submit">
        <a href="{{ route('admin.majors.index') }}" class="e-btn ghost">Batal</a>
        <button type="submit" class="e-btn coral" id="saveBtn">
          <x-hi name="save" style="font-size:12px;" /> <span id="saveBtnText">Simpan Perubahan</span>
        </button>
      </div>
    </div>

    @if($proLocked)
      </div>
      <div class="pl-lock-shade" role="button" tabindex="0" aria-label="Buka info fitur PRO" data-pro-msg="Mengubah jurusan adalah fitur PRO. <b>Aktifkan lisensi</b> untuk mengubah jurusan.">
        <span class="pl-lock-chip"><x-hi name="lock" /> Fitur <b>PRO</b> Terkunci — klik untuk info</span>
      </div>
    </div>
    @endif
  </form>
</div>

@include('partials.pro-lock-modal')

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

@extends('layouts.dashboard')
@section('title', 'Tambah Jurusan')
@section('content')

@php
    $proLocked = ! ($_pv['licensed'] ?? true);
    $levelPicker = $levels->map(fn ($l) => ['v' => (string) $l->id, 'l' => $l->name])->values();
    $schoolPicker = $schools->sortBy('name')->map(function ($s) {
        return ['v' => (string) $s->id, 'l' => $s->name];
    })->values();
@endphp

<style>
  /* ===================== TAMBAH JURUSAN — Bringova (no cards, scoped) ===================== */
  .mjr {
    --coral: #FF6B6B;
    --coral-soft: #FFE5E3;
    --coral-2: #FF8E6E;
    --amber: #F59E0B;   --amber-soft: #FEF3C7;
    --green: #10B981;   --green-soft: #D1FAE5;
    --blue: #3B82F6;    --blue-soft: #DBEAFE;
    --red: #EF4444;     --red-soft: #FEE2E2;
    --gray: #6b7280;    --gray-soft: #F3F4F6;
    --ink: #1a1a2e;     --muted: #8a8f9d;
    --divider: rgba(26,26,46,.10);

    position: relative;
    border-radius: 24px;
    padding: 28px 28px 44px;
    background: #f6f7fb;
    max-width: 100%;
    overflow: hidden;
    box-sizing: border-box;
  }
  .mjr .s-inner { width: 100%; max-width: 1080px; margin: 0 auto; }

  /* ---------- header ---------- */
  .mjr .s-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; flex-wrap: wrap; }
  .mjr .s-crumb a { color: var(--coral); text-decoration: none; }
  .mjr .s-crumb a:hover { text-decoration: underline; }
  .mjr .s-crumb .sep { color: #d3d6de; }
  .mjr .s-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 20px; }
  .mjr .s-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 2px; }
  .mjr .s-meta { font-size: 13px; color: var(--muted); }

  /* ---------- buttons ---------- */
  .mjr .s-btn { display: inline-flex; align-items: center; gap: 7px; border: none; cursor: pointer; border-radius: 11px; padding: 10px 17px; font-size: 13px; font-weight: 700; text-decoration: none; transition: transform .15s ease, filter .15s ease, background-color .15s ease; }
  .mjr .s-btn:hover { transform: translateY(-1px); }
  .mjr .s-btn.coral { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 8px 18px -8px rgba(255,107,107,.6); }
  .mjr .s-btn.coral:hover { filter: brightness(1.04); }
  .mjr .s-btn.ghost { background: rgba(255,255,255,.6); color: var(--ink); box-shadow: 0 2px 10px -8px rgba(26,26,46,.3); }
  .mjr .s-btn.ghost:hover { background: #fff; color: var(--coral); }
  .mjr .s-btn.green { background: var(--green-soft); color: var(--green); }
  .mjr .s-btn.sm { padding: 6px 11px; font-size: 11.5px; border-radius: 9px; }

  /* ---------- alert ---------- */
  .mjr .s-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 18px; font-weight: 500; }
  .mjr .s-alert i { margin-top: 2px; }
  .mjr .s-alert.error { background: var(--red-soft); color: var(--red); }
  .mjr .s-alert ul { margin: 6px 0 0 18px; list-style: disc; }

  /* ---------- sections ---------- */
  .mjr .s-sec { border-top: 1px solid var(--divider); padding: 24px 0 6px; }
  .mjr .s-sec:first-of-type { border-top: none; padding-top: 4px; }
  .mjr .s-sec-head { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 18px; }
  .mjr .s-sec-ic { flex: 0 0 auto; width: 40px; height: 40px; border-radius: 13px; display: flex; align-items: center; justify-content: center; font-size: 16px; background: var(--coral-soft); color: var(--coral); }
  .mjr .s-sec-name { font-size: 16px; font-weight: 700; color: var(--ink); }
  .mjr .s-sec-desc { font-size: 12px; color: var(--muted); margin-top: 1px; }

  /* ---------- fields ---------- */
  .mjr .s-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px 20px; }
  .mjr .s-grid .full { grid-column: 1 / -1; }
  .mjr .s-grid .full-2 { grid-column: 1 / -1; }
  .mjr .s-field label { display: block; font-size: 13px; font-weight: 500; color: var(--ink); margin-bottom: 6px; }
  .mjr .s-field .req { color: var(--red); }

  /* ---------- inputs ---------- */
  .mjr .x-input-line { width: 100%; background: transparent; border: none; border-bottom: 1px solid rgba(26,26,46,.18); border-radius: 0; padding: 9px 4px; font-size: 13px; color: var(--ink); box-sizing: border-box; }
  .mjr .x-input-line:focus { outline: none; border-bottom-color: var(--coral); }
  .mjr .x-input-box { width: 100%; background: rgba(255,255,255,.35); border: 1px solid rgba(26,26,46,.14); border-radius: 11px; padding: 11px 13px; font-size: 13px; color: var(--ink); box-sizing: border-box; backdrop-filter: blur(8px); }
  .mjr .x-input-box:focus { outline: none; border-color: var(--coral); box-shadow: 0 0 0 4px rgba(255,107,107,.14); background: rgba(255,255,255,.55); }
  .mjr textarea.x-input-box { resize: vertical; min-height: 80px; font-family: inherit; }
  .mjr .s-hint { font-size: 11px; color: var(--muted); margin-top: 5px; }
  .mjr .s-err { font-size: 12px; color: var(--red); margin-top: 5px; }

  /* ---------- picker trigger ---------- */
  .mjr .r-pick { display: inline-flex; align-items: center; flex-wrap: nowrap; max-width: 100%; background: transparent; border: none; border-bottom: 1px solid rgba(26,26,46,.18); border-radius: 0; padding: 9px 4px; font-size: 13px; font-weight: 500; color: var(--ink); cursor: pointer; width: 100%; box-sizing: border-box; }
  .mjr .r-pick:hover, .mjr .r-pick:focus { border-bottom-color: var(--coral); outline: none; }
  .mjr .r-pick .pick-label { flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .mjr .r-pick .pick-label.is-placeholder { color: var(--muted); font-weight: 400; }
  .mjr .r-pick .pick-clear { flex: 0 0 auto; display: none; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 6px; background: var(--gray-soft); color: var(--gray); cursor: pointer; font-size: 9px; user-select: none; }
  .mjr .r-pick .pick-clear:hover { background: var(--red-soft); color: var(--red); }
  .mjr .r-pick.has-value .pick-clear { display: inline-flex; }
  .mjr .r-pick .pick-caret { display: none; }

  /* ---------- toggle (aktif) ---------- */
  .mjr .s-toggle { display: inline-flex; align-items: center; gap: 10px; cursor: pointer; user-select: none; }
  .mjr .s-toggle input { width: 18px; height: 18px; accent-color: var(--coral); }

  /* ---------- quota box ---------- */
  .mjr .s-quota { border: 1px solid rgba(26,26,46,.12); border-radius: 14px; padding: 16px; background: rgba(255,255,255,.35); }
  .mjr .s-quota-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
  .mjr .s-total { margin-top: 16px; background: rgba(255,255,255,.6); border: 1px solid rgba(26,26,46,.08); border-radius: 10px; padding: 11px 14px; font-size: 13px; color: var(--gray); }
  .mjr .s-total strong { font-size: 18px; color: var(--coral); }
  .mjr .s-total span { display: block; font-size: 11px; color: var(--muted); margin-top: 1px; }

  /* ---------- action bar (sticky) ---------- */
  .mjr .s-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 26px; flex-wrap: wrap; }

  /* ---------- picker modal ---------- */
  .mjr .picker-backdrop { position: fixed; inset: 0; z-index: 90; background: rgba(26,26,46,.36); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; padding: 16px; }
  .mjr .picker-backdrop.is-open { display: flex; }
  .mjr .picker-panel { width: 100%; max-width: 380px; background: #fff; border-radius: 18px; padding: 18px; box-shadow: 0 24px 60px -18px rgba(26,26,46,.4); animation: mjrPickerPop .2s cubic-bezier(.22,1.2,.36,1); }
  @keyframes mjrPickerPop { from { opacity: 0; transform: scale(.97) translateY(-4px); } to { opacity: 1; transform: scale(1) translateY(0); } }
  .mjr .picker-head { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
  .mjr .picker-head .picker-title { font-size: 15px; font-weight: 700; color: var(--ink); flex: 1; }
  .mjr .picker-head .picker-close { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 8px; border: none; background: transparent; color: var(--muted); cursor: pointer; font-size: 12px; }
  .mjr .picker-head .picker-close:hover { background: var(--gray-soft); color: var(--ink); }
  .mjr .picker-search { position: relative; margin-bottom: 8px; }
  .mjr .picker-search i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 12px; pointer-events: none; }
  .mjr .picker-search input { width: 100%; padding: 9px 12px 9px 32px; border: 1px solid rgba(26,26,46,.14); border-radius: 10px; font-size: 13px; color: var(--ink); box-sizing: border-box; }
  .mjr .picker-search input:focus { outline: none; border-color: var(--coral); box-shadow: 0 0 0 3px rgba(255,107,107,.12); }
  .mjr .picker-list { max-height: 320px; overflow-y: auto; padding: 4px 0; }
  .mjr .picker-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border-radius: 10px; cursor: pointer; font-size: 13px; color: var(--ink); }
  .mjr .picker-item:hover { background: var(--coral-soft); color: var(--coral); }
  .mjr .picker-item.is-selected { background: var(--coral); color: #fff; font-weight: 600; }
  .mjr .picker-item .pi-check { font-size: 11px; opacity: 0; }
  .mjr .picker-item.is-selected .pi-check { opacity: 1; }
  .mjr .picker-empty { padding: 26px 12px; text-align: center; color: var(--muted); font-size: 12.5px; }
  .mjr .picker-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: 12px; }
  .mjr .picker-foot .picker-clear-all { padding: 7px 12px; border-radius: 9px; border: none; background: transparent; color: var(--muted); font-size: 12px; font-weight: 600; cursor: pointer; }
  .mjr .picker-foot .picker-clear-all:hover { color: var(--red); background: var(--red-soft); }
  .mjr .picker-foot .picker-done { padding: 7px 16px; border-radius: 9px; border: none; background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; font-size: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 6px 14px -6px rgba(255,107,107,.55); }

  /* ---------- responsive ---------- */
  @media (max-width: 640px) {
    .mjr { padding: 20px 14px 32px; }
    .mjr .s-head { flex-direction: column; align-items: stretch; gap: 12px; margin-top: 8px; }
    .mjr .s-title { font-size: 22px; }
    .mjr .s-grid { grid-template-columns: 1fr; }
    .mjr .s-quota-grid { grid-template-columns: 1fr; }
  }
</style>

<div class="mjr">
  <div class="s-inner">
  <div class="s-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <a href="{{ route('admin.majors.index') }}">Kelola Jurusan</a>
    <span class="sep">/</span>
    <span>Tambah Jurusan</span>
  </div>

  <div class="s-head">
    <div>
      <h1 class="s-title">Tambah Jurusan
        @if($proLocked) <a href="https://shop.hrry.win" target="_blank" rel="noopener" class="pl-pro-badge" style="text-decoration:none"><x-hi name="lock" /> Akses Terbatas</a> @endif
      </h1>
      <p class="s-meta">Tambahkan jurusan baru beserta kuota per jalur.</p>
    </div>
    <a href="{{ route('admin.majors.index') }}" class="s-btn ghost sm"><x-hi name="arrow-left-01" /> Kembali</a>
  </div>

  @if ($errors->any())
  <div class="s-alert error">
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

  <form action="{{ route('admin.majors.store') }}" method="POST" id="majorForm">
    @csrf

    @if($proLocked)
    <div class="pl-lock-box">
      <div class="pl-lock-fields">
    @endif

    {{-- ================== SEKOLAH & JENJANG ================== --}}
    <div class="s-sec">
      <div class="s-sec-head">
        <span class="s-sec-ic"><x-hi name="school" /></span>
        <div>
          <div class="s-sec-name">Sekolah &amp; Jenjang</div>
          <div class="s-sec-desc">Pilih jenjang dulu, lalu pilih sekolah yang melayaninya.</div>
        </div>
      </div>

      <div class="s-grid">
        <div class="s-field">
          <label>Jenjang <span class="req">*</span></label>
          <button type="button" class="r-pick" data-picker="mjr_level" id="mjrLevelTrig" aria-haspopup="listbox">
            <span class="pick-label is-placeholder">Pilih jenjang</span>
            <span class="pick-clear" data-clear="mjr_level" role="button" tabindex="0"><i class="fa-solid fa-xmark"></i></span>
          </button>
          <input type="hidden" name="school_level_id" data-picker-input="mjr_level" id="school_level_id" value="{{ old('school_level_id') }}">
          @error('school_level_id')<p class="s-err">{{ $message }}</p>@enderror
        </div>

        <div class="s-field">
          <label>Sekolah <span class="req">*</span></label>
          <button type="button" class="r-pick" data-picker="mjr_school" id="mjrSchoolTrig" aria-haspopup="listbox">
            <span class="pick-label is-placeholder">Pilih sekolah</span>
            <span class="pick-clear" data-clear="mjr_school" role="button" tabindex="0"><i class="fa-solid fa-xmark"></i></span>
          </button>
          <input type="hidden" name="school_id" data-picker-input="mjr_school" id="school_id" value="{{ old('school_id') }}">
          @error('school_id')<p class="s-err">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    {{-- ================== DATA JURUSAN ================== --}}
    <div class="s-sec">
      <div class="s-sec-head">
        <span class="s-sec-ic"><x-hi name="mortarboard-01" /></span>
        <div>
          <div class="s-sec-name">Data Jurusan</div>
          <div class="s-sec-desc">Nama, kode, status, dan urutan tampil.</div>
        </div>
      </div>

      <div class="s-grid">
        <div class="s-field">
          <label>Nama Jurusan <span class="req">*</span></label>
          <input type="text" name="name" value="{{ old('name') }}" required maxlength="255" placeholder="e.g. Rekayasa Perangkat Lunak" class="x-input-line">
          @error('name')<p class="s-err">{{ $message }}</p>@enderror
        </div>

        <div class="s-field">
          <label>Kode <span class="req">*</span></label>
          <input type="text" name="code" value="{{ old('code') }}" required maxlength="50" placeholder="e.g. RPL" class="x-input-line" style="text-transform:uppercase;">
          <p class="s-hint">Kode harus unik dalam satu sekolah.</p>
          @error('code')<p class="s-err">{{ $message }}</p>@enderror
        </div>

        <div class="s-field">
          <label>Status</label>
          <div style="display:flex;gap:18px;align-items:center;padding-top:8px;">
            <label class="s-toggle">
              <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
              <span>Aktif (menerima pendaftaran)</span>
            </label>
          </div>
          @error('is_active')<p class="s-err">{{ $message }}</p>@enderror
        </div>

        <div class="s-field">
          <label>Urutan Tampil</label>
          <input type="number" name="order" value="{{ old('order') }}" min="0" class="x-input-line" placeholder="e.g. 1">
          <p class="s-hint">Opsional. Nilai lebih kecil tampil lebih dulu.</p>
          @error('order')<p class="s-err">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    {{-- ================== KUOTA PER JALUR ================== --}}
    <div class="s-sec">
      <div class="s-sec-head">
        <span class="s-sec-ic"><x-hi name="layers-01" /></span>
        <div>
          <div class="s-sec-name">Kuota per Jalur</div>
          <div class="s-sec-desc">Jumlah kuota pendaftaran untuk tiap jalur.</div>
        </div>
      </div>

      <div class="s-quota">
        <div class="s-quota-grid">
          @foreach($tracks as $t)
            <div class="s-field">
              <label>{{ $t->name }}</label>
              <input type="number" name="quota_track_{{ $t->id }}" value="{{ old('quota_track_'.$t->id) }}" min="0" class="x-input-line quota-input" data-track="{{ $t->id }}" placeholder="0">
              @error('quota_track_'.$t->id)<p class="s-err">{{ $message }}</p>@enderror
            </div>
          @endforeach
        </div>
        <div class="s-total">
          Total Kuota: <strong id="totalQuota">0</strong>
          <span>Total otomatis = jumlah kuota semua jalur.</span>
        </div>
      </div>
    </div>

    {{-- ================== DESKRIPSI ================== --}}
    <div class="s-sec">
      <div class="s-sec-head">
        <span class="s-sec-ic"><x-hi name="note-01" /></span>
        <div>
          <div class="s-sec-name">Deskripsi</div>
          <div class="s-sec-desc">Informasi tambahan jurusan (opsional).</div>
        </div>
      </div>

      <div class="s-grid">
        <div class="s-field full-2">
          <textarea name="description" rows="3" class="x-input-box">{{ old('description') }}</textarea>
          @error('description')<p class="s-err">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    <div class="s-actions">
      <a href="{{ route('admin.majors.index') }}" class="s-btn ghost">Batal</a>
      <button type="submit" class="s-btn coral" id="saveBtn">
        <x-hi name="save" /> <span id="saveBtnText">Tambah Jurusan</span>
      </button>
    </div>

    @if($proLocked)
      </div>
      <a href="https://shop.hrry.win" target="_blank" rel="noopener" class="pl-lock-shade" role="button" tabindex="0" aria-label="Buka info Akses Terbatas" data-pro-msg="Menambah jurusan adalah Akses Terbatas. <b>Aktifkan lisensi</b> untuk menambahkan jurusan baru.">
        <span class="pl-lock-chip"><x-hi name="lock" /> <b>Akses Terbatas</b> — klik untuk info</span>
      </a>
    </div>
    @endif
  </form>
  </div>

{{-- ================== PICKER MODAL (Bringova) ================== --}}
<div id="pickerBackdrop" class="picker-backdrop" aria-hidden="true">
  <div class="picker-panel" role="dialog">
    <div class="picker-head"><div class="picker-title" id="pickerTitle"></div>
      <button class="picker-close" onclick="closePicker()"><i class="fa-solid fa-xmark"></i></button></div>
    <div class="picker-search"><i class="fa-solid fa-magnifying-glass"></i>
      <input id="pickerSearch" type="search" placeholder="Cariâ€¦" autocomplete="off"></div>
    <div class="picker-list" id="pickerList" role="listbox"></div>
    <div class="picker-foot"><button class="picker-clear-all" onclick="clearCurrentPicker()">Bersihkan</button>
      <button class="picker-done" onclick="closePicker()">Selesai</button></div>
  </div>
</div>
@php
    $levelAttach = $schools->sortBy('name')->map(fn ($s) => ['id' => (string) $s->id, 'levels' => $s->schoolLevels->pluck('id')->map(fn ($i) => (string) $i)->values()]);
@endphp
<div id="reg-data" hidden data-picker='@json(['mjr_level' => $levelPicker, 'mjr_school' => $schoolPicker])' data-picker-labels='@json(['mjr_level' => 'Jenjang', 'mjr_school' => 'Sekolah'])'
     data-school-levels='@json($levelAttach)'></div>
</div>

@include('partials.pro-lock-modal')

<script>
(function () {
  // ---- data pendukung for maju-mundur filtering ----
  var schoolLevels = {};
  try {
    var slEl = document.getElementById('reg-data');
    schoolLevels = JSON.parse(slEl.getAttribute('data-school-levels') || '{}');
  } catch (e) { schoolLevels = {}; }

  var levelInput = document.getElementById('school_level_id');
  var schoolInput = document.getElementById('school_id');

  // diminimalkan: filter data sekolah di picker sesuai jenjang terpilih (maju-mundur)
  function filterSchoolsByLevel() {
    if (!window.__pickerData) return;
    var lv = levelInput.value;
    var all = window.__pickerFullSchools || [];
    if (!all.length) {
      all = (window.__pickerData['mjr_school'] || []).slice();
      window.__pickerFullSchools = all;
    }
    var filtered;
    if (!lv) {
      filtered = all.slice();
    } else {
      filtered = all.filter(function (s) {
        var info = schoolLevels[s.v] || [];
        return info.indexOf(String(lv)) !== -1;
      });
    }
    window.__pickerData['mjr_school'] = filtered;

    // jika sekolah yang terpilih tak lagi cocok, kosongkan
    var sv = schoolInput.value;
    if (sv) {
      var stillValid = filtered.some(function (s) { return String(s.v) === String(sv); });
      if (!stillValid) {
        schoolInput.value = '';
        var trig = document.getElementById('mjrSchoolTrig');
        if (trig) {
          trig.classList.remove('has-value');
          var lab = trig.querySelector('.pick-label');
          if (lab) { lab.textContent = 'Pilih sekolah'; lab.classList.add('is-placeholder'); }
        }
      }
    }
  }

  if (levelInput) levelInput.addEventListener('change', filterSchoolsByLevel);

  // ---- total quota ----
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
  updateTotal();

  // ---- submit ----
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

@extends('layouts.dashboard')

@section('title', 'Tambah Sekolah')

@section('content')
@php
    $oldLevelIds = old('school_level_ids', []);
    $oldLevelIds = is_array($oldLevelIds) ? $oldLevelIds : [];
    $proLocked = ! ($_pv['licensed'] ?? true);
    $statusOptions = [
        ['v' => 'negeri', 'l' => 'Negeri'],
        ['v' => 'swasta', 'l' => 'Swasta'],
    ];
    $accOptions = array_map(fn ($a) => ['v' => $a, 'l' => $a], ['A', 'B', 'C', 'Belum Terakreditasi']);
@endphp

<style>
  /* ===================== TAMBAH SEKOLAH — Bringova (no cards, scoped) ===================== */
  .skl {
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
  .skl .s-inner { width: 100%; max-width: 1080px; margin: 0 auto; }

  /* ---------- header ---------- */
  .skl .s-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; flex-wrap: wrap; }
  .skl .s-crumb a { color: var(--coral); text-decoration: none; }
  .skl .s-crumb a:hover { text-decoration: underline; }
  .skl .s-crumb .sep { color: #d3d6de; }
  .skl .s-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 20px; }
  .skl .s-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 2px; }
  .skl .s-meta { font-size: 13px; color: var(--muted); }

  /* ---------- buttons ---------- */
  .skl .s-btn { display: inline-flex; align-items: center; gap: 7px; border: none; cursor: pointer; border-radius: 11px; padding: 10px 17px; font-size: 13px; font-weight: 700; text-decoration: none; transition: transform .15s ease, filter .15s ease, background-color .15s ease; }
  .skl .s-btn:hover { transform: translateY(-1px); }
  .skl .s-btn.coral { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 8px 18px -8px rgba(255,107,107,.6); }
  .skl .s-btn.coral:hover { filter: brightness(1.04); }
  .skl .s-btn.ghost { background: rgba(255,255,255,.6); color: var(--ink); box-shadow: 0 2px 10px -8px rgba(26,26,46,.3); }
  .skl .s-btn.ghost:hover { background: #fff; color: var(--coral); }
  .skl .s-btn.sm { padding: 6px 11px; font-size: 11.5px; border-radius: 9px; }

  /* ---------- alert ---------- */
  .skl .s-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 18px; font-weight: 500; }
  .skl .s-alert i { margin-top: 2px; }
  .skl .s-alert.error { background: var(--red-soft); color: var(--red); }
  .skl .s-alert ul { margin: 6px 0 0 18px; list-style: disc; }

  /* ---------- sections (divider, no card) ---------- */
  .skl .s-sec { border-top: 1px solid var(--divider); padding: 24px 0 6px; }
  .skl .s-sec:first-of-type { border-top: none; padding-top: 4px; }
  .skl .s-sec-head { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 18px; }
  .skl .s-sec-ic { flex: 0 0 auto; width: 40px; height: 40px; border-radius: 13px; display: flex; align-items: center; justify-content: center; font-size: 16px; background: var(--coral-soft); color: var(--coral); }
  .skl .s-sec-name { font-size: 16px; font-weight: 700; color: var(--ink); }
  .skl .s-sec-desc { font-size: 12px; color: var(--muted); margin-top: 1px; }

  /* ---------- fields grid ---------- */
  .skl .s-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px 20px; }
  .skl .s-grid .full { grid-column: 1 / -1; }
  .skl .s-field label { display: block; font-size: 13px; font-weight: 500; color: var(--ink); margin-bottom: 6px; }
  .skl .s-field .req { color: var(--red); }

  /* ---------- inputs ---------- */
  .skl .x-input-line { width: 100%; background: transparent; border: none; border-bottom: 1px solid rgba(26,26,46,.18); border-radius: 0; padding: 9px 4px; font-size: 13px; color: var(--ink); box-sizing: border-box; }
  .skl .x-input-line:focus { outline: none; border-bottom-color: var(--coral); }
  .skl .x-input-box { width: 100%; background: rgba(255,255,255,.35); border: 1px solid rgba(26,26,46,.14); border-radius: 11px; padding: 11px 13px; font-size: 13px; color: var(--ink); box-sizing: border-box; backdrop-filter: blur(8px); }
  .skl .x-input-box:focus { outline: none; border-color: var(--coral); box-shadow: 0 0 0 4px rgba(255,107,107,.14); background: rgba(255,255,255,.55); }
  .skl textarea.x-input-box { resize: vertical; min-height: 80px; font-family: inherit; }
  .skl .s-hint { font-size: 11px; color: var(--muted); margin-top: 5px; }
  .skl .s-err { font-size: 12px; color: var(--red); margin-top: 5px; }

  /* ---------- picker trigger ---------- */
  .skl .r-pick { display: inline-flex; align-items: center; flex-wrap: nowrap; max-width: 100%; background: transparent; border: none; border-bottom: 1px solid rgba(26,26,46,.18); border-radius: 0; padding: 9px 4px; font-size: 13px; font-weight: 500; color: var(--ink); cursor: pointer; width: 100%; box-sizing: border-box; }
  .skl .r-pick:hover, .skl .r-pick:focus { border-bottom-color: var(--coral); outline: none; }
  .skl .r-pick .pick-label { flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .skl .r-pick .pick-label.is-placeholder { color: var(--muted); font-weight: 400; }
  .skl .r-pick .pick-clear { flex: 0 0 auto; display: none; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 6px; background: var(--gray-soft); color: var(--gray); cursor: pointer; font-size: 9px; user-select: none; }
  .skl .r-pick .pick-clear:hover { background: var(--red-soft); color: var(--red); }
  .skl .r-pick.has-value .pick-clear { display: inline-flex; }
  .skl .r-pick .pick-caret { display: none; }

  /* ---------- toggle chips (jenjang checkbox) ---------- */
  .skl .s-chips { display: flex; flex-wrap: wrap; gap: 10px; }
  .skl .s-chip { display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(26,26,46,.14); border-radius: 12px; padding: 10px 15px; cursor: pointer; background: rgba(255,255,255,.4); font-size: 13px; font-weight: 500; color: var(--ink); transition: all .15s ease; user-select: none; }
  .skl .s-chip:hover { border-color: var(--coral); }
  .skl .s-chip input { width: 17px; height: 17px; accent-color: var(--coral); }
  .skl .s-chip.is-on { border-color: var(--coral); background: var(--coral-soft); color: var(--coral); }

  /* ---------- logo upload (circle preview) ---------- */
  .skl .s-logo-wrap { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
  .skl .s-logo-preview { width: 120px; height: 120px; border-radius: 14px; border: 2px dashed rgba(26,26,46,.18); background: rgba(255,255,255,.5); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; }
  .skl .s-logo-preview .ph { font-size: 11px; color: var(--muted); text-align: center; padding: 8px; }
  .skl .s-logo-preview img { width: 100%; height: 100%; object-fit: contain; }
  .skl .s-logo-actions { display: flex; flex-direction: column; gap: 8px; }
  .skl .file-btn { display: inline-flex; align-items: center; gap: 6px; cursor: pointer; }

  /* ---------- action bar (sticky) ---------- */
  .skl .s-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 26px; flex-wrap: wrap; }

  /* ---------- picker modal (Bringova, scoped) ---------- */
  .skl .picker-backdrop { position: fixed; inset: 0; z-index: 90; background: rgba(26,26,46,.36); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; padding: 16px; }
  .skl .picker-backdrop.is-open { display: flex; }
  .skl .picker-panel { width: 100%; max-width: 380px; background: #fff; border-radius: 18px; padding: 18px; box-shadow: 0 24px 60px -18px rgba(26,26,46,.4); animation: sklPickerPop .2s cubic-bezier(.22,1.2,.36,1); }
  @keyframes sklPickerPop { from { opacity: 0; transform: scale(.97) translateY(-4px); } to { opacity: 1; transform: scale(1) translateY(0); } }
  .skl .picker-head { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
  .skl .picker-head .picker-title { font-size: 15px; font-weight: 700; color: var(--ink); flex: 1; }
  .skl .picker-head .picker-close { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 8px; border: none; background: transparent; color: var(--muted); cursor: pointer; font-size: 12px; }
  .skl .picker-head .picker-close:hover { background: var(--gray-soft); color: var(--ink); }
  .skl .picker-search { position: relative; margin-bottom: 8px; }
  .skl .picker-search i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 12px; pointer-events: none; }
  .skl .picker-search input { width: 100%; padding: 9px 12px 9px 32px; border: 1px solid rgba(26,26,46,.14); border-radius: 10px; font-size: 13px; color: var(--ink); box-sizing: border-box; }
  .skl .picker-search input:focus { outline: none; border-color: var(--coral); box-shadow: 0 0 0 3px rgba(255,107,107,.12); }
  .skl .picker-list { max-height: 320px; overflow-y: auto; padding: 4px 0; }
  .skl .picker-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border-radius: 10px; cursor: pointer; font-size: 13px; color: var(--ink); }
  .skl .picker-item:hover { background: var(--coral-soft); color: var(--coral); }
  .skl .picker-item.is-selected { background: var(--coral); color: #fff; font-weight: 600; }
  .skl .picker-item .pi-check { font-size: 11px; opacity: 0; }
  .skl .picker-item.is-selected .pi-check { opacity: 1; }
  .skl .picker-empty { padding: 26px 12px; text-align: center; color: var(--muted); font-size: 12.5px; }
  .skl .picker-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: 12px; }
  .skl .picker-foot .picker-clear-all { padding: 7px 12px; border-radius: 9px; border: none; background: transparent; color: var(--muted); font-size: 12px; font-weight: 600; cursor: pointer; }
  .skl .picker-foot .picker-clear-all:hover { color: var(--red); background: var(--red-soft); }
  .skl .picker-foot .picker-done { padding: 7px 16px; border-radius: 9px; border: none; background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; font-size: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 6px 14px -6px rgba(255,107,107,.55); }

  /* ---------- responsive ---------- */
  @media (max-width: 640px) {
    .skl { padding: 20px 14px 32px; }
    .skl .s-head { flex-direction: column; align-items: stretch; gap: 12px; margin-top: 8px; }
    .skl .s-title { font-size: 22px; }
    .skl .s-grid { grid-template-columns: 1fr; }
  }
</style>

<div class="skl">
  <div class="s-inner">
  <div class="s-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <a href="{{ route('admin.schools.index') }}">Sekolah</a>
    <span class="sep">/</span>
    <span>Tambah Sekolah</span>
  </div>

  <div class="s-head">
    <div>
      <h1 class="s-title">Tambah Sekolah
        @if($proLocked) <span class="pl-pro-badge"><x-hi name="lock" /> Fitur PRO</span> @endif
      </h1>
      <p class="s-meta">Tambahkan profil sekolah baru yang ditampilkan pada form pendaftaran siswa.</p>
    </div>
    <a href="{{ route('admin.schools.index') }}" class="s-btn ghost sm"><x-hi name="arrow-left-01" /> Kembali</a>
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

  <form action="{{ route('admin.schools.store') }}" method="POST" enctype="multipart/form-data" id="schoolForm">
    @csrf

    @if($proLocked)
    <div class="pl-lock-box">
      <div class="pl-lock-fields">
    @endif

    {{-- ================== INFORMASI DASAR ================== --}}
    <div class="s-sec">
      <div class="s-sec-head">
        <span class="s-sec-ic"><x-hi name="bank" /></span>
        <div>
          <div class="s-sec-name">Informasi Dasar</div>
          <div class="s-sec-desc">Identitas utama sekolah.</div>
        </div>
      </div>

      <div class="s-grid">
        <div class="s-field">
          <label>Nama Sekolah <span class="req">*</span></label>
          <input type="text" name="name" value="{{ old('name') }}" required maxlength="255" class="x-input-line">
          @error('name')<p class="s-err">{{ $message }}</p>@enderror
        </div>

        <div class="s-field">
          <label>NPSN <span class="req">*</span></label>
          <input type="text" name="npsn" value="{{ old('npsn') }}" required maxlength="8" inputmode="numeric" pattern="[0-9]{8}" title="NPSN harus 8 digit angka" class="x-input-line" id="npsnInput">
          <p class="s-hint">8 digit angka.</p>
          @error('npsn')<p class="s-err">{{ $message }}</p>@enderror
        </div>

        <div class="s-field">
          <label>Status Sekolah</label>
          <button type="button" class="r-pick" data-picker="school_status" aria-haspopup="listbox">
            <span class="pick-label is-placeholder">Pilih status</span>
            <span class="pick-clear" data-clear="school_status" role="button" tabindex="0"><i class="fa-solid fa-xmark"></i></span>
          </button>
          <input type="hidden" name="school_status" data-picker-input="school_status" value="{{ old('school_status') }}">
          @error('school_status')<p class="s-err">{{ $message }}</p>@enderror
        </div>

        <div class="s-field">
          <label>Akreditasi</label>
          <button type="button" class="r-pick" data-picker="accreditation" aria-haspopup="listbox">
            <span class="pick-label is-placeholder">Pilih akreditasi</span>
            <span class="pick-clear" data-clear="accreditation" role="button" tabindex="0"><i class="fa-solid fa-xmark"></i></span>
          </button>
          <input type="hidden" name="accreditation" data-picker-input="accreditation" value="{{ old('accreditation') }}">
          @error('accreditation')<p class="s-err">{{ $message }}</p>@enderror
        </div>

        <div class="s-field full">
          <label>Kepala Sekolah</label>
          <input type="text" name="principal_name" value="{{ old('principal_name') }}" maxlength="255" class="x-input-line">
          @error('principal_name')<p class="s-err">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    {{-- ================== KONTAK ================== --}}
    <div class="s-sec">
      <div class="s-sec-head">
        <span class="s-sec-ic"><x-hi name="call" /></span>
        <div>
          <div class="s-sec-name">Kontak</div>
          <div class="s-sec-desc">Informasi kontak yang dapat dihubungi.</div>
        </div>
      </div>

      <div class="s-grid">
        <div class="s-field">
          <label>Telepon</label>
          <input type="text" name="phone" value="{{ old('phone') }}" maxlength="50" class="x-input-line" inputmode="numeric">
          @error('phone')<p class="s-err">{{ $message }}</p>@enderror
        </div>

        <div class="s-field">
          <label>WhatsApp</label>
          <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" maxlength="50" class="x-input-line" inputmode="numeric" placeholder="08xxxxxxxxxx">
          @error('whatsapp')<p class="s-err">{{ $message }}</p>@enderror
        </div>

        <div class="s-field">
          <label>Email</label>
          <input type="email" name="email" value="{{ old('email') }}" maxlength="255" class="x-input-line">
          @error('email')<p class="s-err">{{ $message }}</p>@enderror
        </div>

        <div class="s-field">
          <label>Website Sekolah</label>
          <input type="url" name="website" value="{{ old('website') }}" maxlength="255" class="x-input-line" placeholder="https://...">
          <p class="s-hint">Opsional. Harus diawali http:// atau https://</p>
          @error('website')<p class="s-err">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    {{-- ================== ALAMAT ================== --}}
    <div class="s-sec">
      <div class="s-sec-head">
        <span class="s-sec-ic"><x-hi name="location-01" /></span>
        <div>
          <div class="s-sec-name">Alamat</div>
          <div class="s-sec-desc">Alamat lengkap sekolah.</div>
        </div>
      </div>

      <div class="s-grid">
        <div class="s-field full">
          <label>Alamat Lengkap</label>
          <textarea name="address" rows="2" class="x-input-box">{{ old('address') }}</textarea>
          @error('address')<p class="s-err">{{ $message }}</p>@enderror
        </div>

        <div class="s-field">
          <label>Kecamatan</label>
          <input type="text" name="district" value="{{ old('district') }}" maxlength="255" class="x-input-line">
          @error('district')<p class="s-err">{{ $message }}</p>@enderror
        </div>

        <div class="s-field">
          <label>Kota/Kabupaten</label>
          <input type="text" name="city" value="{{ old('city') }}" maxlength="255" class="x-input-line">
          @error('city')<p class="s-err">{{ $message }}</p>@enderror
        </div>

        <div class="s-field">
          <label>Provinsi</label>
          <input type="text" name="province" value="{{ old('province') }}" maxlength="255" class="x-input-line">
          @error('province')<p class="s-err">{{ $message }}</p>@enderror
        </div>

        <div class="s-field">
          <label>Link Google Maps</label>
          <input type="url" name="maps_link" value="{{ old('maps_link') }}" maxlength="255" class="x-input-line" placeholder="https://maps.google.com/...">
          <p class="s-hint">Opsional. Harus diawali http:// atau https://</p>
          @error('maps_link')<p class="s-err">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    {{-- ================== BRANDING ================== --}}
    <div class="s-sec">
      <div class="s-sec-head">
        <span class="s-sec-ic"><x-hi name="colors" /></span>
        <div>
          <div class="s-sec-name">Branding</div>
          <div class="s-sec-desc">Logo dan deskripsi singkat sekolah.</div>
        </div>
      </div>

      <div class="s-grid">
        <div class="s-field full">
          <label>Logo Sekolah</label>
          <div class="s-logo-wrap">
            <div class="s-logo-preview" id="logoPreview"><span class="ph">Belum ada<br>logo</span></div>
            <div class="s-logo-actions">
              <label class="s-btn ghost sm file-btn">
                <x-hi name="upload-01" /> Pilih File
                <input type="file" name="logo" id="logoInput" accept="image/jpeg,image/png" style="display:none;">
              </label>
              <p class="s-hint" style="margin:0;">JPG/PNG, maksimal 2MB.</p>
              <p class="s-err" id="logoError" style="display:none;"></p>
              @error('logo')<p class="s-err">{{ $message }}</p>@enderror
            </div>
          </div>
        </div>

        <div class="s-field full">
          <label>Deskripsi Singkat Sekolah</label>
          <textarea name="description" rows="3" maxlength="500" class="x-input-box" id="descInput">{{ old('description') }}</textarea>
          <p class="s-hint"><span id="descCount">0</span>/500 karakter (opsional).</p>
          @error('description')<p class="s-err">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    {{-- ================== JENJANG YANG DILAYANI ================== --}}
    <div class="s-sec">
      <div class="s-sec-head">
        <span class="s-sec-ic"><x-hi name="mortarboard-01" /></span>
        <div>
          <div class="s-sec-name">Jenjang yang Dilayani <span class="req" style="color:var(--red);">*</span></div>
          <div class="s-sec-desc">Centang jenjang pendidikan yang menerima pendaftaran di sekolah ini.</div>
        </div>
      </div>

      <div class="s-chips">
        @foreach ($levels as $level)
          <label class="s-chip {{ in_array($level->id, $oldLevelIds) ? 'is-on' : '' }}">
            <input type="checkbox" name="school_level_ids[]" value="{{ $level->id }}"
              {{ in_array($level->id, $oldLevelIds) ? 'checked' : '' }}>
            <span>{{ $level->name }}</span>
          </label>
        @endforeach
      </div>
      @error('school_level_ids')<p class="s-err">{{ $message }}</p>@enderror
    </div>

    <div class="s-actions">
      <a href="{{ route('admin.schools.index') }}" class="s-btn ghost">Batal</a>
      <button type="submit" class="s-btn coral" id="saveBtn">
        <x-hi name="save" /> <span id="saveBtnText">Simpan Sekolah</span>
      </button>
    </div>

    @if($proLocked)
      </div>
      <div class="pl-lock-shade" role="button" tabindex="0" aria-label="Buka info fitur PRO" data-pro-msg="Menambah sekolah adalah fitur PRO. <b>Aktifkan lisensi</b> untuk menambahkan sekolah baru.">
        <span class="pl-lock-chip"><x-hi name="lock" /> Fitur <b>PRO</b> Terkunci — klik untuk info</span>
      </div>
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
      <input id="pickerSearch" type="search" placeholder="Cari…" autocomplete="off"></div>
    <div class="picker-list" id="pickerList" role="listbox"></div>
    <div class="picker-foot"><button class="picker-clear-all" onclick="clearCurrentPicker()">Bersihkan</button>
      <button class="picker-done" onclick="closePicker()">Selesai</button></div>
  </div>
</div>
<div id="reg-data" hidden data-picker='@json(['school_status' => $statusOptions, 'accreditation' => $accOptions])' data-picker-labels='@json(['school_status' => 'Status Sekolah', 'accreditation' => 'Akreditasi'])'></div>
</div>

@include('partials.pro-lock-modal')

<script>
(function () {
  var form = document.getElementById('schoolForm');
  var logoInput = document.getElementById('logoInput');
  var logoPreview = document.getElementById('logoPreview');
  var logoError = document.getElementById('logoError');
  var saveBtn = document.getElementById('saveBtn');
  var saveBtnText = document.getElementById('saveBtnText');
  var descInput = document.getElementById('descInput');
  var descCount = document.getElementById('descCount');
  var npsnInput = document.getElementById('npsnInput');

  function showLogoError(msg) {
    logoError.textContent = msg;
    logoError.style.display = 'block';
  }
  function hideLogoError() {
    logoError.style.display = 'none';
  }

  if (logoInput) {
    logoInput.addEventListener('change', function () {
      var file = logoInput.files[0];
      hideLogoError();
      if (!file) return;
      if (['image/jpeg', 'image/png'].indexOf(file.type) === -1) {
        showLogoError('Hanya JPG/PNG yang diperbolehkan.');
        logoInput.value = '';
        return;
      }
      if (file.size > 2 * 1024 * 1024) {
        showLogoError('Ukuran file melebihi 2MB.');
        logoInput.value = '';
        return;
      }
      var reader = new FileReader();
      reader.onload = function (e) {
        logoPreview.innerHTML = '<img src="' + e.target.result + '" alt="Preview logo">';
      };
      reader.readAsDataURL(file);
    });
  }

  if (npsnInput) {
    npsnInput.addEventListener('input', function () {
      npsnInput.value = npsnInput.value.replace(/[^0-9]/g, '').slice(0, 8);
    });
  }

  function updateDescCount() { descCount.textContent = descInput.value.length; }
  if (descInput) {
    updateDescCount();
    descInput.addEventListener('input', updateDescCount);
  }

  // toggle chip styling on checkbox change
  document.querySelectorAll('.s-chip input').forEach(function (chk) {
    chk.addEventListener('change', function () {
      chk.closest('.s-chip').classList.toggle('is-on', chk.checked);
    });
  });

  form.addEventListener('submit', function (e) {
    var msg = '';
    if (!npsnInput.value.trim()) {
      msg = 'NPSN wajib diisi.';
    } else if (!/^[0-9]{8}$/.test(npsnInput.value.trim())) {
      msg = 'NPSN harus 8 digit angka.';
    }
    if (msg) {
      e.preventDefault();
      if (typeof showToast === 'function') showToast(msg);
      npsnInput.focus();
      return;
    }
    if (logoInput.files.length) {
      var f = logoInput.files[0];
      if (['image/jpeg', 'image/png'].indexOf(f.type) === -1) {
        e.preventDefault();
        showLogoError('Hanya JPG/PNG yang diperbolehkan.');
        return;
      }
      if (f.size > 2 * 1024 * 1024) {
        e.preventDefault();
        showLogoError('Ukuran file melebihi 2MB.');
        return;
      }
    }
    saveBtn.disabled = true;
    saveBtnText.textContent = 'Menyimpan...';
  });
})();
</script>
@endsection

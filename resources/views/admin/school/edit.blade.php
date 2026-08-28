@extends('layouts.dashboard')

@section('title', 'Edit Sekolah')

@section('content')
@php
    $levelIds = $school?->schoolLevels?->pluck('id')->all() ?? [];
    $oldLevelIds = old('school_level_ids', $levelIds);
    $oldLevelIds = is_array($oldLevelIds) ? $oldLevelIds : [];
    $logoUrl = $school->logo_path ? \Illuminate\Support\Facades\Storage::url($school->logo_path) : null;
@endphp

<style>
  /* ===================== EDIT SEKOLAH — Bringova (no cards, scoped) ===================== */
  .sed {
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
  .sed .s-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
  .sed .s-crumb a { color: var(--coral); text-decoration: none; }
  .sed .s-crumb a:hover { text-decoration: underline; }
  .sed .s-crumb .sep { color: #d3d6de; }
  .sed .s-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 2px; }
  .sed .s-meta { font-size: 13px; color: var(--muted); }
  .sed .s-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 22px; }
  .sed .s-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px; font-weight: 500; }
  .sed .s-alert i { margin-top: 2px; }
  .sed .s-alert.success { background: var(--green-soft); color: var(--green); }
  .sed .s-alert.error { background: var(--red-soft); color: var(--red); }
  .sed .s-alert ul { margin: 6px 0 0 18px; list-style: disc; }
  .sed .s-sec { border-top: 1px solid var(--divider); padding: 24px 0 6px; }
  .sed .s-sec:first-of-type { border-top: none; padding-top: 4px; }
  .sed .s-sec-title { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--ink); text-transform: uppercase; letter-spacing: .4px; margin-bottom: 4px; }
  .sed .s-sec-title i { color: var(--coral); font-size: 13px; }
  .sed .s-sec-desc { font-size: 12.5px; color: var(--muted); margin-bottom: 16px; }
  .sed .s-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
  .sed .s-grid .full { grid-column: 1 / -1; }
  .sed .s-field label { display: block; font-size: 13px; font-weight: 600; color: var(--ink); margin-bottom: 6px; }
  .sed .s-field label .req { color: var(--red); }
  .sed .s-input, .sed .s-select { width: 100%; padding: 9px 4px; border: none; border-bottom: 1px solid rgba(26,26,46,0.18); border-radius: 0; font-size: 13px; background: transparent; color: var(--ink); box-sizing: border-box; transition: border-color .18s ease; }
  .sed .s-input::placeholder { color: var(--muted); }
  .sed .s-input:focus, .sed .s-select:focus { outline: none; border-bottom-color: var(--coral); background: transparent; box-shadow: none; }
  .sed textarea.s-input { resize: vertical; min-height: 80px; border: none; border-bottom: 1px solid rgba(26,26,46,0.18); border-radius: 0; padding: 9px 4px; background: transparent; }
  .sed textarea.s-input:focus { border-bottom-color: var(--coral); background: transparent; box-shadow: none; }
  .sed .s-hint { font-size: 11.5px; color: var(--muted); margin-top: 5px; }
  .sed .s-err { font-size: 12px; color: var(--red); margin-top: 5px; }
  /* ---------- picker trigger (border-bawah, konsisten field) ---------- */
  .sed .r-pick { display: inline-flex; align-items: center; gap: 8px; flex-wrap: nowrap; padding: 9px 4px; border: none; border-bottom: 1px solid rgba(26,26,46,0.18); border-radius: 0; font-size: 13px; color: var(--ink); background: transparent; width: 100%; cursor: pointer; text-align: left; min-height: 38px; transition: border-color .18s ease, color .18s ease; }
  .sed .r-pick:hover { border-bottom-color: var(--coral); }
  .sed .r-pick:focus { outline: none; border-bottom-color: var(--coral); }
  .sed .r-pick .pick-label { flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .sed .r-pick .pick-label.is-placeholder { color: var(--muted); }
  .sed .r-pick .pick-caret { display: none; }
  .sed .r-pick .pick-clear { flex: 0 0 auto; display: none; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 6px; background: var(--gray-soft); color: var(--gray); cursor: pointer; font-size: 9px; user-select: none; }
  .sed .r-pick .pick-clear:hover { background: var(--red-soft); color: var(--red); }
  .sed .r-pick.has-value .pick-clear { display: inline-flex; }
  .sed .r-pick.has-value .pick-label.is-placeholder { display: none; }
  /* ---------- picker modal ---------- */
  .sed .picker-backdrop { position: fixed; inset: 0; z-index: 80; background: rgba(26,26,46,0.32); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); display: none; align-items: flex-start; justify-content: center; padding: 80px 16px 16px; animation: sedPickerFade .18s ease-out; }
  .sed .picker-backdrop.is-open { display: flex; }
  @keyframes sedPickerFade { from { opacity: 0; } to { opacity: 1; } }
  .sed .picker-panel { width: 100%; max-width: 380px; max-height: min(520px, calc(100vh - 120px)); display: flex; flex-direction: column; background: #fff; border-radius: 18px; box-shadow: 0 20px 50px -16px rgba(26,26,46,0.35), 0 0 0 1px rgba(26,26,46,0.06); overflow: hidden; animation: sedPickerPop .22s cubic-bezier(.22,1.2,.36,1); }
  @keyframes sedPickerPop { from { opacity: 0; transform: translateY(-6px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
  .sed .picker-head { display: flex; align-items: center; gap: 10px; padding: 14px 16px; border-bottom: 1px solid var(--divider); }
  .sed .picker-head .picker-title { font-size: 14px; font-weight: 700; color: var(--ink); flex: 1; }
  .sed .picker-head .picker-close { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 8px; border: none; background: transparent; color: var(--muted); cursor: pointer; font-size: 12px; transition: background-color .15s ease, color .15s ease; }
  .sed .picker-head .picker-close:hover { background: var(--gray-soft); color: var(--ink); }
  .sed .picker-search { position: relative; padding: 10px 14px; border-bottom: 1px solid var(--divider); }
  .sed .picker-search i { position: absolute; left: 24px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 12px; pointer-events: none; }
  .sed .picker-search input { width: 100%; padding: 9px 12px 9px 32px; border: 1px solid rgba(26,26,46,0.14); border-radius: 10px; font-size: 13px; color: var(--ink); background: rgba(255,255,255,0.7); transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
  .sed .picker-search input:focus { outline: none; border-color: var(--coral); background: #fff; box-shadow: 0 0 0 3px rgba(255,107,107,0.12); }
  .sed .picker-list { flex: 1; overflow-y: auto; padding: 6px 8px; }
  .sed .picker-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; font-size: 13px; color: var(--ink); cursor: pointer; user-select: none; transition: background-color .15s ease, color .15s ease; }
  .sed .picker-item:hover, .sed .picker-item.is-active { background: var(--coral-soft); color: var(--coral); }
  .sed .picker-item.is-selected { background: var(--coral); color: #fff; font-weight: 600; }
  .sed .picker-item.is-selected:hover { background: var(--coral); }
  .sed .picker-item .pi-label { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .sed .picker-item .pi-check { font-size: 11px; opacity: 0; }
  .sed .picker-item.is-selected .pi-check { opacity: 1; }
  .sed .picker-empty { padding: 26px 12px; text-align: center; color: var(--muted); font-size: 12.5px; }
  .sed .picker-empty i { display: block; font-size: 20px; margin-bottom: 6px; color: #d3d6de; }
  .sed .picker-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 10px 14px; border-top: 1px solid var(--divider); background: rgba(255,255,255,0.5); }
  .sed .picker-foot .picker-clear-all { display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px; border-radius: 9px; border: none; background: transparent; color: var(--muted); font-size: 12px; font-weight: 600; cursor: pointer; transition: color .15s ease, background-color .15s ease; }
  .sed .picker-foot .picker-clear-all:hover { color: var(--red); background: var(--red-soft); }
  .sed .picker-foot .picker-done { display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 9px; border: none; background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; font-size: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 6px 14px -6px rgba(255,107,107,0.55); transition: filter .15s ease, transform .15s ease; }
  .sed .picker-foot .picker-done:hover { filter: brightness(1.04); transform: translateY(-1px); }
  .sed .s-check { display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(26,26,46,0.12); border-radius: 10px; padding: 9px 14px; cursor: pointer; background: rgba(255,255,255,0.55); transition: border-color .15s ease, background-color .15s ease; }
  .sed .s-check:hover { border-color: var(--coral); background: #fff; }
  .sed .s-check:has(input:checked) { border-color: var(--coral); background: var(--coral-soft); }
  .sed .s-check input { width: 16px; height: 16px; accent-color: var(--coral); }
  .sed .s-check span { font-size: 13px; font-weight: 600; color: var(--ink); }
  .sed .s-logo-wrap { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
  .sed .s-logo-preview { width: 120px; height: 120px; border-radius: 14px; border: 1px dashed rgba(26,26,46,0.16); background: rgba(255,255,255,0.55); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; }
  .sed .s-logo-preview img { max-width: 100%; max-height: 100%; object-fit: contain; }
  .sed .s-logo-preview .ph { font-size: 11px; color: var(--muted); text-align: center; padding: 8px; }
  .sed .s-logo-actions { display: flex; flex-direction: column; gap: 8px; }
  .sed .s-submit-row { display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--divider); flex-wrap: wrap; }
  .sed .s-btn { display: inline-flex; align-items: center; gap: 7px; border: none; cursor: pointer; border-radius: 11px; padding: 10px 17px; font-size: 13px; font-weight: 700; text-decoration: none; transition: transform .15s ease, filter .15s ease, background-color .15s ease, color .15s ease; }
  .sed .s-btn:hover { transform: translateY(-1px); }
  .sed .s-btn.ghost { background: rgba(255,255,255,0.65); color: var(--ink); box-shadow: 0 2px 10px -8px rgba(26,26,46,0.3); }
  .sed .s-btn.ghost:hover { background: #fff; color: var(--coral); }
  .sed .s-btn.coral { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 8px 18px -8px rgba(255,107,107,0.6); }
  .sed .s-btn.coral:hover { filter: brightness(1.04); }
  .sed .s-btn.outline { background: transparent; color: var(--muted); }
  .sed .s-btn.outline:hover { color: var(--ink); }
  @media (max-width: 700px) { .sed .s-grid { grid-template-columns: 1fr; } .sed { padding: 20px 16px 32px; } }
</style>

<div class="sed">
  <div class="s-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <a href="{{ route('admin.schools.index') }}">Sekolah</a>
    <span class="sep">/</span>
    <span>Edit Sekolah</span>
  </div>

  <div class="s-head">
    <div>
      <h1 class="s-title">Edit Sekolah</h1>
      <p class="s-meta">Kelola profil sekolah yang ditampilkan pada form pendaftaran siswa.</p>
    </div>
    <a href="{{ route('admin.schools.index') }}" class="s-btn ghost sm"><i class="fa-solid fa-arrow-left" style="font-size:10px;"></i> Kembali</a>
  </div>

  @if (session('success'))
    <div class="s-alert success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
  @endif
  @if (session('error'))
    <div class="s-alert error"><i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span></div>
  @endif

  @if ($errors->any())
    <div class="s-alert error" style="flex-direction:column;align-items:flex-start;">
      <span style="display:flex;gap:8px;align-items:center;"><i class="fa-solid fa-triangle-exclamation"></i> <strong>Periksa kembali isian Anda:</strong></span>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.schools.update', $school) }}" method="POST" enctype="multipart/form-data" id="schoolForm">

    @csrf
    @method('PATCH')

    {{-- ================== INFORMASI DASAR ================== --}}
    <div class="s-sec">
      <div class="s-sec-title"><i class="fa-solid fa-building-columns"></i> Informasi Dasar</div>
      <p class="s-sec-desc">Identitas utama sekolah.</p>
      <div class="s-grid">
        <div class="s-field">
          <label>Nama Sekolah <span class="req">*</span></label>
          <input type="text" name="name" value="{{ old('name', $school->name) }}" required maxlength="255" class="s-input">
          @error('name')<p class="s-err">{{ $message }}</p>@enderror
        </div>
        <div class="s-field">
          <label>NPSN <span class="req">*</span></label>
          <input type="text" name="npsn" value="{{ old('npsn', $school->npsn) }}" required maxlength="8" inputmode="numeric" pattern="[0-9]{8}" title="NPSN harus 8 digit angka" class="s-input" id="npsnInput">
          <p class="s-hint">8 digit angka.</p>
          @error('npsn')<p class="s-err">{{ $message }}</p>@enderror
        </div>
        <div class="s-field">
          <label>Status Sekolah</label>
          <button type="button" class="r-pick" data-picker="school_status" aria-haspopup="listbox" aria-expanded="false">
            <span class="pick-label is-placeholder">-- Pilih Status --</span>
            <span class="pick-clear" data-clear="school_status" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
            <i class="fa-solid fa-chevron-down pick-caret"></i>
          </button>
          <input type="hidden" name="school_status" data-picker-input="school_status" value="{{ old('school_status', $school->school_status) }}">
          @error('school_status')<p class="s-err">{{ $message }}</p>@enderror
        </div>
        <div class="s-field">
          <label>Akreditasi</label>
          <button type="button" class="r-pick" data-picker="accreditation" aria-haspopup="listbox" aria-expanded="false">
            <span class="pick-label is-placeholder">-- Pilih Akreditasi --</span>
            <span class="pick-clear" data-clear="accreditation" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
            <i class="fa-solid fa-chevron-down pick-caret"></i>
          </button>
          <input type="hidden" name="accreditation" data-picker-input="accreditation" value="{{ old('accreditation', $school->accreditation) }}">
          @error('accreditation')<p class="s-err">{{ $message }}</p>@enderror
        </div>
        <div class="s-field full">
          <label>Kepala Sekolah</label>
          <input type="text" name="principal_name" value="{{ old('principal_name', $school->principal_name) }}" maxlength="255" class="s-input">
          @error('principal_name')<p class="s-err">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    {{-- ================== KONTAK ================== --}}
    <div class="s-sec">
      <div class="s-sec-title"><i class="fa-solid fa-phone"></i> Kontak</div>
      <p class="s-sec-desc">Informasi kontak yang dapat dihubungi.</p>
      <div class="s-grid">
        <div class="s-field">
          <label>Telepon</label>
          <input type="text" name="phone" value="{{ old('phone', $school->phone) }}" maxlength="50" class="s-input" inputmode="numeric">
          @error('phone')<p class="s-err">{{ $message }}</p>@enderror
        </div>
        <div class="s-field">
          <label>WhatsApp</label>
          <input type="text" name="whatsapp" value="{{ old('whatsapp', $school->whatsapp) }}" maxlength="50" class="s-input" inputmode="numeric" placeholder="08xxxxxxxxxx">
          @error('whatsapp')<p class="s-err">{{ $message }}</p>@enderror
        </div>
        <div class="s-field">
          <label>Email</label>
          <input type="email" name="email" value="{{ old('email', $school->email) }}" maxlength="255" class="s-input">
          @error('email')<p class="s-err">{{ $message }}</p>@enderror
        </div>
        <div class="s-field">
          <label>Website Sekolah</label>
          <input type="url" name="website" value="{{ old('website', $school->website) }}" maxlength="255" class="s-input" placeholder="https://...">
          <p class="s-hint">Opsional. Harus diawali http:// atau https://</p>
          @error('website')<p class="s-err">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    {{-- ================== ALAMAT ================== --}}
    <div class="s-sec">
      <div class="s-sec-title"><i class="fa-solid fa-location-dot"></i> Alamat</div>
      <p class="s-sec-desc">Alamat lengkap sekolah.</p>
      <div class="s-grid">
        <div class="s-field full">
          <label>Alamat Lengkap</label>
          <textarea name="address" rows="2" class="s-input">{{ old('address', $school->address) }}</textarea>
          @error('address')<p class="s-err">{{ $message }}</p>@enderror
        </div>
        <div class="s-field">
          <label>Kecamatan</label>
          <input type="text" name="district" value="{{ old('district', $school->district) }}" maxlength="255" class="s-input">
          @error('district')<p class="s-err">{{ $message }}</p>@enderror
        </div>
        <div class="s-field">
          <label>Kota/Kabupaten</label>
          <input type="text" name="city" value="{{ old('city', $school->city) }}" maxlength="255" class="s-input">
          @error('city')<p class="s-err">{{ $message }}</p>@enderror
        </div>
        <div class="s-field">
          <label>Provinsi</label>
          <input type="text" name="province" value="{{ old('province', $school->province) }}" maxlength="255" class="s-input">
          @error('province')<p class="s-err">{{ $message }}</p>@enderror
        </div>
        <div class="s-field">
          <label>Link Google Maps</label>
          <input type="url" name="maps_link" value="{{ old('maps_link', $school->maps_link) }}" maxlength="255" class="s-input" placeholder="https://maps.google.com/...">
          <p class="s-hint">Opsional. Harus diawali http:// atau https://</p>
          @error('maps_link')<p class="s-err">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    {{-- ================== BRANDING ================== --}}
    <div class="s-sec">
      <div class="s-sec-title"><i class="fa-solid fa-palette"></i> Branding</div>
      <p class="s-sec-desc">Logo dan deskripsi singkat sekolah.</p>
      <div class="s-grid">
        <div class="s-field full">
          <label>Logo Sekolah</label>
          <div class="s-logo-wrap">
            <div class="s-logo-preview" id="logoPreview">
              @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="Logo sekolah" id="logoImg">
              @else
                <span class="ph">Belum ada logo</span>
              @endif
            </div>
            <div class="s-logo-actions">
              <label class="s-btn ghost sm" style="cursor:pointer;">
                <i class="fa-solid fa-upload" style="font-size:10px;"></i> Pilih File
                <input type="file" name="logo" id="logoInput" accept="image/jpeg,image/png" style="display:none;">
              </label>
              <p class="s-hint" style="margin:0;">JPG/PNG, maksimal 2MB.</p>
              @if ($school->logo_path)
                <label class="s-check" style="border-color:var(--red);">
                  <input type="checkbox" name="remove_logo" value="1" id="removeLogoCheck"> Hapus logo
                </label>
              @endif
              <p class="s-err" id="logoError" style="display:none;"></p>
              @error('logo')<p class="s-err">{{ $message }}</p>@enderror
            </div>
          </div>
        </div>
        <div class="s-field full">
          <label>Deskripsi Singkat Sekolah</label>
          <textarea name="description" rows="3" maxlength="500" class="s-input" id="descInput">{{ old('description', $school->description) }}</textarea>
          <p class="s-hint"><span id="descCount">0</span>/500 karakter (opsional).</p>
          @error('description')<p class="s-err">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    {{-- ================== JENJANG YANG DILAYANI ================== --}}
    <div class="s-sec">
      <div class="s-sec-title"><i class="fa-solid fa-graduation-cap"></i> Jenjang yang Dilayani</div>
      <p class="s-sec-desc">Centang jenjang pendidikan yang menerima pendaftaran di sekolah ini.</p>
      <div style="display:flex;flex-wrap:wrap;gap:10px;">
        @foreach ($levels as $level)
          <label class="s-check">
            <input type="checkbox" name="school_level_ids[]" value="{{ $level->id }}" {{ in_array($level->id, $oldLevelIds) ? 'checked' : '' }}>
            <span>{{ $level->name }}</span>
          </label>
        @endforeach
      </div>
      @error('school_level_ids')<p class="s-err">{{ $message }}</p>@enderror
    </div>

    <div class="s-submit-row">
      <a href="{{ route('admin.schools.index') }}" class="s-btn ghost">Batal</a>
      <button type="submit" class="s-btn coral" id="saveBtn">
        <i class="fa-solid fa-floppy-disk" style="font-size:11px;"></i> <span id="saveBtnText">Simpan Data Sekolah</span>
      </button>
    </div>
  </form>

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
  $pickStatus = [['v'=>'','l'=>'-- Pilih Status --'],['v'=>'negeri','l'=>'Negeri'],['v'=>'swasta','l'=>'Swasta']];
  $pickAcc = [['v'=>'','l'=>'-- Pilih Akreditasi --'],['v'=>'A','l'=>'A'],['v'=>'B','l'=>'B'],['v'=>'C','l'=>'C'],['v'=>'Belum Terakreditasi','l'=>'Belum Terakreditasi']];
  $pickerJson = ['school_status'=>$pickStatus,'accreditation'=>$pickAcc];
  $pickerLabels = ['school_status'=>'Pilih Status Sekolah','accreditation'=>'Pilih Akreditasi'];
@endphp
<div id="reg-data" hidden data-picker='@json($pickerJson)' data-picker-labels='@json($pickerLabels)'></div>
</div>

<script>
(function () {
  var form = document.getElementById('schoolForm');
  var logoInput = document.getElementById('logoInput');
  var logoPreview = document.getElementById('logoPreview');
  var logoError = document.getElementById('logoError');
  var removeLogoCheck = document.getElementById('removeLogoCheck');
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
      var okType = ['image/jpeg', 'image/png'].indexOf(file.type) !== -1;
      if (!okType) {
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
        logoPreview.innerHTML = '<img src="' + e.target.result + '" alt="Preview logo" style="max-width:100%;max-height:100%;object-fit:contain;">';
        if (removeLogoCheck) removeLogoCheck.checked = false;
      };
      reader.readAsDataURL(file);
    });
  }
  if (removeLogoCheck) {
    removeLogoCheck.addEventListener('change', function () {
      if (removeLogoCheck.checked) {
        logoInput.value = '';
        logoPreview.innerHTML = '<span class="ph">Logo akan dihapus</span>';
      } else if (logoInput.files.length === 0) {
        @if ($logoUrl)
          logoPreview.innerHTML = '<img src="{{ $logoUrl }}" alt="Logo sekolah" style="max-width:100%;max-height:100%;object-fit:contain;">';
        @else
          logoPreview.innerHTML = '<span class="ph">Belum ada logo</span>';
        @endif
      }
    });
  }
  function validNpsn() {
    var v = (npsnInput.value || '').trim();
    return /^[0-9]{8}$/.test(v);
  }
  if (npsnInput) {
    npsnInput.addEventListener('input', function () {
      npsnInput.value = npsnInput.value.replace(/[^0-9]/g, '').slice(0, 8);
    });
  }
  function updateDescCount() {
    descCount.textContent = descInput.value.length;
  }
  if (descInput) {
    updateDescCount();
    descInput.addEventListener('input', updateDescCount);
  }
  form.addEventListener('submit', function (e) {
    var msg = '';
    if (!npsnInput.value.trim()) {
      msg = 'NPSN wajib diisi.';
    } else if (!validNpsn()) {
      msg = 'NPSN harus 8 digit angka.';
    }
    if (msg) {
      e.preventDefault();
      alert(msg);
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

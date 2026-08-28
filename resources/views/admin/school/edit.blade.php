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
  .sed .s-input, .sed .s-select { width: 100%; padding: 10px 14px; border: 1px solid rgba(26,26,46,0.14); border-radius: 11px; font-size: 13px; background: rgba(255,255,255,0.55); color: var(--ink); box-sizing: border-box; transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
  .sed .s-input::placeholder { color: var(--muted); }
  .sed .s-input:focus, .sed .s-select:focus { outline: none; border-color: var(--coral); background: #fff; box-shadow: 0 0 0 4px rgba(255,107,107,0.12); }
  .sed textarea.s-input { resize: vertical; min-height: 80px; }
  .sed .s-hint { font-size: 11.5px; color: var(--muted); margin-top: 5px; }
  .sed .s-err { font-size: 12px; color: var(--red); margin-top: 5px; }
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
          <select name="school_status" class="s-select">
            <option value="">-- Pilih Status --</option>
            <option value="negeri" {{ old('school_status', $school->school_status) === 'negeri' ? 'selected' : '' }}>Negeri</option>
            <option value="swasta" {{ old('school_status', $school->school_status) === 'swasta' ? 'selected' : '' }}>Swasta</option>
          </select>
          @error('school_status')<p class="s-err">{{ $message }}</p>@enderror
        </div>
        <div class="s-field">
          <label>Akreditasi</label>
          <select name="accreditation" class="s-select">
            <option value="">-- Pilih Akreditasi --</option>
            @foreach (['A', 'B', 'C', 'Belum Terakreditasi'] as $acc)
              <option value="{{ $acc }}" {{ old('accreditation', $school->accreditation) === $acc ? 'selected' : '' }}>{{ $acc }}</option>
            @endforeach
          </select>
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

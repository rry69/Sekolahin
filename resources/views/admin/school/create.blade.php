@extends('layouts.dashboard')

@section('title', 'Tambah Sekolah')

@section('content')
@php
    $oldLevelIds = old('school_level_ids', []);
    $oldLevelIds = is_array($oldLevelIds) ? $oldLevelIds : [];
    $proLocked = ! ($_pv['licensed'] ?? true);
@endphp

<style>
  .skl-card { background: var(--panel); border: 1px solid var(--border); border-radius: 12px; padding: 22px; margin-bottom: 16px; }
  .skl-card-title { font-size: 12px; font-weight: 600; color: var(--tx3); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; display: flex; align-items: center; gap: 7px; }
  .skl-card-title i { font-size: 12px; }
  .skl-card-desc { font-size: 12px; color: var(--tx3); margin-bottom: 16px; }
  .skl-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
  .skl-grid .full { grid-column: 1 / -1; }
  .skl-field label { display: block; font-size: 13px; font-weight: 500; color: var(--tx1); margin-bottom: 6px; }
  .skl-field .req { color: var(--danger); }
  .skl-input { width: 100%; padding: 8px 12px; border: 1px solid var(--input-border); border-radius: 8px; font-size: 13px; background: var(--input-bg); color: var(--tx-body); box-sizing: border-box; }
  .skl-input:focus { outline: none; border-color: var(--accent); }
  .skl-select { width: 100%; padding: 8px 12px; border: 1px solid var(--input-border); border-radius: 8px; font-size: 13px; background: var(--input-bg); color: var(--tx-body); box-sizing: border-box; }
  .skl-select:focus { outline: none; border-color: var(--accent); }
  textarea.skl-input { resize: vertical; min-height: 80px; }
  .skl-hint { font-size: 11px; color: var(--tx4); margin-top: 5px; }
  .skl-err { font-size: 12px; color: var(--error-fg); margin-top: 5px; }
  .skl-check { display: inline-flex; align-items: center; gap: 8px; border: 1px solid var(--border); border-radius: 10px; padding: 9px 14px; cursor: pointer; background: var(--panel-2); }
  .skl-check input { width: 16px; height: 16px; accent-color: var(--accent); }
  .skl-check span { font-size: 13px; font-weight: 500; color: var(--tx-body); }
  .skl-logo-preview { width: 120px; height: 120px; border-radius: 12px; border: 1px dashed var(--input-border); background: var(--panel-2); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; }
  .skl-logo-preview .ph { font-size: 11px; color: var(--tx4); text-align: center; padding: 8px; }
  .skl-logo-actions { display: flex; flex-direction: column; gap: 8px; }
  .skl-submit-row { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; flex-wrap: wrap; }
  @media (max-width: 700px) { .skl-grid { grid-template-columns: 1fr; } }
</style>

<div class="breadcrumb">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <span class="sep">/</span>
  <a href="{{ route('admin.schools.index') }}">Sekolah</a>
  <span class="sep">/</span>
  <span>Tambah Sekolah</span>
</div>

<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:18px;">
  <div>
    <h1 class="page-title" style="margin-bottom:2px;">Tambah Sekolah
      @if($proLocked) <span class="pl-pro-badge"><x-hi name="lock" /> Fitur PRO</span> @endif
    </h1>
    <p style="font-size:13px;color:var(--tx2);">Tambahkan profil sekolah baru yang ditampilkan pada form pendaftaran siswa.</p>
  </div>
  <a href="{{ route('admin.schools.index') }}" class="btn btn-outline">
    <x-hi name="arrow-left-01" style="font-size:10px;" /> Kembali
  </a>
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

<form action="{{ route('admin.schools.store') }}" method="POST" enctype="multipart/form-data" id="schoolForm">
  @csrf

  @if($proLocked)
  <div class="pl-lock-box">
    <div class="pl-lock-fields">
  @endif

  {{-- ================== INFORMASI DASAR ================== --}}
  <div class="skl-card">
    <h4 class="skl-card-title"><x-hi name="bank" /> Informasi Dasar</h4>
    <p class="skl-card-desc">Identitas utama sekolah.</p>

    <div class="skl-grid">
      <div class="skl-field">
        <label>Nama Sekolah <span class="req">*</span></label>
        <input type="text" name="name" value="{{ old('name') }}" required maxlength="255" class="skl-input">
        @error('name')<p class="skl-err">{{ $message }}</p>@enderror
      </div>

      <div class="skl-field">
        <label>NPSN <span class="req">*</span></label>
        <input type="text" name="npsn" value="{{ old('npsn') }}" required maxlength="8" inputmode="numeric" pattern="[0-9]{8}" title="NPSN harus 8 digit angka" class="skl-input" id="npsnInput">
        <p class="skl-hint">8 digit angka.</p>
        @error('npsn')<p class="skl-err">{{ $message }}</p>@enderror
      </div>

      <div class="skl-field">
        <label>Status Sekolah</label>
        <select name="school_status" class="skl-select">
          <option value="">-- Pilih Status --</option>
          <option value="negeri" {{ old('school_status') === 'negeri' ? 'selected' : '' }}>Negeri</option>
          <option value="swasta" {{ old('school_status') === 'swasta' ? 'selected' : '' }}>Swasta</option>
        </select>
        @error('school_status')<p class="skl-err">{{ $message }}</p>@enderror
      </div>

      <div class="skl-field">
        <label>Akreditasi</label>
        <select name="accreditation" class="skl-select">
          <option value="">-- Pilih Akreditasi --</option>
          @foreach (['A', 'B', 'C', 'Belum Terakreditasi'] as $acc)
            <option value="{{ $acc }}" {{ old('accreditation') === $acc ? 'selected' : '' }}>{{ $acc }}</option>
          @endforeach
        </select>
        @error('accreditation')<p class="skl-err">{{ $message }}</p>@enderror
      </div>

      <div class="skl-field full">
        <label>Kepala Sekolah</label>
        <input type="text" name="principal_name" value="{{ old('principal_name') }}" maxlength="255" class="skl-input">
        @error('principal_name')<p class="skl-err">{{ $message }}</p>@enderror
      </div>
    </div>
  </div>

  {{-- ================== KONTAK ================== --}}
  <div class="skl-card">
    <h4 class="skl-card-title"><x-hi name="call" /> Kontak</h4>
    <p class="skl-card-desc">Informasi kontak yang dapat dihubungi.</p>

    <div class="skl-grid">
      <div class="skl-field">
        <label>Telepon</label>
        <input type="text" name="phone" value="{{ old('phone') }}" maxlength="50" class="skl-input" inputmode="numeric">
        @error('phone')<p class="skl-err">{{ $message }}</p>@enderror
      </div>

      <div class="skl-field">
        <label>WhatsApp</label>
        <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" maxlength="50" class="skl-input" inputmode="numeric" placeholder="08xxxxxxxxxx">
        @error('whatsapp')<p class="skl-err">{{ $message }}</p>@enderror
      </div>

      <div class="skl-field">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" maxlength="255" class="skl-input">
        @error('email')<p class="skl-err">{{ $message }}</p>@enderror
      </div>

      <div class="skl-field">
        <label>Website Sekolah</label>
        <input type="url" name="website" value="{{ old('website') }}" maxlength="255" class="skl-input" placeholder="https://...">
        <p class="skl-hint">Opsional. Harus diawali http:// atau https://</p>
        @error('website')<p class="skl-err">{{ $message }}</p>@enderror
      </div>
    </div>
  </div>

  {{-- ================== ALAMAT ================== --}}
  <div class="skl-card">
    <h4 class="skl-card-title"><x-hi name="location-01" /> Alamat</h4>
    <p class="skl-card-desc">Alamat lengkap sekolah.</p>

    <div class="skl-grid">
      <div class="skl-field full">
        <label>Alamat Lengkap</label>
        <textarea name="address" rows="2" class="skl-input">{{ old('address') }}</textarea>
        @error('address')<p class="skl-err">{{ $message }}</p>@enderror
      </div>

      <div class="skl-field">
        <label>Kecamatan</label>
        <input type="text" name="district" value="{{ old('district') }}" maxlength="255" class="skl-input">
        @error('district')<p class="skl-err">{{ $message }}</p>@enderror
      </div>

      <div class="skl-field">
        <label>Kota/Kabupaten</label>
        <input type="text" name="city" value="{{ old('city') }}" maxlength="255" class="skl-input">
        @error('city')<p class="skl-err">{{ $message }}</p>@enderror
      </div>

      <div class="skl-field">
        <label>Provinsi</label>
        <input type="text" name="province" value="{{ old('province') }}" maxlength="255" class="skl-input">
        @error('province')<p class="skl-err">{{ $message }}</p>@enderror
      </div>

      <div class="skl-field">
        <label>Link Google Maps</label>
        <input type="url" name="maps_link" value="{{ old('maps_link') }}" maxlength="255" class="skl-input" placeholder="https://maps.google.com/...">
        <p class="skl-hint">Opsional. Harus diawali http:// atau https://</p>
        @error('maps_link')<p class="skl-err">{{ $message }}</p>@enderror
      </div>
    </div>
  </div>

  {{-- ================== BRANDING ================== --}}
  <div class="skl-card">
    <h4 class="skl-card-title"><x-hi name="colors" /> Branding</h4>
    <p class="skl-card-desc">Logo dan deskripsi singkat sekolah.</p>

    <div class="skl-grid">
      <div class="skl-field full">
        <label>Logo Sekolah</label>
        <div class="skl-logo-wrap" style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
          <div class="skl-logo-preview" id="logoPreview"><span class="ph">Belum ada logo</span></div>
          <div class="skl-logo-actions">
            <label class="btn btn-outline" style="cursor:pointer;">
              <x-hi name="upload-01" style="font-size:10px;" /> Pilih File
              <input type="file" name="logo" id="logoInput" accept="image/jpeg,image/png" style="display:none;">
            </label>
            <p class="skl-hint" style="margin:0;">JPG/PNG, maksimal 2MB.</p>
            <p class="skl-err" id="logoError" style="display:none;"></p>
            @error('logo')<p class="skl-err">{{ $message }}</p>@enderror
          </div>
        </div>
      </div>

      <div class="skl-field full">
        <label>Deskripsi Singkat Sekolah</label>
        <textarea name="description" rows="3" maxlength="500" class="skl-input" id="descInput">{{ old('description') }}</textarea>
        <p class="skl-hint"><span id="descCount">0</span>/500 karakter (opsional).</p>
        @error('description')<p class="skl-err">{{ $message }}</p>@enderror
      </div>
    </div>
  </div>

  {{-- ================== JENJANG YANG DILAYANI ================== --}}
  <div class="skl-card">
    <h4 class="skl-card-title"><x-hi name="mortarboard-01" /> Jenjang yang Dilayani <span class="req">*</span></h4>
    <p class="skl-card-desc">Centang jenjang pendidikan yang menerima pendaftaran di sekolah ini.</p>

    <div style="display:flex;flex-wrap:wrap;gap:10px;">
      @foreach ($levels as $level)
        <label class="skl-check">
          <input type="checkbox" name="school_level_ids[]" value="{{ $level->id }}"
            {{ in_array($level->id, $oldLevelIds) ? 'checked' : '' }}>
          <span>{{ $level->name }}</span>
        </label>
      @endforeach
    </div>
    @error('school_level_ids')<p class="skl-err">{{ $message }}</p>@enderror
  </div>

  <div class="skl-submit-row">
    <a href="{{ route('admin.schools.index') }}" class="btn btn-outline">Batal</a>
    <button type="submit" class="btn btn-primary" id="saveBtn">
      <x-hi name="save" style="font-size:11px;" /> <span id="saveBtnText">Simpan Sekolah</span>
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
        logoPreview.innerHTML = '<img src="' + e.target.result + '" alt="Preview logo" style="max-width:100%;max-height:100%;object-fit:contain;">';
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

  form.addEventListener('submit', function (e) {
    var msg = '';
    if (!npsnInput.value.trim()) {
      msg = 'NPSN wajib diisi.';
    } else if (!/^[0-9]{8}$/.test(npsnInput.value.trim())) {
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

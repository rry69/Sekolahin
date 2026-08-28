@extends('layouts.dashboard')
@section('title', 'Pengaturan')
@php
    $errorTabs = [
        'pembayaran'    => ['bank_name', 'bank_account_number', 'bank_account_name', 'payment_note'],
        'biaya'         => ['fees', 'notes'],
        'batas-waktu'   => ['registration_deadline_hours', 'payment_deadline_hours'],
        'daftar-ulang'  => ['re_registration_start', 're_registration_end', 'rereg_notif_enabled', 'rereg_notif_title', 'rereg_notif_body', 'rereg_notif_cta', 'rereg_notif_h2'],
        'jenjang'       => ['age_min'],
    ];
    $activeTab = request()->query('tab');
    $__errBag = $errors ?? new \Illuminate\Support\ViewErrorBag;
    if (!array_key_exists($activeTab, $errorTabs)) {
        $activeTab = null;
        foreach ($errorTabs as $tabKey => $fields) {
            foreach ($fields as $field) {
                if ($__errBag->has($field) || $__errBag->has("{$field}.*")) { $activeTab = $tabKey; break 2; }
            }
        }
        $activeTab = $activeTab ?? 'pembayaran';
    }
@endphp
@section('content')
<style>
  .ste { --coral:#FF6B6B; --coral-soft:#FFE5E3; --coral-2:#FF8E6E; --amber:#F59E0B; --amber-soft:#FEF3C7; --green:#10B981; --green-soft:#D1FAE5; --blue:#3B82F6; --blue-soft:#DBEAFE; --purple:#8B5CF6; --purple-soft:#EDE9FE; --red:#EF4444; --red-soft:#FEE2E2; --gray:#6b7280; --gray-soft:#F3F4F6; --ink:#1a1a2e; --muted:#8a8f9d; --divider:rgba(26,26,46,0.10); position:relative; border-radius:24px; padding:28px 28px 40px; background:#f6f7fb; }
  .ste .ste-crumb { display:flex; align-items:center; gap:8px; font-size:12.5px; color:var(--muted); margin-bottom:6px; font-weight:500; }
  .ste .ste-crumb a { color:var(--coral); text-decoration:none; }
  .ste .ste-crumb a:hover { text-decoration:underline; }
  .ste .ste-crumb .sep { color:#d3d6de; }
  .ste .ste-title { font-size:26px; font-weight:800; color:var(--ink); letter-spacing:-0.01em; margin-bottom:2px; }
  .ste .ste-meta { font-size:13px; color:var(--muted); margin-bottom:14px; }
  .ste .ste-alert { display:flex; align-items:flex-start; gap:10px; padding:12px 16px; border-radius:12px; font-size:13px; margin-bottom:16px; font-weight:500; }
  .ste .ste-alert i { margin-top:2px; }
  .ste .ste-alert.success { background:var(--green-soft); color:var(--green); }
  .ste .ste-alert.error { background:var(--red-soft); color:var(--red); }
  .ste .ste-tabs { display:flex; gap:0; flex-wrap:wrap; border-bottom:1px solid var(--divider); margin-bottom:16px; overflow-x:auto; }
  .ste .settings-tab { all:unset; display:inline-flex; align-items:center; padding:10px 14px 11px; font-size:13px; font-weight:600; color:var(--muted); border-bottom:2.5px solid transparent; margin-bottom:-1px; cursor:pointer; white-space:nowrap; transition:color .18s, border-color .18s; }
  .ste .settings-tab:hover { color:var(--ink); }
  .ste .settings-tab.active { color:var(--coral); border-bottom-color:var(--coral); }
  .ste .ste-sec-title { font-size:13px; font-weight:700; color:var(--ink); text-transform:uppercase; letter-spacing:.4px; margin-bottom:4px; }
  .ste .ste-sec-desc { font-size:12.5px; color:var(--muted); margin-bottom:16px; line-height:1.5; }
  .ste .ste-card { padding:18px 0; border-top:1px solid var(--divider); }
  .ste .ste-card:first-of-type { border-top:none; padding-top:4px; }
  .ste .ste-grid2 { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; }
  .ste .ste-field { display:flex; flex-direction:column; gap:6px; }
  .ste .ste-label { font-size:12px; font-weight:600; color:var(--ink); }
  .ste .ste-hint { font-size:11px; color:var(--muted); margin-top:2px; }
  .ste .ste-input-line { width:100%; padding:9px 4px; border:none; border-bottom:1px solid rgba(26,26,46,0.18); border-radius:0; font-size:13px; color:var(--ink); background:transparent; box-sizing:border-box; transition:border-color .18s; }
  .ste .ste-input-line:focus { outline:none; border-bottom-color:var(--coral); }
  .ste .ste-input-box { width:100%; padding:9px 12px; border:1px solid rgba(26,26,46,0.14); border-radius:10px; font-size:13px; color:var(--ink); background:rgba(255,255,255,0.55); box-sizing:border-box; transition:border-color .18s, background .18s, box-shadow .18s; }
  .ste .ste-input-box:focus { outline:none; border-color:var(--coral); background:#fff; box-shadow:0 0 0 3px rgba(255,107,107,0.12); }
  .ste .ste-table-wrap { overflow-x:auto; }
  .ste .ste-table { width:100%; border-collapse:collapse; font-size:13px; }
  .ste .ste-table th { text-align:left; padding:10px 12px; font-size:11px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid var(--divider); }
  .ste .ste-table td { padding:10px 12px; border-bottom:1px solid var(--divider); }
  .ste .ste-table tr:last-child td { border-bottom:none; }
  .ste .ste-pill { display:inline-flex; align-items:center; gap:6px; padding:4px 11px; border-radius:20px; font-size:11px; font-weight:700; }
  .ste .ste-btn { display:inline-flex; align-items:center; gap:6px; border:none; cursor:pointer; border-radius:11px; padding:10px 18px; font-size:13px; font-weight:700; text-decoration:none; transition:transform .15s, filter .15s; }
  .ste .ste-btn:hover { transform:translateY(-1px); }
  .ste .ste-btn.coral { background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; box-shadow:0 8px 18px -8px rgba(255,107,107,0.6); }
  .ste .ste-btn.coral:hover { filter:brightness(1.04); }
  .ste .ste-btn.ghost { background:rgba(255,255,255,0.6); color:var(--ink); }
  .ste .ste-btn.ghost:hover { background:#fff; color:var(--coral); }
  .ste .ste-level-row { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 4px; border-bottom:1px solid var(--divider); }
  .ste .ste-level-row:last-child { border-bottom:none; }
  /* modal */
  .ste .ste-modal-backdrop { position:fixed; inset:0; z-index:90; background:rgba(26,26,46,0.36); backdrop-filter:blur(3px); display:none; align-items:center; justify-content:center; padding:16px; }
  .ste .ste-modal-backdrop.is-open { display:flex; }
  .ste .ste-modal { width:100%; max-width:420px; background:#fff; border-radius:18px; padding:22px; box-shadow:0 24px 60px -18px rgba(26,26,46,0.4); animation:stePop .2s cubic-bezier(.22,1.2,.36,1); }
  @keyframes stePop { from{opacity:0; transform:scale(0.97) translateY(4px)} to{opacity:1; transform:scale(1) translateY(0)} }
  .ste .ste-modal h3 { font-size:15px; font-weight:700; color:var(--ink); margin-bottom:8px; }
  .ste .ste-modal p { font-size:13px; color:var(--muted); margin-bottom:16px; line-height:1.5; }
  .ste .ste-modal-foot { display:flex; justify-content:flex-end; gap:8px; }
  @media(max-width:700px){ .ste .ste-grid2{grid-template-columns:1fr} .ste{padding:20px 16px 32px} }
</style>

<div class="ste">
  <div class="ste-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span>Pengaturan</span>
  </div>
  <h1 class="ste-title">Pengaturan</h1>
  <p class="ste-meta">Kelola konfigurasi sistem SPMB — pembayaran, biaya, batas waktu, daftar ulang, dan jenjang.</p>

  @if (session('success'))
    <div class="ste-alert success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
  @endif
  @if (($__errBag ?? $errors ?? new \Illuminate\Support\ViewErrorBag)->any())
    <div class="ste-alert error"><i class="fa-solid fa-circle-exclamation"></i><span>Ada {{ ($__errBag ?? $errors ?? new \Illuminate\Support\ViewErrorBag)->count() }} kesalahan validasi — tab yang bermasalah sudah dibuka otomatis.</span></div>
  @endif

  <div class="ste-tabs" id="settings-tabs">
    <button type="button" data-tab-btn="pembayaran" class="settings-tab">Pembayaran</button>
    <button type="button" data-tab-btn="biaya" class="settings-tab">Biaya &amp; Jalur</button>
    <button type="button" data-tab-btn="batas-waktu" class="settings-tab">Batas Waktu</button>
    <button type="button" data-tab-btn="daftar-ulang" class="settings-tab">Daftar Ulang</button>
    <button type="button" data-tab-btn="jenjang" class="settings-tab">Jenjang</button>
  </div>

  <form action="{{ route('admin.settings.update') }}" method="POST" id="steMainForm">
    @csrf

    <!-- ================= TAB: PEMBAYARAN ================= -->
    <div data-tab-panel="pembayaran" class="{{ $activeTab === 'pembayaran' ? '' : 'hidden' }}">
      <h4 class="ste-sec-title">Rekening Pembayaran</h4>
      <p class="ste-sec-desc">Nomor rekening pembayaran manual yang ditampilkan kepada siswa.</p>
      <div class="ste-grid2" style="margin-bottom:16px;">
        <div class="ste-field">
          <label class="ste-label">Nama Bank</label>
          <input type="text" name="bank_name" value="{{ old('bank_name', App\Models\Setting::get('bank_name', 'BCA')) }}" required class="ste-input-line">
          @error('bank_name')<p style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
        </div>
        <div class="ste-field">
          <label class="ste-label">Nomor Rekening</label>
          <input type="text" name="bank_account_number" id="bank_account_number" inputmode="numeric" pattern="\d{6,30}" value="{{ old('bank_account_number', App\Models\Setting::get('bank_account_number')) }}" required class="ste-input-line">
          @error('bank_account_number')<p style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
        </div>
      </div>
      <div class="ste-field" style="margin-bottom:16px;">
        <label class="ste-label">Atas Nama</label>
        <input type="text" name="bank_account_name" value="{{ old('bank_account_name', App\Models\Setting::get('bank_account_name')) }}" required class="ste-input-line">
        @error('bank_account_name')<p style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
      </div>
      <div class="ste-field">
        <label class="ste-label">Catatan Pembayaran</label>
        <textarea name="payment_note" rows="2" class="ste-input-box">{{ old('payment_note', App\Models\Setting::get('payment_note')) }}</textarea>
        @error('payment_note')<p style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
      </div>
    </div>

    <!-- ================= TAB: BIAYA & JALUR ================= -->
    <div data-tab-panel="biaya" class="{{ $activeTab === 'biaya' ? '' : 'hidden' }}">
      <h4 class="ste-sec-title">Biaya Pendaftaran per Jenjang</h4>
      <p class="ste-sec-desc">Biaya <strong>Reguler</strong> dikonfigurasi di sini (default Rp 500.000). Untuk <strong>Prestasi &amp; Beasiswa</strong> nominal ditentukan manual oleh panitia di <em>Detail Pendaftaran</em> setelah berkas Terverifikasi.</p>
      <div class="ste-table-wrap" style="margin-bottom:18px;">
        <table class="ste-table">
          <thead>
            <tr>
              <th>Jenjang</th>
              @foreach($tracks as $track)<th style="text-align:center;">{{ $track->name }}</th>@endforeach
            </tr>
          </thead>
          <tbody>
            @foreach($levels as $level)
              <tr>
                <td style="font-weight:700;color:var(--ink);">{{ $level->name }}</td>
                @foreach($tracks as $track)
                  @php $feeKey = "fee_{$level->id}_{$track->id}"; $isReguler = strtolower($track->name) === 'reguler'; @endphp
                  <td style="text-align:center;">
                    @if($isReguler)
                      <input type="number" min="0" max="1000000000" step="1000" name="fees[{{ $level->id }}][{{ $track->id }}]" value="{{ App\Models\Setting::get($feeKey) }}" class="ste-input-line" style="text-align:center;" placeholder="500000">
                    @else
                      <span style="font-size:11px;color:var(--muted);font-style:italic;">Input manual<br>setelah verifikasi</span>
                    @endif
                  </td>
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <h4 class="ste-sec-title" style="margin-top:18px;">Keterangan Biaya per Jalur</h4>
      <p class="ste-sec-desc">Penjelasan singkat apa saja yang dibayarkan pada tiap jalur (tampil di form pendaftaran siswa).</p>
      <div style="display:flex;flex-direction:column;gap:12px;">
        @foreach($tracks as $track)
          <div class="ste-field">
            <label class="ste-label">{{ $track->name }}</label>
            <textarea name="notes[{{ $track->id }}]" rows="2" placeholder="Apa saja yang dibayarkan" class="ste-input-box">{{ App\Models\Setting::get('note_' . $track->id) }}</textarea>
            @error('notes.' . $track->id)<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror
          </div>
        @endforeach
      </div>
    </div>

    <!-- ================= TAB: BATAS WAKTU ================= -->
    <div data-tab-panel="batas-waktu" class="{{ $activeTab === 'batas-waktu' ? '' : 'hidden' }}">
      <h4 class="ste-sec-title">Batas Waktu Pendaftaran &amp; Pembayaran</h4>
      <p class="ste-sec-desc">Atur batas waktu (dalam jam) untuk upload berkas dan pembayaran. Jika melebihi batas, status otomatis menjadi "Dibatalkan" dan kuota akan dibuka kembali.</p>
      <div class="ste-grid2">
        <div class="ste-field">
          <label class="ste-label">Batas Waktu Upload Berkas (jam)</label>
          <input type="number" min="1" max="720" name="registration_deadline_hours" value="{{ old('registration_deadline_hours', App\Models\Setting::get('registration_deadline_hours', '72')) }}" class="ste-input-line" placeholder="72">
          @error('registration_deadline_hours')<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror
          <span class="ste-hint">Default: 72 jam (3 hari)</span>
        </div>
        <div class="ste-field">
          <label class="ste-label">Batas Waktu Pembayaran (jam)</label>
          <input type="number" min="1" max="720" name="payment_deadline_hours" value="{{ old('payment_deadline_hours', App\Models\Setting::get('payment_deadline_hours', '72')) }}" class="ste-input-line" placeholder="72">
          @error('payment_deadline_hours')<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror
          <span class="ste-hint">Default: 72 jam (3 hari)</span>
        </div>
      </div>
      <input type="hidden" name="re_registration_type" value="offline">
    </div>

    <!-- ================= TAB: DAFTAR ULANG ================= -->
    <div data-tab-panel="daftar-ulang" class="{{ $activeTab === 'daftar-ulang' ? '' : 'hidden' }}">
      <h4 class="ste-sec-title">Jadwal Daftar Ulang per Jenjang</h4>
      <p class="ste-sec-desc">Daftar ulang offline di sekolah. Tiap jenjang punya jadwalnya sendiri dan <strong>wajib setelah periode pendaftaran jenjang tersebut berakhir</strong>.</p>
      <div class="ste-table-wrap" style="margin-bottom:14px;">
        <table class="ste-table">
          <thead>
            <tr><th>Jenjang</th><th>Mulai</th><th>Selesai</th></tr>
          </thead>
          <tbody>
            @foreach($levels as $level)
              @php
                $sKey = "re_registration_start_{$level->id}"; $eKey = "re_registration_end_{$level->id}";
                $sVal = old("re_registration_start.{$level->id}", App\Models\Setting::get($sKey, App\Models\Setting::get('re_registration_start')));
                $eVal = old("re_registration_end.{$level->id}", App\Models\Setting::get($eKey, App\Models\Setting::get('re_registration_end')));
                $reRegMin = $reRegMinByLevel[$level->id] ?? null;
                $periodEndLabel = $periodEndByLevel[$level->id] ?? null;
              @endphp
              <tr>
                <td style="font-weight:700;">{{ $level->name }} <span style="font-weight:400;color:var(--muted);">({{ $level->description }})</span>@if($periodEndLabel)<br><span style="font-size:11px;color:var(--muted);">Periode berakhir {{ $periodEndLabel }}</span>@endif</td>
                <td><x-date-picker name="re_registration_start[{{ $level->id }}]" id="re_reg_start_{{ $level->id }}" :value="$sVal" :min="$reRegMin" label="Mulai" />@error("re_registration_start.{$level->id}")<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror @if($reRegMin)<span class="ste-hint">Paling awal {{ $reRegMin }}</span>@endif</td>
                <td><x-date-picker name="re_registration_end[{{ $level->id }}]" id="re_reg_end_{{ $level->id }}" :value="$eVal" :min="$reRegMin" label="Selesai" />@error("re_registration_end.{$level->id}")<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <p class="ste-hint">Kosongkan tanggal = tanpa batas. Jika jadwal jenjang tidak diatur, sistem akan fallback ke pengaturan lama.</p>
      <div style="border-top:1px solid var(--divider); padding-top:18px; margin-top:18px;">
        <h4 class="ste-sec-title">Notifikasi Daftar Ulang</h4>
        <p class="ste-sec-desc">Pengingat ramah di dashboard siswa yang sudah diterima. Mendukung <code style="background:rgba(255,255,255,0.6);padding:1px 4px;border-radius:4px;">{tanggal}</code> dan <code style="background:rgba(255,255,255,0.6);padding:1px 4px;border-radius:4px;">{tanggal_selesai}</code>.</p>
        <div style="display:flex;flex-direction:column;gap:14px;">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:600;color:var(--ink);"><input type="checkbox" name="rereg_notif_enabled" id="rereg_notif_enabled" value="1" {{ old('rereg_notif_enabled', App\Models\Setting::get('rereg_notif_enabled')) ? 'checked' : '' }} style="accent-color:var(--coral);"> Aktifkan notifikasi daftar ulang untuk siswa</label>
          <div class="ste-field">
            <label class="ste-label">Judul Notifikasi</label>
            <input type="text" name="rereg_notif_title" value="{{ old('rereg_notif_title', App\Models\Setting::get('rereg_notif_title', 'Daftar Ulang Segera Dimulai')) }}" maxlength="80" class="ste-input-line">
            @error('rereg_notif_title')<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror
          </div>
          <div class="ste-field">
            <label class="ste-label">Isi Notifikasi</label>
            <textarea name="rereg_notif_body" rows="3" class="ste-input-box">{{ old('rereg_notif_body', App\Models\Setting::get('rereg_notif_body', 'Halo! Kabar baik — kamu sudah diterima sebagai calon siswa. Daftar ulang akan dibuka pada {tanggal} dan berlangsung hingga {tanggal_selesai}, jadi persiapkan berkas asli dan diri kamu untuk hadir ke sekolah.')) }}</textarea>
            @error('rereg_notif_body')<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror
            <span class="ste-hint">Maksimal 3–4 kalimat. Gunakan {tanggal} dan {tanggal_selesai}.</span>
          </div>
          <div class="ste-grid2">
            <div class="ste-field">
              <label class="ste-label">Teks Tombol (CTA)</label>
              <input type="text" name="rereg_notif_cta" value="{{ old('rereg_notif_cta', App\Models\Setting::get('rereg_notif_cta', 'Lihat Detail Pendaftaran')) }}" maxlength="60" class="ste-input-line">
              @error('rereg_notif_cta')<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror
            </div>
            <div class="ste-field">
              <label class="ste-label">Maju Berapa Hari (H-?)</label>
              <input type="number" name="rereg_notif_h2" min="1" max="14" value="{{ old('rereg_notif_h2', App\Models\Setting::get('rereg_notif_h2', '2')) }}" class="ste-input-line">
              @error('rereg_notif_h2')<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror
              <span class="ste-hint">Notifikasi mulai tampil H-<span id="rereg_notif_h2_label">2</span> sebelum tanggal mulai.</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ================= TAB: JENJANG ================= -->
    <div data-tab-panel="jenjang" class="{{ $activeTab === 'jenjang' ? '' : 'hidden' }}">
      <h4 class="ste-sec-title">Batas Usia Minimal per Jenjang</h4>
      <p class="ste-sec-desc">Atur umur minimal (tahun) saat pendaftaran untuk tiap jenjang. Kosongkan untuk menonaktifkan batas.</p>
      <div class="ste-grid2" style="grid-template-columns:repeat(auto-fill,minmax(140px,1fr));">
        @foreach($levels as $level)
          @php $key = "age_min_{$level->id}"; $val = old("age_min.{$level->id}", App\Models\Setting::get($key)); @endphp
          <div class="ste-field">
            <label class="ste-label">{{ $level->name }} <span style="font-weight:400;color:var(--muted);">({{ $level->description }})</span></label>
            <input type="number" min="0" max="30" name="age_min[{{ $level->id }}]" value="{{ $val }}" placeholder="—" class="ste-input-line">
            @error("age_min.{$level->id}")<p style="color:var(--red);font-size:12px;">{{ $message }}</p>@enderror
            <span class="ste-hint">Tahun</span>
          </div>
        @endforeach
      </div>
      <p class="ste-hint" style="margin-top:8px;">Rekomendasi: TK 4, SD 6, SMP 12, SMA/SMK 15</p>
      <div style="border-top:1px solid var(--divider); padding-top:18px; margin-top:18px;">
        <h4 class="ste-sec-title">Status Pendaftaran per Jenjang</h4>
        <p class="ste-sec-desc">Matikan jenjang yang tidak menerima pendaftaran. Jenjang nonaktif tidak muncul di form siswa.</p>
        <div style="display:flex;flex-direction:column;">
          @foreach($levels as $level)
            <div class="ste-level-row">
              <div>
                <p style="font-weight:700;color:var(--ink);font-size:13px;">{{ $level->name }}</p>
                <p style="font-size:12px;color:var(--muted);">{{ $level->description }}</p>
              </div>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="checkbox" name="is_active[{{ $level->id }}]" value="1" {{ $level->is_active ? 'checked' : '' }} style="accent-color:var(--green);width:18px;height:18px;">
                <span style="font-size:12px;font-weight:700;color:{{ $level->is_active ? 'var(--green)' : 'var(--red)' }}">{{ $level->is_active ? 'Aktif' : 'Nonaktif' }}</span>
              </label>
            </div>
          @endforeach
        </div>
        <button type="button" id="btn-save-levels" class="ste-btn coral" style="margin-top:16px;">Simpan Status Pendaftaran</button>
      </div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:22px;padding-top:16px;border-top:1px solid var(--divider);">
      <button type="button" id="btn-save-settings" class="ste-btn coral">Simpan Pengaturan</button>
    </div>
  </form>

  {{-- Confirm modal --}}
  <div id="steConfirmModal" class="ste-modal-backdrop" aria-hidden="true">
    <div class="ste-modal" role="dialog" aria-modal="true">
      <h3 id="steConfirmTitle"></h3>
      <p id="steConfirmMsg"></p>
      <div class="ste-modal-foot">
        <button type="button" class="ste-btn ghost sm" onclick="closeSteConfirm()">Batal</button>
        <button type="button" class="ste-btn coral sm" id="steConfirmAction">Ya, Simpan</button>
      </div>
    </div>
  </div>
</div>

<script>
    var tabsRoot = document.getElementById('settings-tabs');
    var tabButtons = tabsRoot.querySelectorAll('[data-tab-btn]');
    var tabPanels = document.querySelectorAll('[data-tab-panel]');
    function activateTab(key, updateUrl) {
        tabButtons.forEach(function (btn) {
            var on = btn.getAttribute('data-tab-btn') === key;
            btn.classList.toggle('active', on);
        });
        tabPanels.forEach(function (panel) {
            panel.classList.toggle('hidden', panel.getAttribute('data-tab-panel') !== key);
        });
        if (updateUrl && history.replaceState) {
            history.replaceState(null, '', '{{ url('/admin/settings') }}?tab=' + key);
        }
    }
    tabButtons.forEach(function (btn) {
        btn.addEventListener('click', function () { activateTab(btn.getAttribute('data-tab-btn'), true); });
    });
    activateTab('{{ $activeTab }}', false);
    var notifH2 = document.querySelector('input[name="rereg_notif_h2"]');
    var notifH2Label = document.getElementById('rereg_notif_h2_label');
    if (notifH2 && notifH2Label) { notifH2.addEventListener('input', function(){ notifH2Label.textContent = this.value || '2'; }); }
    var bankAcc = document.getElementById('bank_account_number');
    if (bankAcc) { bankAcc.addEventListener('input', function(){ this.value = this.value.replace(/\D/g,'').slice(0,30); }); }
    // Bringova confirm modal for save
    var pendingForm = null;
    var pendingBtn = null;
    function openSteConfirm(title, msg, form, btn) {
        document.getElementById('steConfirmTitle').textContent = title;
        document.getElementById('steConfirmMsg').textContent = msg;
        pendingForm = form;
        pendingBtn = btn;
        var m = document.getElementById('steConfirmModal');
        m.classList.add('is-open'); m.setAttribute('aria-hidden','false');
    }
    window.closeSteConfirm = function(){
        var m = document.getElementById('steConfirmModal');
        m.classList.remove('is-open'); m.setAttribute('aria-hidden','true');
        pendingForm = null; pendingBtn = null;
    };
    document.getElementById('steConfirmAction').addEventListener('click', function(){
        if (pendingForm && pendingBtn) {
            pendingBtn.disabled = true;
            pendingBtn.textContent = 'Menyimpan...';
            pendingForm.submit();
        }
        closeSteConfirm();
    });
    document.getElementById('steConfirmModal').addEventListener('click', function(e){ if(e.target===this) closeSteConfirm(); });
    document.addEventListener('keydown', function(e){ if(e.key==='Escape'){ var m=document.getElementById('steConfirmModal'); if(m&&m.classList.contains('is-open')) closeSteConfirm(); }});
    var btnSave = document.getElementById('btn-save-settings');
    if (btnSave) {
        btnSave.addEventListener('click', function(e){
            e.preventDefault();
            openSteConfirm('Simpan perubahan pengaturan?', 'Termasuk biaya pendaftaran, batas waktu, jadwal daftar ulang, dan batas usia.', document.getElementById('steMainForm'), btnSave);
        });
    }
    var btnLevels = document.getElementById('btn-save-levels');
    if (btnLevels) {
        btnLevels.addEventListener('click', function(e){
            e.preventDefault();
            // submit jenjang via separate form — create temp form
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.schools.levels.update') }}';
            var csrf = document.createElement('input'); csrf.type='hidden'; csrf.name='_token'; csrf.value='{{ csrf_token() }}';
            form.appendChild(csrf);
            document.querySelectorAll('input[name^="is_active"]:checked').forEach(function(el){
                var i = document.createElement('input'); i.type='hidden'; i.name=el.name; i.value='1'; form.appendChild(i);
            });
            document.body.appendChild(form);
            openSteConfirm('Simpan status jenjang?', 'Jenjang yang nonaktif tidak akan muncul di form pendaftaran siswa.', form, btnLevels);
        });
    }
</script>
@endsection

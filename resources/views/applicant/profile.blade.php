<x-student-layout title="Biodata Siswa">

@php
    $sections = [
        'diri'   => ['label' => 'Data Diri',        'icon' => 'fa-id-card',            'cls' => 'coral', 'fields' => ['full_name','nisn','nisn_link','nik','birth_place','birth_date','gender','religion','phone']],
        'alamat' => ['label' => 'Alamat',            'icon' => 'fa-location-dot',       'cls' => 'blue',   'fields' => ['address','province','city','district','village','rt','rw','postal_code']],
        'ortu'   => ['label' => 'Orang Tua / Wali',  'icon' => 'fa-people-roof',        'cls' => 'amber',  'fields' => ['father_name','father_occupation','mother_name','mother_occupation','parent_name','parent_phone']],
        'sekolah'=> ['label' => 'Sekolah Asal',      'icon' => 'fa-school',            'cls' => 'green',  'fields' => ['previous_school','graduation_year']],
    ];
    $values = old() ?: ($applicant?->toArray() ?? []);
    $total = 0; $filled = 0;
    $sectionProgress = [];
    foreach ($sections as $key => $sec) {
        $sTotal = 0; $sFilled = 0;
        foreach ($sec['fields'] as $f) {
            $total++; $sTotal++;
            if (!empty(trim((string)($values[$f] ?? '')))) { $filled++; $sFilled++; }
        }
        $sectionProgress[$key] = $sTotal > 0 ? (int) round($sFilled / $sTotal * 100) : 0;
    }
    $percent = $total > 0 ? (int) round($filled / $total * 100) : 0;
    $initial = mb_strtoupper(mb_substr($applicant?->full_name ?? 'S', 0, 1));
    $hasErrors = $errors->any();
    $pickerJson = [
        'gender' => [['v'=>'','l'=>'-- Pilih --'],['v'=>'L','l'=>'Laki-laki'],['v'=>'P','l'=>'Perempuan']],
        'religion' => [['v'=>'','l'=>'-- Pilih --'],['v'=>'Islam','l'=>'Islam'],['v'=>'Kristen','l'=>'Kristen'],['v'=>'Katolik','l'=>'Katolik'],['v'=>'Hindu','l'=>'Hindu'],['v'=>'Buddha','l'=>'Buddha'],['v'=>'Konghucu','l'=>'Konghucu']],
    ];
    $pickerLabels = ['gender'=>'Jenis Kelamin','religion'=>'Agama','province'=>'Provinsi','city'=>'Kabupaten/Kota','district'=>'Kecamatan','village'=>'Kelurahan/Desa'];
@endphp

<style>
  .apl { --coral:#FF6B6B; --coral-soft:#FFE5E3; --coral-2:#FF8E6E; --amber:#F59E0B; --amber-soft:#FEF3C7; --green:#10B981; --green-soft:#D1FAE5; --blue:#3B82F6; --blue-soft:#DBEAFE; --purple:#8B5CF6; --purple-soft:#EDE9FE; --red:#EF4444; --red-soft:#FEE2E2; --gray:#6b7280; --gray-soft:#F3F4F6; --ink:#1a1a2e; --muted:#8a8f9d; --divider:rgba(26,26,46,0.10); position:relative; border-radius:24px; padding:28px 28px 44px; background:#f6f7fb; box-sizing:border-box; }
  .apl .apl-crumb { display:flex; align-items:center; gap:8px; font-size:12.5px; color:var(--muted); margin-bottom:6px; font-weight:500; flex-wrap:wrap; }
  .apl .apl-crumb a { color:var(--coral); text-decoration:none; }
  .apl .apl-crumb a:hover { text-decoration:underline; }
  .apl .apl-crumb .sep { color:#d3d6de; }
  .apl .apl-title { font-size:26px; font-weight:800; color:var(--ink); letter-spacing:-0.01em; margin-bottom:2px; }
  .apl .apl-meta { font-size:13px; color:var(--muted); margin-bottom:18px; }

  .apl .apl-alert { display:flex; align-items:flex-start; gap:10px; padding:12px 16px; border-radius:12px; font-size:13px; margin-bottom:18px; font-weight:500; }
  .apl .apl-alert i { margin-top:2px; }
  .apl .apl-alert.success { background:var(--green-soft); color:var(--green); }
  .apl .apl-alert.error { background:var(--red-soft); color:var(--red); }
  .apl .apl-alert.info { background:var(--blue-soft); color:var(--blue); }

  /* layout grid */
  .apl .apl-grid { display:grid; grid-template-columns:300px minmax(0,1fr); gap:28px; align-items:start; }

  /* left column */
  .apl .apl-side { position:sticky; top:84px; display:flex; flex-direction:column; gap:16px; }
  .apl .apl-card { background:rgba(255,255,255,.62); border:1px solid rgba(26,26,46,.08); border-radius:16px; padding:18px; }
  .apl .apl-id { display:flex; align-items:center; gap:14px; }
  .apl .apl-ava { flex:0 0 auto; width:56px; height:56px; border-radius:16px; background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:800; box-shadow:0 8px 20px -8px rgba(255,107,107,.6); }
  .apl .apl-id-name { font-size:15px; font-weight:800; color:var(--ink); line-height:1.3; }
  .apl .apl-id-nisn { font-size:12px; color:var(--muted); font-family:'JetBrains Mono', monospace; margin-top:2px; }
  .apl .apl-id-tag { display:inline-flex; align-items:center; gap:5px; margin-top:6px; font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:20px; background:var(--coral-soft); color:var(--coral); }
  .apl .apl-id-tag.green { background:var(--green-soft); color:var(--green); }

  .apl .apl-prog-head { display:flex; align-items:baseline; justify-content:space-between; margin-bottom:8px; }
  .apl .apl-prog-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }
  .apl .apl-prog-num { font-size:26px; font-weight:800; color:var(--ink); font-family:'JetBrains Mono', monospace; line-height:1; }
  .apl .apl-prog-num small { font-size:13px; color:var(--muted); font-weight:600; }
  .apl .apl-prog-bar { height:8px; border-radius:20px; background:var(--gray-soft); overflow:hidden; margin-top:8px; }
  .apl .apl-prog-fill { height:100%; border-radius:20px; background:linear-gradient(135deg,var(--green),#34d399); transition:width .5s ease; }
  .apl .apl-sec-list { margin-top:14px; display:flex; flex-direction:column; gap:2px; }
  .apl .apl-sec-link { display:flex; align-items:center; gap:9px; padding:7px 9px; border-radius:10px; font-size:12.5px; font-weight:600; color:var(--ink); transition:background .15s; }
  .apl .apl-sec-link:hover { background:var(--gray-soft); }
  .apl .apl-sec-link .apl-sec-ic { width:26px; height:26px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:11px; flex:0 0 auto; }
  .apl .apl-sec-link .apl-sec-ic.coral{ background:var(--coral-soft); color:var(--coral); }
  .apl .apl-sec-link .apl-sec-ic.blue{ background:var(--blue-soft); color:var(--blue); }
  .apl .apl-sec-link .apl-sec-ic.amber{ background:var(--amber-soft); color:#b45309; }
  .apl .apl-sec-link .apl-sec-ic.green{ background:var(--green-soft); color:var(--green); }
  .apl .apl-sec-link .apl-sec-name { flex:1; min-width:0; }
  .apl .apl-sec-pill { font-size:10px; font-weight:700; padding:2px 7px; border-radius:20px; background:var(--gray-soft); color:var(--gray); }
  .apl .apl-sec-pill.ok { background:var(--green-soft); color:var(--green); }

  /* right column */
  .apl .apl-main { min-width:0; }
  .apl .apl-sec { padding:22px 0 4px; border-top:1px solid var(--divider); }
  .apl .apl-sec:first-of-type { border-top:none; padding-top:0; }
  .apl .apl-sec-head { display:flex; align-items:center; gap:12px; margin-bottom:18px; }
  .apl .apl-sec-ic { flex:0 0 auto; width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:16px; }
  .apl .apl-sec-ic.coral{ background:var(--coral-soft); color:var(--coral); }
  .apl .apl-sec-ic.blue{ background:var(--blue-soft); color:var(--blue); }
  .apl .apl-sec-ic.amber{ background:var(--amber-soft); color:#b45309; }
  .apl .apl-sec-ic.green{ background:var(--green-soft); color:var(--green); }
  .apl .apl-sec-ttl { font-size:15px; font-weight:800; color:var(--ink); letter-spacing:-0.01em; margin:0; }
  .apl .apl-sec-desc { font-size:12.5px; color:var(--muted); margin:2px 0 0; line-height:1.5; }

  .apl .apl-grid2 { display:grid; grid-template-columns:repeat(2,1fr); gap:16px 20px; }
  .apl .apl-field { display:flex; flex-direction:column; gap:6px; }
  .apl .apl-field.full { grid-column:1 / -1; }
  .apl .apl-label { font-size:12px; font-weight:600; color:var(--ink); display:flex; align-items:center; gap:4px; }
  .apl .apl-label .req { color:var(--red); }
  .apl .apl-hint { font-size:11px; color:var(--muted); margin-top:2px; }
  .apl .apl-err { font-size:12px; color:var(--red); margin-top:4px; display:flex; align-items:flex-start; gap:6px; }
  .apl .apl-err i { margin-top:1px; font-size:11px; }

  .apl .apl-input { width:100%; padding:9px 4px; border:none; border-bottom:1px solid rgba(26,26,46,0.18); border-radius:0; font-size:13px; color:var(--ink); background:transparent; box-sizing:border-box; outline:none; -webkit-tap-highlight-color:transparent; transition:border-color .18s; }
  .apl .apl-input:focus { outline:none; box-shadow:none; border-bottom-color:var(--coral); }
  .apl .apl-input:focus-visible { outline:none; box-shadow:none; border-bottom-color:var(--coral); }
  .apl .apl-input.is-err { border-bottom-color:var(--red); }
  .apl .apl-input.is-err:focus { border-bottom-color:var(--coral); }
  .apl .apl-input-box { width:100%; padding:9px 12px; border:1px solid rgba(26,26,46,0.14); border-radius:10px; font-size:13px; color:var(--ink); background:rgba(255,255,255,0.55); box-sizing:border-box; outline:none; -webkit-tap-highlight-color:transparent; transition:border-color .18s, background .18s, box-shadow .18s; }
  .apl .apl-input-box:focus { outline:none; border-color:var(--coral); background:#fff; box-shadow:0 0 0 3px rgba(255,107,107,0.12); }
  .apl .apl-input-box.is-err { border-color:var(--red); }
  .apl .apl-input-box.is-err:focus { border-color:var(--coral); box-shadow:0 0 0 3px rgba(255,107,107,0.12); }

  /* buttons */
  .apl .apl-btn { display:inline-flex; align-items:center; gap:7px; border:none; cursor:pointer; border-radius:11px; padding:10px 18px; font-size:13px; font-weight:700; text-decoration:none; transition:transform .15s, filter .15s; }
  .apl .apl-btn:hover { transform:translateY(-1px); }
  .apl .apl-btn.coral { background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; box-shadow:0 8px 18px -8px rgba(255,107,107,0.6); }
  .apl .apl-btn.coral:hover { filter:brightness(1.04); }
  .apl .apl-btn.ghost { background:rgba(255,255,255,0.6); color:var(--ink); border:1px solid var(--divider); }
  .apl .apl-btn.ghost:hover { background:#fff; color:var(--coral); }
  .apl .apl-foot { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; padding-top:18px; border-top:1px solid var(--divider); flex-wrap:wrap; }

  /* error summary */
  .apl .apl-summary { border:1px solid rgba(239,68,68,.3); border-left:4px solid var(--red); background:var(--red-soft); border-radius:12px; padding:12px 16px; margin-bottom:18px; }
  .apl .apl-summary h3 { font-size:13px; font-weight:800; color:var(--red); display:flex; align-items:center; gap:7px; }
  .apl .apl-summary ul { margin:8px 0 0; padding-left:18px; display:flex; flex-direction:column; gap:4px; }
  .apl .apl-summary li { font-size:12.5px; color:var(--red); }
  .apl .apl-summary a { color:var(--red); text-decoration:underline; }

  /* nisn result */
  .apl .apl-nisn-result { margin-top:8px; padding:9px 12px; border-radius:10px; font-size:12.5px; display:none; white-space:pre-line; }
  .apl .apl-nisn-result.ok { display:block; background:var(--green-soft); color:var(--green); }
  .apl .apl-nisn-result.no { display:block; background:var(--red-soft); color:var(--red); }
  .apl .apl-nisn-result.warn { display:block; background:var(--amber-soft); color:#b45309; }

  @media (max-width:1024px){
    .apl .apl-grid { grid-template-columns:1fr; }
    .apl .apl-side { position:static; }
    .apl .apl-card { padding:16px; }
  }
  /* Bringova override for date-picker (coral, not eggplore purple) */
  .apl [data-datepicker-trigger]{ border-bottom-color:rgba(26,26,46,0.18) !important; background:transparent !important; }
  .apl [data-datepicker-trigger]:hover, .apl [data-datepicker-trigger]:focus-visible{ border-bottom-color:var(--coral) !important; }
  .apl [data-datepicker-display]{ color:var(--ink) !important; }
  .apl .dp-picker [data-datepicker-seg].bg-eggplore-primary, .apl .dp-picker .bg-eggplore-primary{ background:var(--coral) !important; }
  .apl .dp-picker .bg-eggplore-primary-50, .apl .dp-picker .hover\:bg-eggplore-primary-50{ background-color:var(--coral-soft) !important; }
  .apl .dp-picker .text-eggplore-primary-600, .apl .dp-picker .text-eggplore-primary-500{ color:var(--coral) !important; }
  @media (max-width:640px){
    .apl { padding:18px 14px 28px; }
    .apl .apl-title { font-size:22px; }
    .apl .apl-grid2 { grid-template-columns:1fr; }
    .apl .apl-foot { justify-content:stretch; }
    .apl .apl-foot .apl-btn { width:100%; justify-content:center; min-height:44px; }
  }
</style>

<div class="apl">
  <div class="apl-crumb">
    <a href="{{ route('dashboard') }}">Beranda</a><span class="sep">/</span>
    <span>Biodata Siswa</span>
  </div>
  <h1 class="apl-title">Biodata Siswa</h1>
  <p class="apl-meta">Lengkapi data diri Anda sebelum mendaftar. Data ini menjadi profil resmi pendaftaran Anda.</p>

  @if (session('success'))
    <div class="apl-alert success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
  @endif
  @if (session('error'))
    <div class="apl-alert error"><i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span></div>
  @endif

  <div class="apl-grid">
    {{-- ===== KOLOM KIRI: kartu profil + progress ===== --}}
    <aside class="apl-side">
      <div class="apl-card">
        <div class="apl-id">
          <div class="apl-ava">{{ $initial }}</div>
          <div class="min-w-0">
            <div class="apl-id-name">{{ $applicant?->full_name ?? auth()->user()?->name ?? 'Calon Siswa' }}</div>
            <div class="apl-id-nisn">NISN {{ $applicant?->nisn ?: '—' }}</div>
            <span class="apl-id-tag green"><i class="fa-solid fa-circle-check"></i> Profil</span>
          </div>
        </div>
      </div>

      <div class="apl-card">
        <div class="apl-prog-head">
          <span class="apl-prog-label">Kelengkapan Biodata</span>
          <span id="progress-percent" class="apl-prog-num">{{ $percent }}<small>%</small></span>
        </div>
        <div class="apl-prog-bar">
          <div id="progress-bar" class="apl-prog-fill" style="width: {{ $percent }}%"></div>
        </div>
        <div class="apl-sec-list">
          @foreach ($sections as $key => $sec)
            <a href="#section-{{ $key }}" class="apl-sec-link scroll-mt-28">
              <span class="apl-sec-ic {{ $sec['cls'] }}"><i class="fa-solid {{ $sec['icon'] }}"></i></span>
              <span class="apl-sec-name">{{ $sec['label'] }}</span>
              <span data-section-progress="{{ $key }}" class="apl-sec-pill {{ $sectionProgress[$key] == 100 ? 'ok' : '' }}">{{ $sectionProgress[$key] == 100 ? 'Lengkap' : $sectionProgress[$key] . '%' }}</span>
            </a>
          @endforeach
        </div>
      </div>
    </aside>

    {{-- ===== KOLOM KANAN: form ===== --}}
    <main class="apl-main">
      @if ($hasErrors)
        <div id="error-summary" class="apl-summary" role="alert" tabindex="-1" aria-labelledby="error-summary-title">
          <h3 id="error-summary-title"><i class="fa-solid fa-circle-exclamation"></i> Ada {{ $errors->count() }} masalah pada biodata</h3>
          <ul>
            @foreach ($errors->keys() as $errKey)
              @if ($first = $errors->first($errKey))
                <li><a href="#{{ $errKey }}">{{ $first }}</a></li>
              @endif
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('applicant.profile.update') }}" id="biodata-form" novalidate>
        @csrf
        @method('PATCH')

        {{-- ===== DATA DIRI ===== --}}
        <section id="section-diri" class="apl-sec scroll-mt-28">
          <div class="apl-sec-head">
            <span class="apl-sec-ic coral"><i class="fa-solid fa-id-card"></i></span>
            <div>
              <h3 class="apl-sec-ttl">Data Diri</h3>
              <p class="apl-sec-desc">Identitas utama calon siswa.</p>
            </div>
          </div>

          <div class="apl-grid2">
            <div class="apl-field full">
              <label class="apl-label" for="full_name">Nama Lengkap <span class="req">*</span></label>
              <input type="text" id="full_name" name="full_name" class="apl-input" value="{{ old('full_name', $applicant?->full_name) }}" required data-progress-field="full_name" data-validate="required|min:3" placeholder="Contoh: Budi Santoso">
              @error('full_name')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="apl-field">
              <label class="apl-label" for="nisn">NISN <span class="req">*</span></label>
              <input type="text" id="nisn" name="nisn" class="apl-input" inputmode="numeric" maxlength="10" value="{{ old('nisn', $applicant?->nisn) }}" required data-progress-field="nisn" data-validate="required|digits:10" placeholder="Contoh: 0081234567" style="font-family:'JetBrains Mono',monospace;">
              <p class="apl-hint">10 digit Nomor Induk Siswa Nasional (lihat rapor/ijazah).</p>
              @error('nisn')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="apl-field">
              <label class="apl-label" for="nik">NIK <span class="req">*</span></label>
              <input type="text" id="nik" name="nik" class="apl-input" inputmode="numeric" maxlength="16" value="{{ old('nik', $applicant?->nik) }}" required data-progress-field="nik" data-validate="required|digits:16" placeholder="Contoh: 3171010101010001" style="font-family:'JetBrains Mono',monospace;">
              <p class="apl-hint">16 digit Nomor Induk Kependudukan (KTP/KK).</p>
              @error('nik')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="apl-field full">
              <label class="apl-label" for="nisn_link">Link Hasil Pencarian NISN <span class="req">*</span></label>
              <div style="display:flex; flex-direction:column; gap:10px;">
                <input type="text" id="nisn_link" name="nisn_link" class="apl-input" value="{{ old('nisn_link', $applicant?->nisn_link) }}" required data-progress-field="nisn_link" data-validate="required" placeholder="https://nisn.data.kemendikdasmen.go.id/search-result?id=0x...">
                <div>
                  <button type="button" id="cek-nisn-btn" class="apl-btn coral sm" style="padding:7px 14px;font-size:12.5px;border-radius:9px;"><i class="fa-solid fa-magnifying-glass"></i> Cek NISN &amp; NIK</button>
                </div>
              </div>
              <div id="nisn-check-result" class="apl-nisn-result"></div>
              <p class="apl-hint">
                Tempel link hasil pencarian NISN dari situs resmi Kemendikdasmen.
                <button type="button" onclick="document.getElementById('nisn-help').classList.toggle('hidden')" style="color:var(--coral);font-weight:600;display:inline;padding:0;">Cara mendapatkannya</button>
              </p>
              <div id="nisn-help" class="hidden" style="margin-top:6px;border-left:2px solid var(--blue-soft);padding-left:12px;font-size:12px;color:var(--muted);line-height:1.6;">
                <p><strong>Langkah-langkah:</strong></p>
                <p>1. Buka situs <a href="https://nisn.data.kemendikdasmen.go.id" target="_blank" style="color:var(--coral);">nisn.data.kemendikdasmen.go.id</a></p>
                <p>2. Masukkan NISN dan nama ibu kandung, lalu klik <em>Cari Data Siswa</em></p>
                <p>3. Setelah hasil muncul, salin (copy) alamat/link di address bar browser</p>
                <p>4. Tempel (paste) link tersebut di kolom ini</p>
              </div>
              @error('nisn_link')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="apl-field">
              <label class="apl-label" for="birth_place">Tempat Lahir <span class="req">*</span></label>
              <input type="text" id="birth_place" name="birth_place" class="apl-input" value="{{ old('birth_place', $applicant?->birth_place) }}" required data-progress-field="birth_place" data-validate="required|min:3" placeholder="Contoh: Jakarta">
              @error('birth_place')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="apl-field">
              <label class="apl-label" for="birth_date">Tanggal Lahir <span class="req">*</span></label>
              <x-date-picker name="birth_date" id="birth_date" :value="$applicant?->birth_date?->format('Y-m-d')" :required="true" :max="date('Y-m-d')" placeholder="Pilih tanggal" data-progress-field="birth_date" />
              <p id="age-hint" class="apl-hint"></p>
              @error('birth_date')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="apl-field">
              <label class="apl-label">Jenis Kelamin <span class="req">*</span></label>
              <button type="button" class="r-pick" data-picker="gender" aria-haspopup="listbox" aria-expanded="false">
                <span class="pick-label is-placeholder">-- Pilih --</span>
                <span class="pick-clear" data-clear="gender" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
                <i class="fa-solid fa-chevron-down pick-caret"></i>
              </button>
              <input type="hidden" name="gender" data-picker-input="gender" data-progress-field="gender" data-validate="required" value="{{ old('gender', $applicant?->gender) }}">
              @error('gender')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="apl-field">
              <label class="apl-label">Agama <span class="req">*</span></label>
              <button type="button" class="r-pick" data-picker="religion" aria-haspopup="listbox" aria-expanded="false">
                <span class="pick-label is-placeholder">-- Pilih --</span>
                <span class="pick-clear" data-clear="religion" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
                <i class="fa-solid fa-chevron-down pick-caret"></i>
              </button>
              <input type="hidden" name="religion" data-picker-input="religion" data-progress-field="religion" data-validate="required" value="{{ old('religion', $applicant?->religion) }}">
              @error('religion')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="apl-field">
              <label class="apl-label" for="phone">Nomor Telepon <span class="req">*</span></label>
              <input type="text" id="phone" name="phone" class="apl-input" value="{{ old('phone', $applicant?->phone) }}" required data-progress-field="phone" data-validate="required|phone" placeholder="Contoh: 081234567890" style="font-family:'JetBrains Mono',monospace;">
              @error('phone')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
          </div>
        </section>

        {{-- ===== ALAMAT ===== --}}
        <section id="section-alamat" class="apl-sec scroll-mt-28">
          <div class="apl-sec-head">
            <span class="apl-sec-ic blue"><i class="fa-solid fa-location-dot"></i></span>
            <div>
              <h3 class="apl-sec-ttl">Alamat</h3>
              <p class="apl-sec-desc">Alamat tempat tinggal calon siswa saat ini.</p>
            </div>
          </div>

          <div class="apl-grid2">
            <div class="apl-field full">
              <label class="apl-label" for="address">Alamat Lengkap <span class="req">*</span></label>
              <textarea id="address" name="address" rows="3" class="apl-input-box" required data-progress-field="address" data-validate="required|min:5" placeholder="Contoh: Jl. Melati No. 10, RT 02/RW 05">{{ old('address', $applicant?->address) }}</textarea>
              @error('address')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="apl-field">
              <label class="apl-label">Provinsi</label>
              <button type="button" class="r-pick" data-picker="province" aria-haspopup="listbox" aria-expanded="false">
                <span class="pick-label is-placeholder">-- Pilih Provinsi --</span>
                <span class="pick-clear" data-clear="province" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
                <i class="fa-solid fa-chevron-down pick-caret"></i>
              </button>
              <input type="hidden" name="province" data-picker-input="province" id="province" data-progress-field="province" data-validate="required" value="{{ old('province', $applicant?->province) }}">
              @error('province')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="apl-field">
              <label class="apl-label">Kabupaten/Kota</label>
              <button type="button" class="r-pick" data-picker="city" aria-haspopup="listbox" aria-expanded="false">
                <span class="pick-label is-placeholder">-- Pilih Kabupaten/Kota --</span>
                <span class="pick-clear" data-clear="city" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
                <i class="fa-solid fa-chevron-down pick-caret"></i>
              </button>
              <input type="hidden" name="city" data-picker-input="city" id="city" data-progress-field="city" data-validate="required" value="{{ old('city', $applicant?->city) }}">
              @error('city')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="apl-field">
              <label class="apl-label">Kecamatan</label>
              <button type="button" class="r-pick" data-picker="district" aria-haspopup="listbox" aria-expanded="false">
                <span class="pick-label is-placeholder">-- Pilih Kecamatan --</span>
                <span class="pick-clear" data-clear="district" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
                <i class="fa-solid fa-chevron-down pick-caret"></i>
              </button>
              <input type="hidden" name="district" data-picker-input="district" id="district" data-progress-field="district" data-validate="required" value="{{ old('district', $applicant?->district) }}">
              @error('district')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="apl-field">
              <label class="apl-label">Kelurahan/Desa</label>
              <button type="button" class="r-pick" data-picker="village" aria-haspopup="listbox" aria-expanded="false">
                <span class="pick-label is-placeholder">-- Pilih Kelurahan/Desa --</span>
                <span class="pick-clear" data-clear="village" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
                <i class="fa-solid fa-chevron-down pick-caret"></i>
              </button>
              <input type="hidden" name="village" data-picker-input="village" id="village" data-progress-field="village" data-validate="required" value="{{ old('village', $applicant?->village) }}">
              @error('village')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="apl-field">
              <label class="apl-label" for="rt">RT</label>
              <input type="text" id="rt" name="rt" class="apl-input" value="{{ old('rt', $applicant?->rt) }}" data-progress-field="rt" placeholder="Contoh: 02" style="font-family:'JetBrains Mono',monospace;">
              @error('rt')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="apl-field">
              <label class="apl-label" for="rw">RW</label>
              <input type="text" id="rw" name="rw" class="apl-input" value="{{ old('rw', $applicant?->rw) }}" data-progress-field="rw" placeholder="Contoh: 05" style="font-family:'JetBrains Mono',monospace;">
              @error('rw')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="apl-field">
              <label class="apl-label" for="postal_code">Kode Pos</label>
              <input type="text" id="postal_code" name="postal_code" class="apl-input" value="{{ old('postal_code', $applicant?->postal_code) }}" data-progress-field="postal_code" placeholder="Contoh: 12345" style="font-family:'JetBrains Mono',monospace;">
              @error('postal_code')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
          </div>
        </section>

        {{-- ===== ORANG TUA / WALI ===== --}}
        <section id="section-ortu" class="apl-sec scroll-mt-28">
          <div class="apl-sec-head">
            <span class="apl-sec-ic amber"><i class="fa-solid fa-people-roof"></i></span>
            <div>
              <h3 class="apl-sec-ttl">Orang Tua / Wali</h3>
              <p class="apl-sec-desc">Data ayah, ibu, dan wali (jika ada).</p>
            </div>
          </div>

          <div class="apl-grid2">
            <div class="apl-field">
              <label class="apl-label" for="father_name">Nama Ayah <span class="req">*</span></label>
              <input type="text" id="father_name" name="father_name" class="apl-input" value="{{ old('father_name', $applicant?->father_name) }}" required data-progress-field="father_name" data-validate="required|min:3" placeholder="Contoh: Ahmad Subarjo">
              @error('father_name')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
            <div class="apl-field">
              <label class="apl-label" for="father_occupation">Pekerjaan Ayah</label>
              <input type="text" id="father_occupation" name="father_occupation" class="apl-input" value="{{ old('father_occupation', $applicant?->father_occupation) }}" data-progress-field="father_occupation" placeholder="Contoh: Wiraswasta">
              @error('father_occupation')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
            <div class="apl-field">
              <label class="apl-label" for="mother_name">Nama Ibu <span class="req">*</span></label>
              <input type="text" id="mother_name" name="mother_name" class="apl-input" value="{{ old('mother_name', $applicant?->mother_name) }}" required data-progress-field="mother_name" data-validate="required|min:3" placeholder="Contoh: Siti Aminah">
              @error('mother_name')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
            <div class="apl-field">
              <label class="apl-label" for="mother_occupation">Pekerjaan Ibu</label>
              <input type="text" id="mother_occupation" name="mother_occupation" class="apl-input" value="{{ old('mother_occupation', $applicant?->mother_occupation) }}" data-progress-field="mother_occupation" placeholder="Contoh: Ibu Rumah Tangga">
              @error('mother_occupation')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
            <div class="apl-field">
              <label class="apl-label" for="parent_name">Nama Wali</label>
              <input type="text" id="parent_name" name="parent_name" class="apl-input" value="{{ old('parent_name', $applicant?->parent_name) }}" data-progress-field="parent_name" placeholder="Contoh: Bambang">
              @error('parent_name')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
            <div class="apl-field">
              <label class="apl-label" for="parent_phone">Nomor HP Orang Tua/Wali</label>
              <input type="text" id="parent_phone" name="parent_phone" class="apl-input" value="{{ old('parent_phone', $applicant?->parent_phone) }}" data-progress-field="parent_phone" data-validate="phone" placeholder="Contoh: 081298765432" style="font-family:'JetBrains Mono',monospace;">
              @error('parent_phone')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
          </div>
        </section>

        {{-- ===== SEKOLAH ASAL ===== --}}
        <section id="section-sekolah" class="apl-sec scroll-mt-28">
          <div class="apl-sec-head">
            <span class="apl-sec-ic green"><i class="fa-solid fa-school"></i></span>
            <div>
              <h3 class="apl-sec-ttl">Sekolah Asal</h3>
              <p class="apl-sec-desc">Sekolah terakhir yang ditempuh calon siswa.</p>
            </div>
          </div>

          <div class="apl-grid2">
            <div class="apl-field">
              <label class="apl-label" for="previous_school">Sekolah Asal <span class="req">*</span></label>
              <input type="text" id="previous_school" name="previous_school" class="apl-input" value="{{ old('previous_school', $applicant?->previous_school) }}" required data-progress-field="previous_school" data-validate="required|min:3" placeholder="Contoh: SMPN 1 Jakarta">
              @error('previous_school')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
            <div class="apl-field">
              <label class="apl-label" for="graduation_year">Tahun Lulus</label>
              <input type="text" id="graduation_year" name="graduation_year" class="apl-input" inputmode="numeric" maxlength="4" placeholder="Contoh: 2024" value="{{ old('graduation_year', $applicant?->graduation_year) }}" data-progress-field="graduation_year" data-validate="digits:4" style="font-family:'JetBrains Mono',monospace;">
              <p id="grad-hint" class="apl-hint"></p>
              <p class="apl-hint">Diisi 4 digit (1990–{{ date('Y') }}). Divalidasi silang dengan tanggal lahir.</p>
              @error('graduation_year')<p class="apl-err"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
          </div>
        </section>

        {{-- Aksi --}}
        <div class="apl-foot">
          <a href="{{ route('registration.index') }}" class="apl-btn ghost"><i class="fa-solid fa-xmark"></i> Batal</a>
          <button type="submit" id="biodata-submit" class="apl-btn coral">Simpan &amp; Lanjut ke Review <i class="fa-solid fa-arrow-right"></i></button>
        </div>
      </form>
    </main>
  </div>
</div>

{{-- Picker data (gender/religion statis; wilayah diisi JS) --}}
<div id="reg-data" hidden data-picker='@json($pickerJson)' data-picker-labels='@json($pickerLabels)'></div>

@push('scripts')
<script>
(function () {
    const API = 'https://www.emsifa.com/api-wilayah-indonesia/api/';
    const saved = {
        province: @json(old('province', $applicant?->province)),
        city: @json(old('city', $applicant?->city)),
        district: @json(old('district', $applicant?->district)),
        village: @json(old('village', $applicant?->village)),
    };
    window.__pickerData = window.__pickerData || {};
    window.__pickerLabels = window.__pickerLabels || {};

    function setData(key, items) {
        let arr = [{v:'', l:'-- Pilih ' + ((window.__pickerLabels||{})[key]||'').replace(/^Pilih /,'') + ' --'}];
        (items||[]).forEach(function(it){ arr.push({v:it.name, l:it.name, id:it.id}); });
        window.__pickerData[key] = arr;
    }
    function syncTriggers() {
        document.querySelectorAll('.r-pick[data-picker]').forEach(function(t){
            if (t.__pickerBound) {
                var key = t.getAttribute('data-picker');
                var input = document.querySelector('[data-picker-input="'+key+'"]');
                var data = window.__pickerData[key] || [];
                var v = input ? input.value : '';
                var found = data.find(function(x){ return String(x.v)===String(v); });
                var labelEl = t.querySelector('.pick-label');
                if (found && String(found.v)!==''){ labelEl.textContent=found.l; labelEl.classList.remove('is-placeholder'); t.classList.add('has-value'); }
                else { t.classList.add('is-placeholder'); t.classList.remove('has-value'); }
            }
        });
    }

    function loadProvince() {
        fetch(API + 'provinces.json').then(r=>r.json()).then(function(items){
            setData('province', items);
            if (saved.province) {
                var chosen = items.find(function(it){ return it.name===saved.province || it.code===saved.province; });
                if (chosen) loadCities(chosen.id);
            }
            syncTriggers();
        });
    }
    function loadCities(provId){
        fetch(API + 'regencies/' + provId + '.json').then(r=>r.json()).then(function(items){
            setData('city', items);
            var chosen = saved.city ? items.find(function(it){ return it.name===saved.city || it.code===saved.city; }) : null;
            if (chosen) loadDistricts(chosen.id);
            syncTriggers();
        });
    }
    function loadDistricts(cityId){
        fetch(API + 'districts/' + cityId + '.json').then(r=>r.json()).then(function(items){
            setData('district', items);
            var chosen = saved.district ? items.find(function(it){ return it.name===saved.district || it.code===saved.district; }) : null;
            if (chosen) loadVillages(chosen.id);
            syncTriggers();
        });
    }
    function loadVillages(distId){
        fetch(API + 'villages/' + distId + '.json').then(r=>r.json()).then(function(items){
            setData('village', items);
            syncTriggers();
        });
    }

    // wire cascade: when a hidden picker input changes, load next level
    document.querySelector('[data-picker-input="province"]').addEventListener('change', function(e){
        const provId = (this.value && window.__pickerData.province) ? (window.__pickerData.province.find(x=>String(x.v)===String(this.value))||{}).id : null;
        window.__pickerData.city = [{v:'',l:'-- Pilih Kabupaten/Kota --'}];
        window.__pickerData.district = [{v:'',l:'-- Pilih Kecamatan --'}];
        window.__pickerData.village = [{v:'',l:'-- Pilih Kelurahan/Desa --'}];
        var c = document.querySelector('[data-picker-input="city"]'); if(c) c.value='';
        var d = document.querySelector('[data-picker-input="district"]'); if(d) d.value='';
        var v = document.querySelector('[data-picker-input="village"]'); if(v) v.value='';
        syncTriggers();
        if (provId) loadCities(provId);
    });
    document.querySelector('[data-picker-input="city"]').addEventListener('change', function(e){
        const cityId = (this.value && window.__pickerData.city) ? (window.__pickerData.city.find(x=>String(x.v)===String(this.value))||{}).id : null;
        window.__pickerData.district = [{v:'',l:'-- Pilih Kecamatan --'}];
        window.__pickerData.village = [{v:'',l:'-- Pilih Kelurahan/Desa --'}];
        var d = document.querySelector('[data-picker-input="district"]'); if(d) d.value='';
        var v = document.querySelector('[data-picker-input="village"]'); if(v) v.value='';
        syncTriggers();
        if (cityId) loadDistricts(cityId);
    });
    document.querySelector('[data-picker-input="district"]').addEventListener('change', function(e){
        const distId = (this.value && window.__pickerData.district) ? (window.__pickerData.district.find(x=>String(x.v)===String(this.value))||{}).id : null;
        window.__pickerData.village = [{v:'',l:'-- Pilih Kelurahan/Desa --'}];
        var v = document.querySelector('[data-picker-input="village"]'); if(v) v.value='';
        syncTriggers();
        if (distId) loadVillages(distId);
    });

    loadProvince();
})();
</script>
@endpush

@push('scripts')
<script>
(function () {
    const bd = document.getElementById('birth_date');
    const gy = document.getElementById('graduation_year');
    const ah = document.getElementById('age-hint');
    const gh = document.getElementById('grad-hint');
    function syncHints() {
        const curY = new Date().getFullYear();
        if (bd && bd.value && ah) {
            const birth = new Date(bd.value);
            if (!isNaN(birth)) {
                const age = Math.floor((Date.now() - birth) / 31557600000);
                ah.textContent = 'Usia sekarang: ' + age + ' tahun';
                ah.style.color = (age < 3 ? 'var(--red)' : age > 40 ? '#b45309' : 'var(--muted)');
            }
        } else if (ah) { ah.textContent = ''; }
        if (bd && gy && gh) {
            const gv = (gy.value || '').trim();
            if (!gv) { gh.textContent = ''; return; }
            if (!/^\d{4}$/.test(gv)) { gh.textContent = 'Tahun lulus harus 4 digit.'; gh.style.color='#b45309'; return; }
            const g = parseInt(gv,10);
            if (g < 1990 || g > curY) { gh.textContent = 'Tahun lulus harus 1990–' + curY + '.'; gh.style.color='var(--red)'; return; }
            if (!bd.value) { gh.textContent = 'Isi tanggal lahir untuk cek konsistensi.'; gh.style.color='var(--muted)'; return; }
            const birth = new Date(bd.value);
            if (isNaN(birth)) { gh.textContent=''; return; }
            const by = birth.getFullYear();
            const atGrad = g - by;
            if (g < by) { gh.textContent='Tidak boleh sebelum tahun lahir ('+by+').'; gh.style.color='var(--red)'; return; }
            if (atGrad < 5) { gh.textContent='Usia saat lulus hanya ' + atGrad + ' tahun — periksa kembali.'; gh.style.color='var(--red)'; return; }
            if (atGrad > 30) { gh.textContent='Usia saat lulus ' + atGrad + ' tahun — tidak wajar, periksa kembali.'; gh.style.color='#b45309'; return; }
            gh.textContent='Konsisten (usia saat lulus ±' + atGrad + ' tahun).'; gh.style.color='var(--green)';
        }
    }
    if (bd) bd.addEventListener('change', syncHints);
    if (bd) bd.addEventListener('input', syncHints);
    if (gy) gy.addEventListener('input', syncHints);
    if (gy) gy.addEventListener('change', syncHints);
    syncHints();
})();
</script>
@endpush

@push('scripts')
<script>
(function () {
    const btn = document.getElementById('cek-nisn-btn');
    if (!btn) return;
    const result = document.getElementById('nisn-check-result');
    const nisnInput = document.querySelector('input[name="nisn"]');
    const linkInput = document.querySelector('input[name="nisn_link"]');
    const nikInput = document.querySelector('input[name="nik"]');

    function show(msg, kind) {
        result.className = 'apl-nisn-result';
        if (kind === 'green') result.classList.add('ok');
        else if (kind === 'red') result.classList.add('no');
        else result.classList.add('warn');
        result.textContent = msg;
    }

    btn.addEventListener('click', async function () {
        const nisn = nisnInput.value.trim();
        const link = linkInput.value.trim();
        const nik = nikInput ? nikInput.value.trim() : '';
        if (!nisn || !link) { show('Isi NISN dan link hasil pencarian terlebih dahulu.', 'warn'); return; }
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Memeriksa...';
        try {
            const res = await fetch('{{ route('applicant.profile.check-nisn') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ nisn: nisn, nisn_link: link, nik: nik }),
            });
            let body = {};
            try { body = await res.json(); } catch (e) {}
            const lines = [];
            let kind = 'green';
            if (body.nik_duplicate) { lines.push('\u2717 NIK sudah terdaftar atas nama pendaftar lain. Jangan gunakan NIK yang sama.'); kind = 'red'; }
            if (res.ok && body.status === 'valid') {
                const nama = body.data && body.data.nama ? ' atas nama ' + body.data.nama : '';
                lines.push('\u2713 NISN valid dan terdaftar di Kemendikdasmen' + nama + '.');
            } else if (res.ok && body.status === 'invalid') {
                lines.push('\u2717 ' + (body.message || 'NISN tidak valid.')); kind = 'red';
            } else if (res.ok) {
                lines.push('! ' + (body.message || 'Server NISN sedang tidak dapat diakses. Anda tetap bisa melanjutkan; verifikasi dilakukan admin.'));
                if (kind === 'green') kind = 'warn';
            } else {
                const errs = body.errors || {};
                const msg = (errs.nisn_link || errs.nisn || [body.message || 'Terjadi kesalahan saat memeriksa NISN.']).join(' ');
                lines.push('\u2717 ' + msg); kind = 'red';
            }
            show(lines.join('\n') || 'Tidak ada hasil.', kind);
        } catch (e) {
            show('! Gagal terhubung ke server. Coba lagi.', 'warn');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> Cek NISN & NIK';
        }
    });
})();
</script>
@endpush

@push('scripts')
<script>
(function () {
    const form = document.getElementById('biodata-form');
    if (!form) return;
    const sectionOf = {
        full_name:'diri', nisn:'diri', nisn_link:'diri', nik:'diri', birth_place:'diri',
        gender:'diri', religion:'diri', phone:'diri',
        address:'alamat', province:'alamat', city:'alamat', district:'alamat',
        village:'alamat', rt:'alamat', rw:'alamat', postal_code:'alamat',
        father_name:'ortu', father_occupation:'ortu', mother_name:'ortu',
        mother_occupation:'ortu', parent_name:'ortu', parent_phone:'ortu',
        previous_school:'sekolah', graduation_year:'sekolah',
    };
    const total = Object.keys(sectionOf).length;
    function update() {
        const counts = {};
        let filled = 0;
        form.querySelectorAll('[data-progress-field]').forEach(function (el) {
            const key = sectionOf[el.dataset.progressField];
            if (!key) return;
            counts[key] = counts[key] || { t: 0, f: 0 };
            counts[key].t++;
            const val = (el.type === 'checkbox') ? el.checked : el.value.trim();
            if (val) { filled++; counts[key].f++; }
        });
        const pct = Math.round(filled / total * 100);
        const bar = document.getElementById('progress-bar');
        const num = document.getElementById('progress-percent');
        if (bar) bar.style.width = pct + '%';
        if (num) num.textContent = pct;
        Object.keys(counts).forEach(function (key) {
            const badge = document.querySelector('[data-section-progress="' + key + '"]');
            if (!badge) return;
            const done = counts[key].f === counts[key].t;
            badge.textContent = done ? 'Lengkap' : Math.round(counts[key].f / counts[key].t * 100) + '%';
            badge.classList.toggle('ok', done);
        });
    }
    form.addEventListener('input', update);
    form.addEventListener('change', update);
    update();
})();
</script>
@endpush

@push('scripts')
<script>
(function () {
    const form = document.getElementById('biodata-form');
    if (!form) return;
    const messages = { required: 'Field ini wajib diisi.', min: 'Minimal :n karakter.', digits: 'Harus :n digit angka.', phone: 'Nomor HP tidak valid. Gunakan format 08xxxxxxxxxx (10-13 digit).' };
    function parseRules(raw) {
        const out = {};
        (raw || '').split('|').forEach(function (r) {
            const [k, v] = r.split(':');
            out[k] = v !== undefined ? Number(v) : true;
        });
        return out;
    }
    function isPhone(v) {
        const s = v.replace(/[\s\-]/g, '');
        // 08xx... (diawali 0/62/+62, lalu 8, total 10-13 digit)
        return /^(\+?62|0)8[1-9][0-9]{7,10}$/.test(s);
    }
    function validateField(el) {
        const rules = parseRules(el.dataset.validate);
        const val = (el.value || '').trim();
        let msg = null;
        if (rules.required && !val) msg = messages.required;
        else if (val && rules.phone && !isPhone(val)) msg = messages.phone;
        else if (val && rules.digits && !new RegExp('^\\d{' + rules.digits + '}$').test(val)) msg = messages.digits.replace(':n', rules.digits);
        else if (val && rules.min && val.length < rules.min) msg = messages.min.replace(':n', rules.min);
        return msg;
    }
    function setState(el, msg) {
        el.classList.remove('is-err');
        if (msg) el.classList.add('is-err');
        const field = el.closest('.apl-field');
        if (!field) return;
        let err = field.querySelector('[data-inline-error]');
        if (msg) {
            if (!err) {
                err = document.createElement('p');
                err.setAttribute('data-inline-error', '');
                err.className = 'apl-err';
                err.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i><span></span>';
                field.appendChild(err);
            }
            err.querySelector('span').textContent = msg;
        } else if (err) { err.remove(); }
    }
    form.querySelectorAll('[data-validate]').forEach(function (el) {
        el.addEventListener('blur', function () { el.dataset.touched = '1'; setState(el, validateField(el)); });
        el.addEventListener('input', function () { if (el.dataset.touched) setState(el, validateField(el)); });
        el.addEventListener('change', function () { el.dataset.touched = '1'; setState(el, validateField(el)); });
    });
    const summary = document.getElementById('error-summary');
    if (summary) summary.focus();
})();
</script>
@endpush

</x-student-layout>

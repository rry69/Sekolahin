<x-student-layout title="Review Data Diri">
    @php
        $verifStatus = $data['nisn_verification_status'] ?? null;
        $verifBadge = match ($verifStatus) {
            'verified'   => ['label' => '✓ Terverifikasi', 'cls' => 'is-green', 'icon' => 'fa-circle-check'],
            'unavailable'=> ['label' => 'Menunggu verifikasi', 'cls' => 'is-amber', 'icon' => 'fa-clock'],
            default      => null,
        };
        $birthDate = !empty($data['birth_date']) ? \Carbon\Carbon::parse($data['birth_date']) : null;
        $gradYear  = (int) ($data['graduation_year'] ?? 0);
        $ageAtGrad = ($birthDate && $gradYear) ? (int) $gradYear - (int) $birthDate->format('Y') : null;

        $sections = [
            ['id' => 'review-diri',    'label' => 'Data Diri',        'icon' => 'fa-id-card',      'cls' => 'coral'],
            ['id' => 'review-alamat',  'label' => 'Alamat',           'icon' => 'fa-location-dot', 'cls' => 'blue'],
            ['id' => 'review-ortu',    'label' => 'Orang Tua / Wali', 'icon' => 'fa-people-roof',  'cls' => 'amber'],
            ['id' => 'review-sekolah', 'label' => 'Sekolah Asal',     'icon' => 'fa-school',       'cls' => 'green'],
        ];
    @endphp

    <style>
        .apl { --coral:#FF6B6B; --coral-2:#FF8E6E; --ink:#1a1a2e; --muted:#8a8f9d; --divider:rgba(26,26,46,.10);
            position:relative; border-radius:24px; padding:28px 28px 44px; background:#f6f7fb; margin:24px auto; max-width:1080px; }
        .apl .apl-inner { max-width:1080px; margin:0 auto; }
        .apl-crumb { font-size:12.5px; color:var(--muted); margin-bottom:6px; }
        .apl-crumb a { color:var(--coral); font-weight:600; }
        .apl-crumb i { font-size:10px; margin:0 6px; color:var(--muted); }
        .apl-title { font-size:26px; font-weight:800; letter-spacing:-0.01em; color:var(--ink); line-height:1.15; }
        .apl-meta { font-size:13px; color:var(--muted); margin-top:4px; }
        .apl-alert { border-radius:14px; padding:12px 16px; font-size:13px; line-height:1.5; margin-bottom:20px; display:flex; align-items:flex-start; gap:10px; }
        .apl-alert.error { background:rgba(239,68,68,.10); color:#c0392b; border:1px solid rgba(239,68,68,.22); }
        .apl-alert i { margin-top:2px; }

        .apl-grid { display:grid; grid-template-columns:300px minmax(0,1fr); gap:28px; align-items:start; }
        @media (max-width:1024px){ .apl-grid { grid-template-columns:1fr; } }
        .apl-side { position:sticky; top:84px; display:flex; flex-direction:column; gap:20px; }
        @media (max-width:1024px){ .apl-side { position:static; } }

        .apl-card { background:rgba(255,255,255,.62); border:1px solid var(--divider); border-radius:16px; padding:20px; }
        .apl-card h4 { font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); margin-bottom:14px; }
        .apl-step { display:flex; align-items:center; gap:12px; padding:9px 10px; border-radius:12px; font-size:13px; }
        .apl-step .st-ic { flex:0 0 auto; width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:10px; }
        .apl-step.done .st-ic { background:var(--coral); color:#fff; }
        .apl-step.done .st-tx { color:var(--muted); }
        .apl-step.current { background:rgba(255,107,107,.10); }
        .apl-step.current .st-ic { background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; }
        .apl-step.current .st-tx { color:var(--ink); font-weight:700; }
        .apl-step.todo .st-ic { border:1.5px solid rgba(26,26,46,.22); color:var(--muted); }
        .apl-step .st-tx { flex:1; }

        .apl-sec-link { display:flex; align-items:center; gap:10px; padding:9px 10px; border-radius:12px; font-size:13px; color:var(--muted); text-decoration:none; transition:background .15s,color .15s; }
        .apl-sec-link:hover { background:rgba(255,107,107,.08); color:var(--coral); }
        .apl-sec-link i { font-size:11px; color:var(--muted); margin-left:auto; }

        .apl-main { min-width:0; }
        .apl-sec { border-top:1px solid var(--divider); padding:26px 0 6px; }
        .apl-sec:first-of-type { border-top:none; }
        .apl-sec-head { display:flex; align-items:center; gap:12px; margin-bottom:18px; }
        .apl-sec-ic { flex:0 0 auto; width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:17px; }
        .apl-sec-ic.coral  { background:rgba(255,107,107,.14); color:var(--coral); }
        .apl-sec-ic.blue   { background:rgba(59,130,246,.14); color:#3b82f6; }
        .apl-sec-ic.amber  { background:rgba(245,158,11,.16); color:#b45309; }
        .apl-sec-ic.green  { background:rgba(16,185,129,.14); color:#0e9f6e; }
        .apl-sec-ttl { font-size:15px; font-weight:800; color:var(--ink); letter-spacing:-0.01em; }
        .apl-sec-desc { font-size:12px; color:var(--muted); margin-top:1px; }
        .apl-review-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px 28px; max-width:840px; }
        @media (max-width:640px){ .apl-review-grid { grid-template-columns:1fr; } }

        .apl-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; border:none; cursor:pointer;
            border-radius:11px; padding:11px 18px; font-size:13px; font-weight:700; text-decoration:none; transition:transform .15s,box-shadow .15s,background .15s; }
        .apl-btn:hover { transform:translateY(-1px); }
        .apl-btn.coral { background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; box-shadow:0 8px 20px -8px rgba(255,107,107,.7); }
        .apl-btn.ghost { background:transparent; color:var(--muted); border:1px solid var(--divider); }
        .apl-btn.ghost:hover { color:var(--ink); border-color:rgba(26,26,46,.22); }
        .apl-foot { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-top:26px; padding-top:22px; border-top:1px solid var(--divider); }
        .apl-muted { color:var(--muted); }
        .apl-pill { display:inline-flex; align-items:center; gap:6px; border-radius:20px; padding:4px 12px; font-size:11px; font-weight:700; }
        .apl-pill.is-green { background:rgba(16,185,129,.14); color:#0e9f6e; }
        .apl-pill.is-amber { background:rgba(245,158,11,.16); color:#b45309; }
        .apl-age-hint { font-size:12px; margin-left:6px; }
        .apl-age-hint.ok  { color:#0e9f6e; }
        .apl-age-hint.bad { color:#c0392b; }
    </style>

    <div class="apl">
        <div class="apl-inner">
            <div class="apl-crumb"><a href="{{ route('dashboard') }}">Beranda</a><i class="fa-solid fa-chevron-right"></i>Review Data Diri</div>
            <h1 class="apl-title">Review Data Diri</h1>
            <p class="apl-meta">Pastikan seluruh data sudah benar sebelum disimpan. Anda masih bisa kembali untuk mengubah.</p>

            @if (session('error'))
                <div class="apl-alert error"><i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span></div>
            @endif

            <div class="apl-grid">
                <aside class="apl-side">
                    <div class="apl-card">
                        <h4>Langkah Pendaftaran</h4>
                        <nav aria-label="Progress pendaftaran" style="display:flex;flex-direction:column;gap:2px;">
                            <a href="{{ route('applicant.profile') }}" class="apl-step done">
                                <span class="st-ic"><i class="fa-solid fa-check"></i></span>
                                <span class="st-tx">Isi Biodata</span>
                            </a>
                            <span class="apl-step current">
                                <span class="st-ic"><i class="fa-solid fa-file-pen"></i></span>
                                <span class="st-tx">Review Data</span>
                            </span>
                            <span class="apl-step todo">
                                <span class="st-ic"><i class="fa-solid fa-check"></i></span>
                                <span class="st-tx">Selesai</span>
                            </span>
                        </nav>
                    </div>

                    <div class="apl-card">
                        <h4>Bagian Data</h4>
                        <nav aria-label="Bagian data" style="display:flex;flex-direction:column;gap:2px;">
                            @foreach ($sections as $sec)
                                <a href="#{{ $sec['id'] }}" class="apl-sec-link">
                                    <i class="fa-solid {{ $sec['icon'] }}"></i>{{ $sec['label'] }}<i class="fa-solid fa-arrow-right"></i>
                                </a>
                            @endforeach
                        </nav>
                    </div>
                </aside>

                <main class="apl-main">
                    <div class="apl-sec" id="review-diri" style="scroll-margin-top:84px;">
                        <header class="apl-sec-head">
                            <span class="apl-sec-ic coral"><i class="fa-solid fa-id-card"></i></span>
                            <div>
                                <div class="apl-sec-ttl">Data Diri</div>
                                <div class="apl-sec-desc">Identitas utama calon murid</div>
                            </div>
                        </header>
                        <div class="apl-review-grid">
                            <x-review-item label="Nama Lengkap" value="{{ $data['full_name'] }}" wide />
                            <x-review-item label="NISN" value="{{ $data['nisn'] ?? null }}" mono />
                            <x-review-item label="Verifikasi NISN">
                                @if ($verifBadge)
                                    <span class="apl-pill {{ $verifBadge['cls'] }}"><i class="fa-solid {{ $verifBadge['icon'] }}"></i>{{ $verifBadge['label'] }}</span>
                                @else
                                    <span class="apl-muted">—</span>
                                @endif
                            </x-review-item>
                            <x-review-item label="NIK" value="{{ $data['nik'] }}" mono />
                            <x-review-item label="Tempat Lahir" value="{{ $data['birth_place'] }}" />
                            <x-review-item label="Tanggal Lahir" value="{{ $birthDate?->format('d M Y') }}" mono />
                            <x-review-item label="Jenis Kelamin" value="{{ $data['gender'] === 'L' ? 'Laki-laki' : 'Perempuan' }}" />
                            <x-review-item label="Agama" value="{{ $data['religion'] }}" />
                            <x-review-item label="Nomor Telepon" value="{{ $data['phone'] }}" mono />
                        </div>
                    </div>

                    <div class="apl-sec" id="review-alamat" style="scroll-margin-top:84px;">
                        <header class="apl-sec-head">
                            <span class="apl-sec-ic blue"><i class="fa-solid fa-location-dot"></i></span>
                            <div>
                                <div class="apl-sec-ttl">Alamat</div>
                                <div class="apl-sec-desc">Alamat domisili saat ini</div>
                            </div>
                        </header>
                        <div class="apl-review-grid">
                            <x-review-item label="Alamat Lengkap" value="{{ $data['address'] }}" wide />
                            <x-review-item label="RT / RW" value="{{ ($data['rt'] ?? null) && ($data['rw'] ?? null) ? $data['rt'] . ' / ' . $data['rw'] : null }}" mono />
                            <x-review-item label="Kelurahan / Desa" value="{{ $data['village'] ?? null }}" />
                            <x-review-item label="Kecamatan" value="{{ $data['district'] ?? null }}" />
                            <x-review-item label="Kabupaten / Kota" value="{{ $data['city'] ?? null }}" />
                            <x-review-item label="Provinsi" value="{{ $data['province'] ?? null }}" />
                            <x-review-item label="Kode Pos" value="{{ $data['postal_code'] ?? null }}" mono />
                        </div>
                    </div>

                    <div class="apl-sec" id="review-ortu" style="scroll-margin-top:84px;">
                        <header class="apl-sec-head">
                            <span class="apl-sec-ic amber"><i class="fa-solid fa-people-roof"></i></span>
                            <div>
                                <div class="apl-sec-ttl">Orang Tua / Wali</div>
                                <div class="apl-sec-desc">Data orang tua atau wali calon murid</div>
                            </div>
                        </header>
                        <div class="apl-review-grid">
                            <x-review-item label="Nama Ayah" value="{{ $data['father_name'] }}" />
                            <x-review-item label="Pekerjaan Ayah" value="{{ $data['father_occupation'] ?? null }}" />
                            <x-review-item label="Nama Ibu" value="{{ $data['mother_name'] }}" />
                            <x-review-item label="Pekerjaan Ibu" value="{{ $data['mother_occupation'] ?? null }}" />
                            <x-review-item label="Nama Wali" value="{{ $data['parent_name'] ?? null }}" />
                            <x-review-item label="Nomor HP Orang Tua / Wali" value="{{ $data['parent_phone'] ?? null }}" mono />
                        </div>
                    </div>

                    <div class="apl-sec" id="review-sekolah" style="scroll-margin-top:84px;">
                        <header class="apl-sec-head">
                            <span class="apl-sec-ic green"><i class="fa-solid fa-school"></i></span>
                            <div>
                                <div class="apl-sec-ttl">Sekolah Asal</div>
                                <div class="apl-sec-desc">Asal pendidikan calon murid</div>
                            </div>
                        </header>
                        <div class="apl-review-grid">
                            <x-review-item label="Sekolah Asal" value="{{ $data['previous_school'] }}" />
                            <x-review-item label="Tahun Lulus" value="{{ $data['graduation_year'] ?? null }}" mono>
                                @if ($ageAtGrad !== null)
                                    <span class="apl-age-hint {{ $ageAtGrad < 5 || $ageAtGrad > 30 ? 'bad' : 'ok' }}">(usia saat lulus ±{{ $ageAtGrad }} th)</span>
                                @endif
                            </x-review-item>
                        </div>
                    </div>

                    <div class="apl-foot">
                        <a href="{{ route('applicant.profile') }}" class="apl-btn ghost"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
                        <form method="POST" action="{{ route('applicant.profile.confirm') }}">
                            @csrf
                            <button type="submit" class="apl-btn coral"><i class="fa-solid fa-check"></i> Konfirmasi &amp; Simpan</button>
                        </form>
                    </div>
                </main>
            </div>
        </div>
    </div>
</x-student-layout>

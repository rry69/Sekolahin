<x-student-layout title="Review Pendaftaran">
  <style>
    .rvw { --coral:#FF6B6B; --coral-2:#FF8E6E; --coral-soft:#FFE5E3; --ink:#1a1a2e; --muted:#8a8f9d; --divider:rgba(26,26,46,.10); --green:#10B981; --green-soft:#D1FAE5; --red:#EF4444; --red-soft:#FEE2E2; --amber:#D97706; --amber-soft:#FEF3C7; --blue:#2563EB; --blue-soft:#DBEAFE; --indigo:#6366F1; --indigo-soft:#E0E7FF; position:relative; border-radius:24px; padding:28px 28px 48px; background:#f6f7fb; }
    .rvw .rvw-inner { max-width:1000px; margin:0 auto; }
    .rvw-crumb { font-size:12.5px; color:var(--muted); margin-bottom:6px; display:flex; align-items:center; gap:7px; flex-wrap:wrap; }
    .rvw-crumb a { color:var(--coral); font-weight:600; } .rvw-crumb a:hover { text-decoration:underline; }
    .rvw-title { font-size:26px; font-weight:800; color:var(--ink); letter-spacing:-0.01em; line-height:1.2; }
    .rvw-meta { font-size:13px; color:var(--muted); margin-top:6px; }

    /* wizard stepper (2 langkah) */
    .rvw-step { display:flex; align-items:center; gap:10px; margin-top:22px; padding:16px 18px; border-top:1px solid var(--divider); border-bottom:1px solid var(--divider); }
    .rvw-step-item { display:flex; align-items:center; gap:9px; flex:1; min-width:0; }
    .rvw-step-num { width:26px; height:26px; border-radius:50%; background:#E5E7EB; color:var(--muted); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; flex:0 0 auto; }
    .rvw-step-item.done .rvw-step-num { background:var(--green); color:#fff; }
    .rvw-step-item.active .rvw-step-num { background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; box-shadow:0 6px 14px -6px rgba(255,107,107,.6); }
    .rvw-step-label { font-size:12px; font-weight:700; color:var(--muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .rvw-step-item.active .rvw-step-label, .rvw-step-item.done .rvw-step-label { color:var(--ink); }
    .rvw-step-sep { flex:0 0 auto; width:18px; height:1px; background:var(--divider); }

    /* alert */
    .rvw-alert { display:flex; gap:13px; align-items:flex-start; border-radius:14px; padding:14px 16px; margin-top:20px; border:1px solid transparent; }
    .rvw-alert svg.hi.rvw-alert-ic { font-size:16px; flex:0 0 auto; margin-top:2px; }
    .rvw-alert .rvw-alert-body { flex:1; min-width:0; }
    .rvw-alert .rvw-alert-t { font-weight:700; font-size:13.5px; }
    .rvw-alert .rvw-alert-p { font-size:13px; margin-top:2px; opacity:.92; }
    .rvw-alert.red { background:var(--red-soft); border-color:rgba(239,68,68,.25); }
    .rvw-alert.red svg.hi.rvw-alert-ic { color:var(--red); }
    .rvw-alert.red .rvw-alert-t, .rvw-alert.red .rvw-alert-p { color:#B91C1C; }

    /* Pilihan Pendaftaran */
    .rvw-sec { border-top:1px solid var(--divider); padding:26px 0 6px; }
    .rvw-sec:first-of-type { border-top:none; padding-top:24px; }
    .rvw-sec-head { display:flex; align-items:center; gap:12px; margin-bottom:14px; }
    .rvw-sec-ic { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:17px; flex:0 0 auto; }
    .rvw-sec-ic.coral { background:var(--coral-soft); color:var(--coral); }
    .rvw-sec-ic.blue { background:var(--blue-soft); color:var(--blue); }
    .rvw-sec-ic.amber { background:var(--amber-soft); color:var(--amber); }
    .rvw-sec-ic.green { background:var(--green-soft); color:var(--green); }
    .rvw-sec-ttl { font-size:14px; font-weight:800; color:var(--ink); }
    .rvw-sec-desc { font-size:12px; color:var(--muted); margin-top:1px; }
    .rvw-sec-edit { margin-left:auto; display:inline-flex; align-items:center; gap:6px; padding:7px 12px; border-radius:9px; font-size:11.5px; font-weight:700; color:var(--coral); background:var(--coral-soft); transition:background .15s, color .15s; }
    .rvw-sec-edit:hover { background:var(--coral); color:#fff; }
    .rvw-sec-edit svg.hi { font-size:10px; }

    /* selection grid (2x2) */
    .rvw-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; }
    .rvw-tile { display:flex; align-items:flex-start; gap:12px; padding:14px; border:1px solid var(--divider); border-radius:14px; background:transparent; position:relative; }
    .rvw-tile-ic { width:40px; height:40px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:16px; flex:0 0 auto; }
    .rvw-tile-ic.school { background:var(--blue-soft); color:var(--blue); }
    .rvw-tile-ic.track { background:var(--amber-soft); color:var(--amber); }
    .rvw-tile-ic.major { background:var(--amber-soft); color:var(--amber); }
    .rvw-tile-ic.period { background:#F3F4F6; color:var(--muted); }
    .rvw-tile-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); display:flex; align-items:center; gap:6px; }
    .rvw-tile-val { margin-top:3px; font-size:14px; font-weight:700; color:var(--ink); word-break:break-word; }
    .rvw-pop { display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:99px; background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:.04em; }

    /* field sections */
    .rvw-fields { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px 28px; }
    .rvw-fields .rvw-field-wide { grid-column:1 / -1; }
    .rvw-field .rvw-f-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }
    .rvw-field .rvw-f-val { margin-top:3px; font-size:14px; font-weight:600; color:var(--ink); line-height:1.45; }
    .rvw-field .rvw-f-val.mono { font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace; font-size:13px; }
    .rvw-field .rvw-f-val.empty { color:var(--muted); font-weight:500; }

    /* confirmation box */
    .rvw-confirm { margin-top:28px; display:flex; align-items:flex-start; gap:14px; padding:18px 20px; border:1px solid rgba(16,185,129,.35); border-left:3px solid var(--green); border-radius:16px; background:var(--green-soft); }
    .rvw-confirm svg.hi { color:var(--green); font-size:20px; margin-top:1px; }
    .rvw-confirm-t { font-size:14px; font-weight:800; color:#065F46; }
    .rvw-confirm-p { margin-top:3px; font-size:13px; line-height:1.6; color:#047857; }

    /* sticky action bar (all breakpoints) */
    .rvw-bar { position:fixed; left:0; right:0; bottom:0; z-index:50; background:rgba(246,247,251,.94); backdrop-filter:blur(6px); border-top:1px solid var(--divider); padding:12px 16px; }
    .rvw-bar-inner { max-width:1000px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .rvw-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:13px 20px; border-radius:12px; font-size:14px; font-weight:700; transition:transform .15s, box-shadow .15s; min-height:44px; }
    .rvw-btn.coral { background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; box-shadow:0 10px 22px -10px rgba(255,107,107,.65); }
    .rvw-btn.coral:hover { transform:translateY(-1px); box-shadow:0 14px 26px -10px rgba(255,107,107,.7); }
    .rvw-btn.coral:disabled { background:var(--muted); box-shadow:none; cursor:not-allowed; opacity:.55; transform:none; }
    .rvw-btn.ghost { background:transparent; color:var(--coral); border:1.5px solid var(--coral); }
    .rvw-btn.ghost:hover { background:var(--coral-soft); }

    @media (max-width:640px) {
      .rvw { padding:20px 18px 96px; border-radius:18px; }
      .rvw-step { flex-direction:column; align-items:stretch; gap:6px; padding:14px 12px; }
      .rvw-step-sep { display:none; }
      .rvw-grid { grid-template-columns:1fr; }
      .rvw-fields { grid-template-columns:1fr; }
      .rvw-fields .rvw-field-wide { grid-column:auto; }
    }
  </style>

  <div class="rvw">
    <div class="rvw-inner">
      {{-- Crumbs + title --}}
      <div class="rvw-crumb">
        <a href="{{ route('registration.index') }}">Pendaftaran</a>
        <x-hi icon="fa-chevron-right" style="font-size:9px" />
        <span>Review Pendaftaran</span>
      </div>
      <h1 class="rvw-title">Review Pendaftaran</h1>
      <p class="rvw-meta">Periksa kembali seluruh data sebelum mengonfirmasi pendaftaran.</p>

      {{-- Wizard stepper --}}
      <div class="rvw-step">
        <div class="rvw-step-item done">
          <span class="rvw-step-num"><x-hi icon="fa-check" style="font-size:11px" /></span>
          <span class="rvw-step-label">Pilih &amp; Isi</span>
        </div>
        <div class="rvw-step-sep"></div>
        <div class="rvw-step-item active">
          <span class="rvw-step-num">2</span>
          <span class="rvw-step-label">Review &amp; Konfirmasi</span>
        </div>
      </div>

      @if (session('error'))
        <div class="rvw-alert red">
          <x-hi icon="fa-circle-exclamation" class="rvw-alert-ic" />
          <div class="rvw-alert-body"><p class="rvw-alert-p">{{ session('error') }}</p></div>
        </div>
      @endif

      {{-- ===== PILIHAN PENDAFTARAN ===== --}}
      <section class="rvw-sec">
        <div class="rvw-sec-head">
          <div class="rvw-sec-ic coral"><x-hi icon="fa-clipboard-list" /></div>
          <div>
            <p class="rvw-sec-ttl">Pilihan Pendaftaran</p>
            <p class="rvw-sec-desc">Sekolah, jalur, jurusan, dan periode yang kamu pilih.</p>
          </div>
          <a href="{{ route('registration.create') }}" class="rvw-sec-edit"><x-hi icon="fa-pen" /> Edit</a>
        </div>

        <div class="rvw-grid">
          {{-- Sekolah --}}
          <div class="rvw-tile">
            <span class="rvw-tile-ic school"><x-hi icon="fa-school" /></span>
            <div class="min-w-0">
              <p class="rvw-tile-label">Sekolah</p>
              <p class="rvw-tile-val">{{ $school->name }}</p>
            </div>
            <span class="rvw-pop" style="margin-left:auto;align-self:flex-start"><x-hi icon="fa-bolt" /> Populer</span>
          </div>

          {{-- Jalur --}}
          <div class="rvw-tile">
            <span class="rvw-tile-ic track"><x-hi icon="fa-route" /></span>
            <div class="min-w-0">
              <p class="rvw-tile-label">Jalur</p>
              <p class="rvw-tile-val">{{ $track->name }}</p>
            </div>
          </div>

          @if($major)
          {{-- Jurusan --}}
          <div class="rvw-tile">
            <span class="rvw-tile-ic major"><x-hi icon="fa-book-open" /></span>
            <div class="min-w-0">
              <p class="rvw-tile-label">Jurusan</p>
              <p class="rvw-tile-val">{{ $major->name }}</p>
            </div>
          </div>
          @endif

          {{-- Periode --}}
          <div class="rvw-tile">
            <span class="rvw-tile-ic period"><x-hi icon="fa-calendar-days" /></span>
            <div class="min-w-0">
              <p class="rvw-tile-label">Periode</p>
              <p class="rvw-tile-val">{{ $period->name }}</p>
            </div>
          </div>
        </div>
      </section>

      {{-- ===== DATA PRIBADI ===== --}}
      <section class="rvw-sec">
        <div class="rvw-sec-head">
          <div class="rvw-sec-ic blue"><x-hi icon="fa-id-card" /></div>
          <div>
            <p class="rvw-sec-ttl">Data Pribadi</p>
            <p class="rvw-sec-desc">Informasi diri dari biodata kamu.</p>
          </div>
          <a href="{{ route('applicant.profile') }}" class="rvw-sec-edit"><x-hi icon="fa-pen" /> Edit</a>
        </div>
        <div class="rvw-fields">
          <div class="rvw-field rvw-field-wide">
            <p class="rvw-f-label">Nama Lengkap</p>
            <p class="rvw-f-val">{{ $applicant->full_name }}</p>
          </div>
          <div class="rvw-field">
            <p class="rvw-f-label">NIK</p>
            <p class="rvw-f-val mono">{{ $applicant->nik }}</p>
          </div>
          <div class="rvw-field">
            <p class="rvw-f-label">NISN</p>
            <p class="rvw-f-val mono">{{ $applicant->nisn ?? '—' }}</p>
          </div>
          <div class="rvw-field rvw-field-wide">
            <p class="rvw-f-label">Tempat, Tanggal Lahir</p>
            <p class="rvw-f-val">{{ $applicant->birth_place }}, {{ $applicant->birth_date?->format('d M Y') }}</p>
          </div>
          <div class="rvw-field">
            <p class="rvw-f-label">Jenis Kelamin</p>
            <p class="rvw-f-val">{{ $applicant->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
          </div>
          <div class="rvw-field">
            <p class="rvw-f-label">Agama</p>
            <p class="rvw-f-val">{{ $applicant->religion }}</p>
          </div>
          <div class="rvw-field">
            <p class="rvw-f-label">No. Telepon</p>
            <p class="rvw-f-val mono">{{ $applicant->phone }}</p>
          </div>
          <div class="rvw-field rvw-field-wide">
            <p class="rvw-f-label">Sekolah Asal</p>
            <p class="rvw-f-val">{{ $applicant->previous_school }}</p>
          </div>
        </div>
      </section>

      {{-- ===== ALAMAT ===== --}}
      <section class="rvw-sec">
        <div class="rvw-sec-head">
          <div class="rvw-sec-ic green"><x-hi icon="fa-location-dot" /></div>
          <div>
            <p class="rvw-sec-ttl">Alamat</p>
            <p class="rvw-sec-desc">Alamat tempat tinggal kamu.</p>
          </div>
          <a href="{{ route('applicant.profile') }}" class="rvw-sec-edit"><x-hi icon="fa-pen" /> Edit</a>
        </div>
        <div class="rvw-fields">
          <div class="rvw-field rvw-field-wide">
            <p class="rvw-f-label">Alamat Lengkap</p>
            <p class="rvw-f-val">{{ $applicant->address }}</p>
          </div>
          <div class="rvw-field">
            <p class="rvw-f-label">RT / RW</p>
            <p class="rvw-f-val mono">{{ ($applicant->rt ?? null) && ($applicant->rw ?? null) ? $applicant->rt . ' / ' . $applicant->rw : '—' }}</p>
          </div>
          <div class="rvw-field">
            <p class="rvw-f-label">Kelurahan / Desa</p>
            <p class="rvw-f-val">{{ $applicant->village ?? '—' }}</p>
          </div>
          <div class="rvw-field">
            <p class="rvw-f-label">Kecamatan</p>
            <p class="rvw-f-val">{{ $applicant->district ?? '—' }}</p>
          </div>
          <div class="rvw-field">
            <p class="rvw-f-label">Kabupaten / Kota</p>
            <p class="rvw-f-val">{{ $applicant->city ?? '—' }}</p>
          </div>
          <div class="rvw-field">
            <p class="rvw-f-label">Provinsi</p>
            <p class="rvw-f-val">{{ $applicant->province ?? '—' }}</p>
          </div>
          <div class="rvw-field">
            <p class="rvw-f-label">Kode Pos</p>
            <p class="rvw-f-val mono">{{ $applicant->postal_code ?? '—' }}</p>
          </div>
        </div>
      </section>

      {{-- ===== ORANG TUA / WALI ===== --}}
      <section class="rvw-sec">
        <div class="rvw-sec-head">
          <div class="rvw-sec-ic amber"><x-hi icon="fa-people-roof" /></div>
          <div>
            <p class="rvw-sec-ttl">Orang Tua / Wali</p>
            <p class="rvw-sec-desc">Informasi orang tua atau wali kamu.</p>
          </div>
          <a href="{{ route('applicant.profile') }}" class="rvw-sec-edit"><x-hi icon="fa-pen" /> Edit</a>
        </div>
        <div class="rvw-fields">
          <div class="rvw-field">
            <p class="rvw-f-label">Nama Ayah</p>
            <p class="rvw-f-val">{{ $applicant->father_name ?? '—' }}</p>
          </div>
          <div class="rvw-field">
            <p class="rvw-f-label">Pekerjaan Ayah</p>
            <p class="rvw-f-val">{{ $applicant->father_occupation ?? '—' }}</p>
          </div>
          <div class="rvw-field">
            <p class="rvw-f-label">Nama Ibu</p>
            <p class="rvw-f-val">{{ $applicant->mother_name ?? '—' }}</p>
          </div>
          <div class="rvw-field">
            <p class="rvw-f-label">Pekerjaan Ibu</p>
            <p class="rvw-f-val">{{ $applicant->mother_occupation ?? '—' }}</p>
          </div>
          <div class="rvw-field">
            <p class="rvw-f-label">Nama Wali</p>
            <p class="rvw-f-val">{{ $applicant->parent_name ?? '—' }}</p>
          </div>
          <div class="rvw-field">
            <p class="rvw-f-label">No. HP Orang Tua / Wali</p>
            <p class="rvw-f-val mono">{{ $applicant->parent_phone ?? '—' }}</p>
          </div>
        </div>
      </section>

      {{-- ===== CONFIRMATION BOX ===== --}}
      <section class="rvw-confirm">
        <x-hi icon="fa-circle-check" />
        <div>
          <p class="rvw-confirm-t">Pastikan seluruh data di atas sudah benar.</p>
          <p class="rvw-confirm-p">Setelah dikonfirmasi, pendaftaran Anda akan dikirim dan <strong>tidak dapat diubah</strong>. Periksa kembali sebelum menekan tombol konfirmasi.</p>
        </div>
      </section>

      <div style="height:28px"></div>
    </div>
  </div>

  {{-- ===== STICKY ACTION BAR (semua breakpoint) ===== --}}
  <div class="rvw-bar">
    <div class="rvw-bar-inner">
      <a href="{{ route('registration.create') }}" class="rvw-btn ghost">
        <x-hi icon="fa-arrow-left" /> Kembali
      </a>
      <form method="POST" action="{{ route('registration.confirm') }}" id="confirmForm">
        @csrf
        <input type="hidden" name="registration_period_id" value="{{ $validated['registration_period_id'] }}">
        <input type="hidden" name="registration_track_id" value="{{ $validated['registration_track_id'] }}">
        <input type="hidden" name="major_id" value="{{ $validated['major_id'] ?? '' }}">
        <input type="hidden" name="school_id" value="{{ $validated['school_id'] }}">
        <button type="submit" id="confirmBtn" class="rvw-btn coral">
          <x-hi icon="fa-check" /> Konfirmasi &amp; Daftar
        </button>
      </form>
    </div>
  </div>

  @push('scripts')
  <script>
    // Anti double-submit + loading state
    (function () {
      const btn = document.getElementById('confirmBtn');
      if (!btn) return;
      btn.addEventListener('click', function () {
        if (btn.disabled) return;
        btn.disabled = true;
        btn.innerHTML = hiSvg('fa-spinner', 'class="animate-spin"') + ' Memproses...';
        setTimeout(() => { document.getElementById('confirmForm').submit(); }, 80);
      });
    })();
  </script>
  @endpush
</x-student-layout>

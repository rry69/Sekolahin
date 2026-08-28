<x-student-layout title="Buat Pendaftaran Baru">
  <style>
    .cre { --coral:#FF6B6B; --coral-2:#FF8E6E; --coral-soft:#FFE5E3; --ink:#1a1a2e; --muted:#8a8f9d; --divider:rgba(26,26,46,.10); --green:#10B981; --green-soft:#D1FAE5; --red:#EF4444; --red-soft:#FEE2E2; --amber:#D97706; --amber-soft:#FEF3C7; --blue:#2563EB; --blue-soft:#DBEAFE; --indigo:#6366F1; --indigo-soft:#E0E7FF; position:relative; border-radius:24px; padding:28px 28px 44px; background:#f6f7fb; }
    .cre .cre-inner { max-width:1120px; margin:0 auto; }
    .cre-crumb { font-size:12.5px; color:var(--muted); margin-bottom:6px; display:flex; align-items:center; gap:7px; flex-wrap:wrap; }
    .cre-crumb a { color:var(--coral); font-weight:600; } .cre-crumb a:hover { text-decoration:underline; }
    .cre-title { font-size:26px; font-weight:800; color:var(--ink); letter-spacing:-0.01em; line-height:1.2; }
    .cre-meta { font-size:13px; color:var(--muted); margin-top:6px; }

    /* alerts */
    .cre-alert { display:flex; gap:13px; align-items:flex-start; border-radius:14px; padding:14px 16px; margin-top:20px; border:1px solid transparent; }
    .cre-alert i.cre-alert-ic { width:22px; height:22px; border-radius:7px; display:flex; align-items:center; justify-content:center; font-size:11px; flex:0 0 auto; margin-top:1px; }
    .cre-alert .cre-alert-body { flex:1; min-width:0; }
    .cre-alert .cre-alert-t { font-weight:700; font-size:13.5px; }
    .cre-alert .cre-alert-p { font-size:13px; margin-top:2px; opacity:.92; }
    .cre-alert.red { background:var(--red-soft); border-color:rgba(239,68,68,.25); }
    .cre-alert.red i.cre-alert-ic { background:var(--red); color:#fff; }
    .cre-alert.red .cre-alert-t, .cre-alert.red .cre-alert-p { color:#B91C1C; }
    .cre-alert.amber { background:var(--amber-soft); border-color:rgba(217,119,6,.3); }
    .cre-alert.amber i.cre-alert-ic { background:var(--amber); color:#fff; }
    .cre-alert.amber .cre-alert-t, .cre-alert.amber .cre-alert-p { color:#B45309; }
    .cre-alert.info { background:var(--indigo-soft); border-color:rgba(99,102,241,.25); }
    .cre-alert.info i.cre-alert-ic { background:var(--indigo); color:#fff; }
    .cre-alert.info .cre-alert-t, .cre-alert.info .cre-alert-p { color:#4338CA; }

    /* section */
    .cre-sec { border-top:1px solid var(--divider); padding:26px 0 6px; }
    .cre-sec:first-of-type { border-top:none; padding-top:22px; }
    .cre-sec-head { display:flex; align-items:center; gap:12px; margin-bottom:6px; }
    .cre-sec-ic { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:17px; flex:0 0 auto; }
    .cre-sec-ic.coral { background:var(--coral-soft); color:var(--coral); }
    .cre-sec-ic.blue { background:var(--blue-soft); color:var(--blue); }
    .cre-sec-ic.amber { background:var(--amber-soft); color:var(--amber); }
    .cre-sec-ic.green { background:var(--green-soft); color:var(--green); }
    .cre-sec-ttl { font-size:14px; font-weight:800; color:var(--ink); }
    .cre-sec-desc { font-size:12px; color:var(--muted); margin-top:1px; }

    /* wizard stepper */
    .cre-step { display:flex; align-items:center; gap:8px; margin-top:20px; padding:16px 18px; border-top:1px solid var(--divider); border-bottom:1px solid var(--divider); }
    .cre-step-item { display:flex; align-items:center; gap:9px; cursor:pointer; flex:1; min-width:0; padding:4px 6px; border-radius:10px; }
    .cre-step-item:hover { background:var(--coral-soft); }
    .cre-step-num { width:26px; height:26px; border-radius:50%; background:#E5E7EB; color:var(--muted); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; flex:0 0 auto; transition:all .2s; }
    .cre-step-item.done .cre-step-num { background:var(--green); color:#fff; }
    .cre-step-item.active .cre-step-num { background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; box-shadow:0 6px 14px -6px rgba(255,107,107,.6); }
    .cre-step-label { font-size:12px; font-weight:700; color:var(--muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; transition:color .2s; }
    .cre-step-item.active .cre-step-label, .cre-step-item.done .cre-step-label { color:var(--ink); }
    .cre-step-sep { flex:0 0 auto; width:18px; height:1px; background:var(--divider); }

    /* two-column layout */
    .cre-grid { display:grid; grid-template-columns:minmax(0,1fr) 320px; gap:28px; align-items:start; margin-top:6px; }
    .cre-main { min-width:0; }
    .cre-side { position:sticky; top:88px; }

    /* summary card */
    .cre-sum { background:transparent; border:1px solid var(--divider); border-radius:18px; padding:20px; border-left:3px solid var(--coral); }
    .cre-sum-hd { display:flex; align-items:center; gap:11px; padding-bottom:16px; border-bottom:1px solid var(--divider); margin-bottom:14px; }
    .cre-sum-ava { width:42px; height:42px; border-radius:50%; background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:15px; flex:0 0 auto; }
    .cre-sum-name { font-size:13.5px; font-weight:800; color:var(--ink); }
    .cre-sum-role { font-size:11px; color:var(--muted); margin-top:1px; }
    .cre-sum-age { margin-left:auto; font-size:10.5px; font-weight:700; color:#047857; background:var(--green-soft); padding:3px 9px; border-radius:99px; white-space:nowrap; }
    .cre-sum-rows { display:flex; flex-direction:column; }
    .cre-sum-row { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; padding:10px 0; border-top:1px solid var(--divider); }
    .cre-sum-row:first-of-type { border-top:none; padding-top:0; }
    .cre-sum-row .lb { font-size:11.5px; color:var(--muted); display:flex; align-items:center; gap:6px; flex:0 0 auto; }
    .cre-sum-row .lb i { color:var(--coral); font-size:11px; width:13px; text-align:center; }
    .cre-sum-row .val { font-size:12px; font-weight:700; color:var(--ink); text-align:right; }
    .cre-sum-row .val.placeholder { color:var(--muted); font-weight:600; }
    .cre-sum-row .val .q-badge { font-size:9.5px; font-weight:700; padding:1px 7px; border-radius:99px; margin-left:5px; }
    .cre-sum-row .val .q-badge.green { background:var(--green-soft); color:#047857; }
    .cre-sum-row .val .q-badge.red { background:var(--red-soft); color:#B91C1C; }
    .cre-sum-foot { margin-top:14px; padding-top:16px; border-top:1px solid var(--divider); display:flex; flex-direction:column; gap:10px; }
    .cre-sum .cre-btn.coral { width:100%; padding:13px 16px; font-size:13.5px; }
    .cre-sum .cre-btn.ghost { width:100%; }
    .cre-sum-err { font-size:11.5px; color:var(--red); display:flex; align-items:flex-start; gap:6px; }

    /* mobile sticky action bar */
    .cre-bar { display:none; position:sticky; bottom:0; z-index:40; background:rgba(246,247,251,.94); backdrop-filter:blur(6px); border-top:1px solid var(--divider); padding:12px 4px 8px; margin-top:10px; gap:10px; }
    .cre-bar .cre-btn { flex:1; }

    /* NO-WHITE-CARD overrides (neutralize Tailwind white cards to transparent + divider) */
    .cre .period-item, .cre .track-item { background:transparent !important; border:none !important; border-radius:0 !important; padding:18px 4px; border-top:1px solid var(--divider); }
    .cre .period-item:first-of-type, .cre .track-item:first-of-type { border-top:none; padding-top:6px; }
    .cre .period-item.bg-eggplore-primary-50, .cre .period-item.bg-white, .cre .track-item.bg-eggplore-primary-50, .cre .track-item.bg-white { background:transparent !important; }
    .cre .period-item.ring-2, .cre .track-item.ring-2 { box-shadow:none !important; }
    .cre .period-item:has(.period-radio:checked), .cre .track-item:has(.track-radio:checked) { background:var(--coral-soft) !important; border-radius:14px !important; border-top-color:transparent !important; }
    .cre .period-item.bg-eggplore-danger-soft { background:var(--red-soft) !important; border-radius:14px !important; }
    .cre .period-item.bg-eggplore-neutral-100 { background:#F3F4F6 !important; border-radius:14px !important; }

    /* custom dropdown triggers + panels -> non-white, divider-based */
    .cre .school-trigger, .cre .major-trigger { background:transparent !important; border:none !important; border-bottom:1px solid rgba(26,26,46,.18) !important; border-radius:0 !important; padding:10px 4px !important; outline:none; -webkit-tap-highlight-color:transparent; box-shadow:none !important; }
    .cre .school-trigger:focus, .cre .school-trigger:focus-visible, .cre .school-trigger:focus-within,
    .cre .major-trigger:focus, .cre .major-trigger:focus-visible, .cre .major-trigger:focus-within { border-bottom-color:var(--coral) !important; outline:none !important; box-shadow:none !important; }
    .cre .period-radio:focus, .cre .period-radio:focus-visible { outline:none !important; box-shadow:none !important; }
    .cre #school-panel, .cre #major-panel { }
    .cre #school-panel .rounded-2xl, .cre #major-panel .rounded-2xl { background:transparent !important; border:none !important; padding:0 !important; }
    .cre .school-option, .cre .major-option { border-radius:10px; }
    .cre .school-option:hover, .cre .major-option:hover { background:var(--coral-soft) !important; box-shadow:none !important; }
    .cre .school-option.bg-eggplore-primary-50, .cre .school-option.bg-eggplore-primary-100, .cre .major-option.bg-eggplore-primary-50, .cre .major-option.bg-eggplore-primary-100 { background:var(--coral-soft) !important; }

    /* pills */
    .cre-pill { display:inline-flex; align-items:center; gap:6px; padding:4px 11px; border-radius:99px; font-size:11px; font-weight:700; }
    .cre-pill.green { background:var(--green-soft); color:#047857; }
    .cre-pill.amber { background:var(--amber-soft); color:#B45309; }
    .cre-pill.red { background:var(--red-soft); color:#B91C1C; }
    .cre-pill.blue { background:var(--blue-soft); color:#1D4ED8; }
    .cre-pill.gray { background:#F3F4F6; color:var(--gray, #6b7280); }
    .cre-pill.indigo { background:var(--indigo-soft); color:#4338CA; }

    /* buttons */
    .cre-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:11px 18px; border-radius:11px; font-size:13px; font-weight:700; transition:transform .15s, box-shadow .15s; }
    .cre-btn.coral { background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; box-shadow:0 10px 22px -10px rgba(255,107,107,.65); }
    .cre-btn.coral:hover { transform:translateY(-1px); box-shadow:0 14px 26px -10px rgba(255,107,107,.7); }
    .cre-btn.ghost { background:transparent; color:var(--coral); border:1.5px solid var(--coral); }
    .cre-btn.ghost:hover { background:var(--coral-soft); }
    .cre-btn.sm { padding:9px 15px; font-size:12.5px; border-radius:10px; }
    .cre-btn:disabled { background:var(--muted); color:#fff; box-shadow:none; cursor:not-allowed; opacity:.55; }

    /* footer actions */
    .cre-foot { display:flex; align-items:center; justify-content:space-between; gap:14px; margin-top:6px; padding-top:22px; border-top:1px solid var(--divider); }
    @media (max-width:640px) {
      .cre-foot { flex-direction:column-reverse; align-items:stretch; }
      .cre-foot .cre-btn { width:100%; }
    }

    /* hint / error */
    .cre-hint { display:flex; align-items:center; gap:6px; font-size:12px; }
    .cre-err { display:flex; align-items:flex-start; gap:6px; font-size:12px; color:var(--red); margin-top:8px; }

    /* empty state */
    .cre-empty { margin-top:22px; text-align:center; padding:44px 24px; border-top:1px solid var(--divider); }
    .cre-empty-ic { width:76px; height:76px; margin:0 auto; border-radius:22px; background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; display:flex; align-items:center; justify-content:center; font-size:30px; box-shadow:0 18px 40px -16px rgba(255,107,107,.65); }
    .cre-empty p { max-width:400px; margin:14px auto 0; font-size:14px; color:var(--muted); }
    .cre-empty .cre-btn { margin-top:22px; }

    @media (max-width:1024px) {
      .cre-grid { grid-template-columns:1fr; gap:20px; }
      .cre-side { position:static; }
      .cre-sum { order:0; }
    }
    @media (max-width:640px) {
      .cre { padding:20px 18px 32px; border-radius:18px; }
      .cre-sec-head { margin-bottom:2px; }
      .cre-step { flex-direction:column; align-items:stretch; gap:6px; padding:14px 12px; }
      .cre-step-sep { display:none; }
      .cre-step-item { justify-content:flex-start; }
      .cre-bar { display:flex; flex-direction:column-reverse; }
      .cre-sum .cre-btn.coral { display:none; }
    }
  </style>

  <div class="cre">
    <div class="cre-inner">
      {{-- Crumbs + title --}}
      <div class="cre-crumb">
        <a href="{{ route('registration.index') }}">Pendaftaran</a>
        <i class="fa-solid fa-chevron-right" style="font-size:9px"></i>
        <span>Buat Pendaftaran Baru</span>
      </div>
      <h1 class="cre-title">Buat Pendaftaran Baru</h1>
      <p class="cre-meta">Lengkapi data pendaftaran kamu di bawah ini.</p>

      @if (session('error'))
        <div class="cre-alert red">
          <i class="fa-solid fa-circle-exclamation cre-alert-ic"></i>
          <div class="cre-alert-body"><p class="cre-alert-p">{{ session('error') }}</p></div>
        </div>
      @endif

      @if ($periods->isEmpty())
        <div class="cre-empty">
          <div class="cre-empty-ic"><i class="fa-regular fa-calendar-xmark"></i></div>
          <p>Tidak ada periode pendaftaran yang aktif saat ini.</p>
          <a href="{{ route('registration.index') }}" class="cre-btn ghost sm"><i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Pendaftaran</a>
        </div>
      @else
                        @php
                            $hasAge = isset($applicantAge) && $applicantAge !== null;
                            $openCount = $periods->filter(fn($p) => $p->registrationStatus() === 'open')->count();
                            $notStartedCount = $periods->filter(fn($p) => $p->registrationStatus() === 'not_started')->count();
                            $closedCount = $periods->filter(fn($p) => $p->registrationStatus() === 'closed')->count();
                        @endphp
                        @if($openCount === 0)
                            @if($notStartedCount > 0 && $closedCount === 0)
                                <div class="cre-alert amber">
                                    <i class="fa-solid fa-hourglass-half cre-alert-ic"></i>
                                    <div class="cre-alert-body">
                                        <p class="cre-alert-t">Pendaftaran Belum Dibuka</p>
                                        <p class="cre-alert-p">Periode pendaftaran belum dimulai. Silakan kembali lagi sesuai jadwal yang tertera di bawah. Pendaftaran tidak dapat dilakukan sebelum tanggal mulai.</p>
                                    </div>
                                </div>
                            @elseif($closedCount > 0 && $notStartedCount === 0)
                                <div class="cre-alert red">
                                    <i class="fa-solid fa-circle-xmark cre-alert-ic"></i>
                                    <div class="cre-alert-body">
                                        <p class="cre-alert-t">Pendaftaran Sudah Ditutup</p>
                                        <p class="cre-alert-p">Periode pendaftaran telah berakhir. Pendaftaran baru tidak dapat dilakukan lagi.</p>
                                    </div>
                                </div>
                            @else
                                <div class="cre-alert amber">
                                    <i class="fa-solid fa-circle-info cre-alert-ic"></i>
                                    <div class="cre-alert-body">
                                        <p class="cre-alert-t">Pendaftaran Tidak Tersedia Saat Ini</p>
                                        <p class="cre-alert-p">Tidak ada periode pendaftaran yang sedang dibuka. Periksa jadwal di bawah — ada yang belum dibuka atau sudah ditutup.</p>
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- Wizard stepper --}}
                        <div class="cre-step" id="cre-step">
                          <div class="cre-step-item active" data-step="1" data-target="step-jenjang" role="button" tabindex="0">
                            <span class="cre-step-num">1</span>
                            <span class="cre-step-label">Pilih Jenjang &amp; Jalur</span>
                          </div>
                          <div class="cre-step-sep"></div>
                          <div class="cre-step-item" data-step="2" data-target="school-dd" role="button" tabindex="0">
                            <span class="cre-step-num">2</span>
                            <span class="cre-step-label">Pilih Sekolah &amp; Jurusan</span>
                          </div>
                          <div class="cre-step-sep"></div>
                          <div class="cre-step-item" data-step="3" data-target="cre-side" role="button" tabindex="0">
                            <span class="cre-step-num">3</span>
                            <span class="cre-step-label">Ringkasan &amp; Konfirmasi</span>
                          </div>
                        </div>

                        <form method="POST" action="{{ route('registration.store') }}" novalidate id="reg-form">
                            @csrf
                            <div class="cre-grid">
                            <div class="cre-main">
                            @if($hasAge)
                                <div class="cre-alert info">
                                    <i class="fa-solid fa-cake-candles cre-alert-ic"></i>
                                    <div class="cre-alert-body">
                                        <p class="cre-alert-t">Usia Anda saat ini: <span style="color:var(--ink)">{{ $applicantAge }} tahun</span></p>
                                        <p class="cre-alert-p">(dari tanggal lahir di profil)</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Pilih Jenjang & Periode --}}
                            <div class="cre-sec" id="step-jenjang">
                                <div class="cre-sec-head">
                                    <div class="cre-sec-ic coral"><i class="fa-solid fa-school"></i></div>
                                    <div>
                                        <p class="cre-sec-ttl">Pilih Jenjang &amp; Periode <span style="color:var(--red)">*</span></p>
                                        <p class="cre-sec-desc">Pilih jenjang dan gelombang pendaftaran yang sedang dibuka.</p>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    @foreach ($periods as $period)
                                        @php
                                            $min = $ageMins[$period->id] ?? null;
                                            $blockedByAge = $hasAge && $min !== null && $applicantAge < $min;
                                            $pStatus = $period->registrationStatus();
                                            $isOpenPeriod = $pStatus === 'open';
                                            $isDisabled = $blockedByAge || !$isOpenPeriod;
                                            $isChecked = old('registration_period_id') == $period->id;
                                            $statusBadge = match($pStatus) {
                                                'not_started' => 'Belum Dibuka',
                                                'closed' => 'Sudah Ditutup',
                                                'inactive' => 'Nonaktif',
                                                default => 'Dibuka',
                                            };
                                            $badgeCls = match($pStatus) {
                                                'not_started' => 'bg-eggplore-warning-soft text-[#B98A2E] border-eggplore-warning',
                                                'closed' => 'bg-eggplore-danger-soft text-eggplore-danger border-eggplore-danger',
                                                'inactive' => 'bg-eggplore-neutral-100 text-eggplore-neutral-500 border-eggplore-neutral-300',
                                                default => 'bg-eggplore-success-soft text-eggplore-success border-eggplore-success',
                                            };
                                            $badgeIcon = match($pStatus) {
                                                'not_started' => 'fa-hourglass-half',
                                                'closed' => 'fa-circle-xmark',
                                                'inactive' => 'fa-circle-minus',
                                                default => 'fa-circle-check',
                                            };
                                        @endphp
                                        <label class="period-item relative flex items-start gap-4 rounded-card border p-4 transition-all cursor-pointer {{ $isDisabled ? 'bg-eggplore-neutral-100 border-eggplore-neutral-200 opacity-70 cursor-not-allowed' : ($blockedByAge ? 'bg-eggplore-danger-soft border-eggplore-danger/30' : 'bg-white border-eggplore-neutral-200 hover:border-eggplore-primary-400 hover:bg-eggplore-primary-50/40') }}">
                                            <input type="radio" name="registration_period_id" value="{{ $period->id }}" required {{ $isDisabled ? 'disabled' : '' }} {{ $isChecked ? 'checked' : '' }}
                                                class="period-radio mt-0.5 h-[18px] w-[18px] shrink-0 accent-eggplore-primary-500 focus:ring-2 focus:ring-eggplore-primary-400 focus:ring-offset-1"
                                                data-status="{{ $pStatus }}" data-start="{{ $period->start_date->format('Y-m-d') }}" data-end="{{ $period->end_date->format('Y-m-d') }}" data-level="{{ $period->school_level_id }}" data-name="{{ $period->schoolLevel->name }} - {{ $period->name }}">
                                            <span class="flex-1 min-w-0">
                                                <span class="flex flex-wrap items-center gap-2">
                                                    <span class="text-sm font-semibold text-eggplore-neutral-900">{{ $period->schoolLevel->name }}</span>
                                                    <span class="text-sm text-eggplore-neutral-500">- {{ $period->name }}</span>
                                                    <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-[11px] font-semibold {{ $badgeCls }}">
                                                        <i class="fa-solid {{ $badgeIcon }} text-[10px]"></i> {{ $statusBadge }}
                                                    </span>
                                                </span>
                                                <span class="mt-1 block font-mono text-xs text-eggplore-neutral-500">
                                                    {{ $period->start_date->format('d M Y') }} — {{ $period->end_date->format('d M Y') }}
                                                    @if($min !== null)
                                                        · Minimal {{ $min }} tahun
                                                        @if($blockedByAge)<span class="text-eggplore-danger font-semibold"> — belum memenuhi ({{ $applicantAge }} th)</span>@endif
                                                    @endif
                                                    @if($pStatus === 'not_started')
                                                        <span class="text-[#B98A2E] font-medium"> — akan dibuka {{ $period->start_date->format('d M Y') }}</span>
                                                    @elseif($pStatus === 'closed')
                                                        <span class="text-eggplore-danger font-medium"> — ditutup {{ $period->end_date->format('d M Y') }}</span>
                                                    @endif
                                                </span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('registration_period_id')
                                    <p class="mt-2 flex items-start gap-1.5 text-xs text-eggplore-danger">
                                        <i class="fa-solid fa-circle-exclamation mt-0.5 text-[11px]"></i>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                                <p id="age-period-hint" class="mt-2 flex items-center gap-1.5 text-xs"></p>
                                <p id="period-status-hint" class="mt-1 flex items-center gap-1.5 text-xs"></p>
                            </div>

                            {{-- Pilih Jalur Pendaftaran --}}
                            <div class="cre-sec">
                                <div class="cre-sec-head">
                                    <div class="cre-sec-ic blue"><i class="fa-solid fa-route"></i></div>
                                    <div>
                                        <p class="cre-sec-ttl">Pilih Jalur Pendaftaran <span style="color:var(--red)">*</span></p>
                                        <p class="cre-sec-desc">Pilih jalur masuk sesuai minat dan prestasimu.</p>
                                    </div>
                                </div>
                                <div class="space-y-3" id="track-list">
                                    @foreach ($tracks as $track)
                                        @php
                                            $isReguler = strtolower($track->name) === 'reguler';
                                            $isPrestasi = strtolower($track->name) === 'prestasi';
                                            $trackIcon = match(true) {
                                                $isReguler => 'fa-user-graduate',
                                                $isPrestasi => 'fa-trophy',
                                                default => 'fa-hand-holding-heart',
                                            };
                                            $trackIconCls = match(true) {
                                                $isReguler => 'bg-eggplore-primary-50 text-eggplore-primary-600',
                                                $isPrestasi => 'bg-eggplore-warning-soft text-[#B98A2E]',
                                                default => 'bg-eggplore-info-soft text-eggplore-info',
                                            };
                                            $trackBadge = $isReguler ? 'Populer' : 'Umum';
                                            $trackBadgeCls = $isReguler
                                                ? 'bg-eggplore-primary-50 text-eggplore-primary-700 border-eggplore-primary-200'
                                                : 'bg-eggplore-neutral-100 text-eggplore-neutral-500 border-eggplore-neutral-200';
                                        @endphp
                                        <label class="track-item relative flex items-start gap-4 rounded-card border border-eggplore-neutral-200 bg-white p-4 transition-all cursor-pointer hover:border-eggplore-primary-400 hover:bg-eggplore-primary-50/40" data-track-id="{{ $track->id }}" data-name="{{ $track->name }}">
                                            <input type="radio" name="registration_track_id" value="{{ $track->id }}" required
                                                class="track-radio sr-only">
                                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-input {{ $trackIconCls }}">
                                                <i class="fa-solid {{ $trackIcon }} text-lg"></i>
                                            </span>
                                            <span class="flex-1 min-w-0">
                                                <span class="flex flex-wrap items-center gap-2">
                                                    <span class="text-sm font-semibold text-eggplore-neutral-900">{{ $track->name }}</span>
                                                    <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide {{ $trackBadgeCls }}">{{ $trackBadge }}</span>
                                                </span>
                                                <span class="mt-0.5 block text-sm text-eggplore-neutral-500">{{ $track->description }}</span>
                                            </span>
                                            <span class="track-check hidden h-5 w-5 shrink-0 items-center justify-center rounded-full bg-eggplore-primary text-white">
                                                <i class="fa-solid fa-check text-[10px]"></i>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('registration_track_id')
                                    <p class="mt-2 flex items-start gap-1.5 text-xs text-eggplore-danger">
                                        <i class="fa-solid fa-circle-exclamation mt-0.5 text-[11px]"></i>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>

      {{-- Pilih Sekolah --}}
      <div class="cre-sec" id="school-dd">
        <div class="cre-sec-head">
          <div class="cre-sec-ic blue"><i class="fa-solid fa-building-columns"></i></div>
          <div>
            <p class="cre-sec-ttl">Pilih Sekolah <span style="color:var(--red)">*</span></p>
            <p class="cre-sec-desc">Pilih sekolah tujuan sesuai jenjang pilihan.</p>
          </div>
        </div>

                                {{-- Trigger (closed state) --}}
                                <button type="button" id="school-trigger"
                                    class="school-trigger flex w-full items-stretch gap-3 rounded-card border border-eggplore-neutral-200 bg-white p-3 text-left transition-colors focus-within:border-eggplore-primary-500 focus-within:ring-2 focus-within:ring-eggplore-primary-400/30 disabled:cursor-not-allowed disabled:bg-eggplore-neutral-100 disabled:opacity-70"
                                    aria-haspopup="listbox" aria-expanded="false" aria-controls="school-listbox" disabled>
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-input bg-eggplore-info-soft text-eggplore-info">
                                        <i class="fa-solid fa-school text-lg"></i>
                                    </span>
                                    <span class="flex flex-1 items-center justify-between gap-2">
                                        <span id="school-label" class="block min-w-0 text-sm text-eggplore-neutral-400">-- Pilih Sekolah --</span>
                                        <svg class="school-chevron h-4 w-4 shrink-0 text-eggplore-neutral-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"></path></svg>
                                    </span>
                                </button>

                                {{-- Panel (soft card inline) --}}
                                <div id="school-panel" class="grid transition-all duration-200 ease-out" style="grid-template-rows:0fr">
                                    <div class="overflow-hidden">
                                        <div class="mt-2 rounded-2xl border border-eggplore-primary-100 bg-eggplore-primary-50/30 p-2">
                                            <ul role="listbox" id="school-listbox" aria-labelledby="school-label"
                                                class="school-options max-h-56 overflow-y-auto">
                                                @foreach ($schools as $sc)
                                                    <li role="option"
                                                        data-value="{{ $sc->id }}"
                                                        data-levels="{{ $sc->schoolLevels->pluck('id')->join(',') }}"
                                                        aria-selected="false"
                                                        id="school-opt-{{ $sc->id }}"
                                                        class="school-option flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-eggplore-neutral-900 transition-colors hover:bg-white hover:shadow-xs {{ old('school_id') == $sc->id ? 'bg-eggplore-primary-50' : '' }}">
                                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-input bg-eggplore-neutral-100 text-eggplore-neutral-400">
                                                            <i class="fa-solid fa-building-columns text-xs"></i>
                                                        </span>
                                                        <span class="flex-1 truncate">{{ $sc->name }}</span>
                                                        <span class="school-check {{ old('school_id') == $sc->id ? 'flex' : 'hidden' }} h-5 w-5 shrink-0 items-center justify-center rounded-full bg-eggplore-primary text-white">
                                                            <i class="fa-solid fa-check text-[10px]"></i>
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Native select hidden sebagai source-of-truth (nilai form + syncMajors) --}}
                                <select id="school-select" name="school_id" required
                                        class="sr-only" aria-hidden="true" tabindex="-1">
                                    <option value="">-- Pilih Sekolah --</option>
                                    @foreach ($schools as $sc)
                                        <option value="{{ $sc->id }}" data-levels="{{ $sc->schoolLevels->pluck('id')->join(',') }}"
                                            {{ old('school_id') == $sc->id ? 'selected' : '' }}>{{ $sc->name }}</option>
                                    @endforeach
                                </select>

                                <p id="school-hint" class="mt-1.5 flex items-center gap-1.5 text-xs text-eggplore-neutral-400">
                                    <i class="fa-solid fa-circle-info text-[11px]"></i>
                                    <span>Pilih jenjang dulu untuk melihat sekolah yang tersedia.</span>
                                </p>
                                @error('school_id')
                                    <p class="mt-2 flex items-start gap-1.5 text-xs text-eggplore-danger">
                                        <i class="fa-solid fa-circle-exclamation mt-0.5 text-[11px]"></i>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>

                {{-- Jurusan Pilihan --}}
                <div id="major-section" class="cre-sec">
                    <div class="cre-sec-head">
                        <div class="cre-sec-ic amber"><i class="fa-solid fa-book-open"></i></div>
                        <div>
                            <p class="cre-sec-ttl">Jurusan Pilihan <span style="color:var(--red)">*</span></p>
                            <p class="cre-sec-desc">Pilih jurusan yang tersedia di sekolah tujuan.</p>
                        </div>
                    </div>

                                {{-- Trigger (closed state) --}}
                                <button type="button" id="major-trigger"
                                    class="major-trigger flex w-full items-stretch gap-3 rounded-card border border-eggplore-neutral-200 bg-white p-3 text-left transition-colors focus-within:border-eggplore-primary-500 focus-within:ring-2 focus-within:ring-eggplore-primary-400/30 disabled:cursor-not-allowed disabled:bg-eggplore-neutral-100 disabled:opacity-70"
                                    aria-haspopup="listbox" aria-expanded="false" aria-controls="major-listbox" disabled>
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-input bg-eggplore-warning-soft text-[#B98A2E]">
                                        <i class="fa-solid fa-book-open text-lg"></i>
                                    </span>
                                    <span class="flex flex-1 items-center justify-between gap-2">
                                        <span id="major-label" class="block min-w-0 text-sm text-eggplore-neutral-400">-- Pilih Jurusan --</span>
                                        <svg class="major-chevron h-4 w-4 shrink-0 text-eggplore-neutral-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"></path></svg>
                                    </span>
                                </button>

                                {{-- Panel (soft card inline) --}}
                                <div id="major-panel" class="grid transition-all duration-200 ease-out" style="grid-template-rows:0fr">
                                    <div class="overflow-hidden">
                                        <div class="mt-2 rounded-2xl border border-eggplore-warning/30 bg-eggplore-warning-soft/30 p-2">
                                            <ul role="listbox" id="major-listbox" aria-labelledby="major-label"
                                                class="major-options max-h-72 overflow-y-auto overscroll-contain">
                                                {{-- options diisi dinamis oleh JS (syncMajors) --}}
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Native select hidden sebagai source-of-truth (nilai form + syncQuota) --}}
                                <select id="major-select" name="major_id"
                                        class="sr-only" aria-hidden="true" tabindex="-1">
                                    <option value="">-- Pilih Jurusan --</option>
                                </select>

                                <p id="major-quota-hint" class="mt-1 flex items-center gap-1.5 text-xs text-eggplore-neutral-400">
                                    <i class="fa-solid fa-circle-info text-[11px]"></i>
                                    <span>Pilih sekolah dan jalur untuk melihat sisa kuota.</span>
                                </p>
                                @error('major_id')
                                    <p class="mt-2 flex items-start gap-1.5 text-xs text-eggplore-danger">
                                        <i class="fa-solid fa-circle-exclamation mt-0.5 text-[11px]"></i>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>

                            </div>{{-- /cre-main --}}

                            <aside class="cre-side" id="cre-side">
                              <div class="cre-sum">
                                <div class="cre-sum-hd">
                                  <div class="cre-sum-ava">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                                  <div>
                                    <p class="cre-sum-name">{{ auth()->user()->name }}</p>
                                    <p class="cre-sum-role">Pendaftar</p>
                                  </div>
                                  @if($hasAge)
                                    <span class="cre-sum-age"><i class="fa-solid fa-cake-candles"></i> {{ $applicantAge }} th</span>
                                  @endif
                                </div>
                                <div class="cre-sum-rows">
                                  <div class="cre-sum-row">
                                    <span class="lb"><i class="fa-solid fa-school"></i> Jenjang</span>
                                    <span class="val placeholder" id="sum-period">Belum dipilih</span>
                                  </div>
                                  <div class="cre-sum-row">
                                    <span class="lb"><i class="fa-solid fa-route"></i> Jalur</span>
                                    <span class="val placeholder" id="sum-track">Belum dipilih</span>
                                  </div>
                                  <div class="cre-sum-row">
                                    <span class="lb"><i class="fa-solid fa-building-columns"></i> Sekolah</span>
                                    <span class="val placeholder" id="sum-school">Belum dipilih</span>
                                  </div>
                                  <div class="cre-sum-row">
                                    <span class="lb"><i class="fa-solid fa-book-open"></i> Jurusan</span>
                                    <span class="val placeholder" id="sum-major">Belum dipilih</span>
                                  </div>
                                </div>
                                <div class="cre-sum-foot">
                                  @if($openCount === 0)
                                    <p class="cre-sum-err"><i class="fa-solid fa-circle-exclamation"></i> Tidak ada periode yang sedang dibuka — pendaftaran tidak dapat dilanjutkan.</p>
                                  @endif
                                  <a href="{{ route('registration.index') }}" class="cre-btn ghost sm">
                                    <i class="fa-solid fa-arrow-left"></i> Kembali
                                  </a>
                                  <button type="submit" id="submit-registration" class="cre-btn coral" @if($openCount === 0) disabled @endif>
                                    <i class="fa-solid fa-paper-plane"></i> Lanjut ke Review
                                  </button>
                                </div>
                              </div>
                            </aside>
                            </div>{{-- /cre-grid --}}

                            <div class="cre-bar">
                              <a href="{{ route('registration.index') }}" class="cre-btn ghost sm">
                                <i class="fa-solid fa-arrow-left"></i> Kembali
                              </a>
                              <button type="submit" form="reg-form" id="submit-bar" class="cre-btn coral" @if($openCount === 0) disabled @endif>
                                <i class="fa-solid fa-paper-plane"></i> Lanjut ke Review
                              </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

    @push('scripts')
    <script>
    (function(){
      const mins = @json($ageMins ?? []);
      const age = @json($applicantAge);
      const hint = document.getElementById('age-period-hint');
      const periodStatusHint = document.getElementById('period-status-hint');
      const submitBtn = document.getElementById('submit-registration');
      const openCount = @json($openCount ?? 0);

      function setHint(el, html, tone){
        if(!el) return;
        el.innerHTML = html;
        const base = 'mt-2 flex items-center gap-1.5 text-xs';
        const tones = {
          gray: 'text-eggplore-neutral-500',
          green: 'text-eggplore-success',
          red: 'text-eggplore-danger',
          amber: 'text-[#B98A2E]',
        };
        el.className = base + ' ' + (tones[tone] || tones.gray);
      }

      function selectedPeriod(){
        return document.querySelector('input[name="registration_period_id"]:checked:not(:disabled)');
      }

      function syncAgeHint(){
        const sel = selectedPeriod();
        if(!sel){ setHint(hint, '', 'gray'); return; }
        const min = mins[sel.value];
        if(min==null){ setHint(hint, '<i class="fa-solid fa-circle-check text-[11px]"></i> Jenjang ini tidak memiliki batas usia minimal.', 'green'); return; }
        if(age==null){ setHint(hint, '<i class="fa-solid fa-circle-info text-[11px]"></i> Minimal ' + min + ' tahun untuk jenjang ini.', 'gray'); return; }
        if(age < min){
          setHint(hint, '<i class="fa-solid fa-circle-exclamation text-[11px]"></i> Usia ' + age + ' tahun — belum memenuhi minimal ' + min + ' tahun untuk jenjang ini.', 'red');
        } else {
          setHint(hint, '<i class="fa-solid fa-circle-check text-[11px]"></i> Memenuhi batas minimal ' + min + ' tahun (usia ' + age + ' tahun).', 'green');
        }
      }

      function syncPeriodHint(){
        const sel = selectedPeriod();
        if(!sel){ setHint(periodStatusHint, '', 'gray'); syncAgeHint(); return; }
        const st = sel.getAttribute('data-status');
        if(st === 'not_started'){
          setHint(periodStatusHint, '<i class="fa-solid fa-hourglass-half text-[11px]"></i> Pendaftaran jenjang ini belum dibuka — akan dibuka pada ' + sel.getAttribute('data-start') + '. Tidak bisa melanjutkan.', 'amber');
        } else if(st === 'closed'){
          setHint(periodStatusHint, '<i class="fa-solid fa-circle-xmark text-[11px]"></i> Pendaftaran jenjang ini sudah ditutup pada ' + sel.getAttribute('data-end') + '. Tidak bisa melanjutkan.', 'red');
        } else if(st === 'open'){
          setHint(periodStatusHint, '<i class="fa-solid fa-circle-check text-[11px]"></i> Periode sedang dibuka — silakan lanjutkan pendaftaran.', 'green');
        } else {
          setHint(periodStatusHint, '', 'gray');
        }
        syncAgeHint();
      }

      function syncSubmit(){
        if(!submitBtn) return;
        const sel = selectedPeriod();
        const hasOpen = openCount > 0 && sel !== null && sel.getAttribute('data-status') === 'open';
        submitBtn.disabled = !hasOpen;
        const barBtn = document.getElementById('submit-bar');
        if(barBtn) barBtn.disabled = submitBtn.disabled;
      }

      function syncCards(){
        document.querySelectorAll('.period-item').forEach(function(item){
          const radio = item.querySelector('input[type="radio"]');
          if(!radio || radio.disabled) return;
          const checked = radio.checked;
          item.classList.toggle('border-eggplore-primary-500', checked);
          item.classList.toggle('ring-2', checked);
          item.classList.toggle('ring-eggplore-primary-100', checked);
          item.classList.toggle('bg-eggplore-primary-50', checked);
          item.classList.toggle('border-eggplore-neutral-200', !checked);
          item.classList.toggle('bg-white', !checked);
        });
        document.querySelectorAll('.track-item').forEach(function(item){
          const radio = item.querySelector('input[type="radio"]');
          if(!radio) return;
          const checked = radio.checked;
          item.classList.toggle('border-eggplore-primary-500', checked);
          item.classList.toggle('ring-2', checked);
          item.classList.toggle('ring-eggplore-primary-100', checked);
          item.classList.toggle('bg-eggplore-primary-50', checked);
          item.classList.toggle('border-eggplore-neutral-200', !checked);
          item.classList.toggle('bg-white', !checked);
          const check = item.querySelector('.track-check');
          if(check){
            check.classList.toggle('hidden', !checked);
            check.classList.toggle('flex', checked);
          }
        });
      }

      document.querySelectorAll('input[name="registration_period_id"]').forEach(function(r){
        r.addEventListener('change', syncPeriodHint);
        r.addEventListener('change', syncSubmit);
        r.addEventListener('change', syncSchools);
        r.addEventListener('change', syncTracks);
        r.addEventListener('change', syncCards);
      });
      document.querySelectorAll('input[name="registration_track_id"]').forEach(function(r){
        r.addEventListener('change', syncQuota);
        r.addEventListener('change', syncCards);
      });

      // Sekolah & jurusan dinamis berdasarkan jenjang terpilih
      const schools = @json($schoolOptionsJson);
      const majorsByLevel = @json($majorOptionsJson);
      const quotaMap = @json($quotaMap ?? []);
      const acceptedByMajorTrack = @json($acceptedByMajorTrack ?? []);
      const tracks = @json($tracks->keyBy('id')->map(fn($t)=>$t->name) ?? []);
      const trackStatusMap = @json($trackStatusMap ?? []);

      const schoolSelect = document.getElementById('school-select');
      const majorSelect = document.getElementById('major-select');
      const majorSection = document.getElementById('major-section');
      const schoolHint = document.getElementById('school-hint');
      const quotaHint = document.getElementById('major-quota-hint');

      const NO_MAJOR_LEVELS = ['1', '2', '3'];

      function getLevelId(){
        const el = selectedPeriod();
        return el ? el.getAttribute('data-level') : null;
      }
      function getTrackId(){ const el=document.querySelector('input[name="registration_track_id"]:checked'); return el?el.value:null; }
      function levelNeedsMajor(){ const l=getLevelId(); return l && !NO_MAJOR_LEVELS.includes(l); }

      function syncTracks(){
        const levelId = getLevelId();
        document.querySelectorAll('.track-item').forEach(function(item){
          const trackId = item.getAttribute('data-track-id');
          const active = levelId ? (trackStatusMap[levelId] ? !!trackStatusMap[levelId][trackId] : true) : true;
          const radio = item.querySelector('input[name="registration_track_id"]');
          if(!active){
            item.style.display = 'none';
            if(radio && radio.checked) radio.checked = false;
          } else {
            item.style.display = '';
          }
        });
        syncQuota();
      }

      function syncMajorSection(){
        const need = levelNeedsMajor();
        if(majorSection) majorSection.style.display = need ? '' : 'none';
        if(majorSelect){
          if(need){ majorSelect.setAttribute('required','required'); }
          else { majorSelect.removeAttribute('required'); majorSelect.value=''; }
        }
        // Sinkron label "(wajib)" & trigger custom
        if(reqLabel){
          reqLabel.textContent = need ? '(wajib)' : '';
        }
        if(!need){
          if(majorTrigger){ majorTrigger.disabled = true; }
          majorUpdateLabel('');
          majorMarkSelected('');
          majorClosePanel();
        } else {
          // Disabled sampai ada jenjang valid (level yang butuh jurusan)
          if(majorTrigger) majorTrigger.disabled = !getLevelId();
        }
      }
      function syncSchools(){
        const levelId = getLevelId();
        const hasLevel = !!levelId;

        // Sinkronkan visibility option native (untuk syncMajors internal)
        Array.from(schoolSelect.options).forEach(function(opt){
          if(!opt.value) return;
          const levels = (opt.getAttribute('data-levels')||'').split(',').map(function(v){return v.trim();});
          opt.style.display = (!levelId || levels.includes(levelId)) ? '' : 'none';
        });

        // Sinkronkan visibility item custom listbox
        let anyVisible = false;
        Array.from(document.querySelectorAll('.school-option')).forEach(function(item){
          const levels = (item.getAttribute('data-levels')||'').split(',').map(function(v){return v.trim();});
          const show = (!levelId || levels.includes(levelId));
          item.style.display = show ? '' : 'none';
          if(show) anyVisible = true;
        });

        // Reset pilihan jika sekolah yang dipilih tidak cocok jenjang baru
        const sel = schoolSelect.options[schoolSelect.selectedIndex];
        if(sel && sel.value && sel.getAttribute('data-levels') && !sel.getAttribute('data-levels').split(',').includes(levelId)){
          schoolSelect.value = '';
          schoolUpdateLabel('');
          schoolMarkSelected('');
        }

        // Disabled trigger bila belum pilih jenjang
        if(schoolTrigger) schoolTrigger.disabled = !hasLevel;

        // Pesan helper dinamis
        if(schoolHint){
          schoolHint.innerHTML = hasLevel
            ? (anyVisible
                ? '<i class="fa-solid fa-circle-info text-[11px]"></i> <span>Pilih sekolah yang tersedia untuk jenjang ini.</span>'
                : '<i class="fa-solid fa-circle-exclamation text-[11px]"></i> <span>Tidak ada sekolah untuk jenjang ini.</span>')
            : '<i class="fa-solid fa-circle-info text-[11px]"></i> <span>Pilih jenjang dulu untuk melihat sekolah yang tersedia.</span>';
        }

        syncMajorSection();
        syncMajors();
      }

      // ==========================================================
      // CUSTOM DROPDOWN SEKOLAH — SOFT CARD INLINE
      // ==========================================================
      const schoolTrigger = document.getElementById('school-trigger');
      const schoolPanel   = document.getElementById('school-panel');
      const schoolLabel   = document.getElementById('school-label');
      let schoolOpen = false;
      let schoolActiveIdx = -1;

      function schoolVisibleOptions(){
        return Array.from(document.querySelectorAll('.school-option')).filter(function(o){ return o.style.display !== 'none'; });
      }

      function schoolUpdateLabel(name){
        if(!schoolLabel) return;
        if(name){
          schoolLabel.textContent = name;
          schoolLabel.classList.remove('text-eggplore-neutral-400');
          schoolLabel.classList.add('text-eggplore-neutral-900');
        } else {
          schoolLabel.textContent = '-- Pilih Sekolah --';
          schoolLabel.classList.add('text-eggplore-neutral-400');
          schoolLabel.classList.remove('text-eggplore-neutral-900');
        }
      }

      function schoolMarkSelected(value){
        Array.from(document.querySelectorAll('.school-option')).forEach(function(item){
          const selected = String(item.getAttribute('data-value')) === String(value);
          item.setAttribute('aria-selected', selected ? 'true' : 'false');
          item.classList.toggle('bg-eggplore-primary-50', selected);
          const check = item.querySelector('.school-check');
          if(check){ check.classList.toggle('hidden', !selected); check.classList.toggle('flex', selected); }
        });
      }

      function schoolOpenPanel(){
        if(!schoolTrigger || schoolTrigger.disabled) return;
        schoolOpen = true;
        schoolPanel.style.gridTemplateRows = '1fr';
        schoolTrigger.setAttribute('aria-expanded','true');
        const chev = schoolTrigger.querySelector('.school-chevron');
        if(chev) chev.classList.add('rotate-180');
        schoolActiveIdx = -1;
      }
      function schoolClosePanel(){
        schoolOpen = false;
        schoolPanel.style.gridTemplateRows = '0fr';
        schoolTrigger.setAttribute('aria-expanded','false');
        const chev = schoolTrigger.querySelector('.school-chevron');
        if(chev) chev.classList.remove('rotate-180');
      }
      function schoolSelectOption(item){
        if(!item) return;
        const value = item.getAttribute('data-value');
        const name  = item.textContent.trim();
        // Sinkron native select (source of truth) + picu syncMajors
        schoolSelect.value = value;
        schoolUpdateLabel(name);
        schoolMarkSelected(value);
        schoolSelect.dispatchEvent(new Event('change', { bubbles: true }));
        schoolClosePanel();
        schoolTrigger.focus();
      }
      function schoolUpdateActive(){
        const v = schoolVisibleOptions();
        v.forEach(function(o, i){
          const active = i === schoolActiveIdx;
          o.classList.toggle('bg-eggplore-primary-100', active);
          if(active){ schoolTrigger.setAttribute('aria-activedescendant', o.id); o.scrollIntoView({block:'nearest'}); }
        });
      }

      if(schoolTrigger){
        schoolTrigger.addEventListener('click', function(){
          schoolOpen ? schoolClosePanel() : schoolOpenPanel();
        });
        schoolTrigger.addEventListener('keydown', function(e){
          const v = schoolVisibleOptions();
          if(e.key === 'Enter' || e.key === ' '){ e.preventDefault(); schoolOpen ? schoolClosePanel() : schoolOpenPanel(); }
          else if(e.key === 'Escape'){ schoolClosePanel(); }
          else if(e.key === 'ArrowDown'){ e.preventDefault(); if(!schoolOpen) schoolOpenPanel(); schoolActiveIdx = Math.min(schoolActiveIdx + 1, v.length - 1); schoolUpdateActive(); }
          else if(e.key === 'ArrowUp'){ e.preventDefault(); if(!schoolOpen) schoolOpenPanel(); schoolActiveIdx = Math.max(schoolActiveIdx - 1, 0); schoolUpdateActive(); }
          else if(e.key === 'Home'){ schoolActiveIdx = 0; schoolUpdateActive(); }
          else if(e.key === 'End'){ schoolActiveIdx = v.length - 1; schoolUpdateActive(); }
        });
      }

      // Klik item
      document.getElementById('school-listbox').addEventListener('click', function(e){
        const item = e.target.closest('.school-option');
        if(item && item.style.display !== 'none') schoolSelectOption(item);
      });

      // Klik di luar -> tutup
      document.addEventListener('click', function(e){
        const dd = document.getElementById('school-dd');
        if(dd && !dd.contains(e.target)) schoolClosePanel();
      });

      // Inisialisasi label & selected dari old()/value saat reload
      (function(){
        const cur = schoolSelect.options[schoolSelect.selectedIndex];
        if(cur && cur.value){
          schoolUpdateLabel(cur.textContent.trim());
          schoolMarkSelected(cur.value);
        }
      })();
      function syncMajors(){
        const levelId = getLevelId();
        const schoolId = schoolSelect.value;
        majorSelect.innerHTML = '<option value="">-- Pilih Jurusan --</option>';
        if(!levelNeedsMajor()){ majorRenderOptions([]); syncQuota(); return; }
        const majors = levelId ? (majorsByLevel[levelId] || []) : [];
        const options = schoolId
          ? majors.filter(function(m){ return String(m.school_id) === String(schoolId); })
          : majors;
        options.forEach(function(m){
          const opt = document.createElement('option');
          opt.value = m.id;
          opt.textContent = m.name;
          opt.dataset.fallbackQuota = m.quota;
          opt.dataset.fallbackUsed = m.used;
          majorSelect.appendChild(opt);
        });
        // Render item custom listbox dari data yang sama
        majorRenderOptions(options);
        // NOTE: schoolHint dikelola oleh syncSchools (pesan dinamis sesuai jenjang)
        syncQuota();
      }

      // ==========================================================
      // CUSTOM DROPDOWN JURUSAN — SOFT CARD INLINE
      // ==========================================================
      const majorTrigger = document.getElementById('major-trigger');
      const majorPanel   = document.getElementById('major-panel');
      const majorLabel   = document.getElementById('major-label');
      const reqLabel     = document.getElementById('major-required-label');
      let majorOpen = false;
      let majorActiveIdx = -1;
      let majorCurrentOptions = [];

      function majorRenderOptions(options){
        majorCurrentOptions = options || [];
        const list = document.getElementById('major-listbox');
        if(!list) return;
        if(majorCurrentOptions.length === 0){
          list.innerHTML = '<li class="px-4 py-6 text-center text-xs text-eggplore-neutral-400">Pilih sekolah dan jalur dulu untuk melihat daftar jurusan.</li>';
          return;
        }
        list.innerHTML = majorCurrentOptions.map(function(m){
          return '<li role="option" data-value="' + m.id + '" aria-selected="false" id="major-opt-' + m.id + '"'
            + ' class="major-option flex cursor-pointer items-center gap-3 rounded-lg px-3 py-3 text-sm text-eggplore-neutral-900 transition-colors hover:bg-white hover:shadow-xs">'
            + '<span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-input bg-eggplore-warning-soft text-[#B98A2E]"><i class="fa-solid fa-book text-xs"></i></span>'
            + '<span class="flex min-w-0 flex-1 flex-col gap-1">'
            +   '<span class="major-name text-[13px] font-medium leading-snug text-eggplore-neutral-900">' + m.name + '</span>'
            +   '<span class="major-q-badge"></span>'
            + '</span>'
            + '<span class="major-check hidden h-5 w-5 shrink-0 items-center justify-center rounded-full bg-eggplore-primary text-white"><i class="fa-solid fa-check text-[10px]"></i></span>'
            + '</li>';
        }).join('');
        majorSyncBadges();
        // Pulihkan selected bila ada nilai
        if(majorSelect.value) majorMarkSelected(majorSelect.value);
      }

      function majorBadgeFor(mid){
        const tid = getTrackId();
        if(!tid) return null;
        const quota = quotaMap[mid] && quotaMap[mid][tid] !== undefined ? quotaMap[mid][tid] : null;
        const used = acceptedByMajorTrack[mid] && acceptedByMajorTrack[mid][tid] !== undefined ? acceptedByMajorTrack[mid][tid] : 0;
        if(quota===null || quota===0) return { text:'Tanpa batas', cls:'bg-eggplore-neutral-100 text-eggplore-neutral-500 border-eggplore-neutral-200' };
        const open = Math.max(0, quota - used);
        if(open===0) return { text:'PENUH', cls:'bg-eggplore-danger-soft text-eggplore-danger border-eggplore-danger' };
        return { text:'Sisa '+open+'/'+quota, cls:'bg-eggplore-success-soft text-eggplore-success border-eggplore-success' };
      }

      function majorSyncBadges(){
        Array.from(document.querySelectorAll('.major-option')).forEach(function(item){
          const badge = item.querySelector('.major-q-badge');
          if(!badge) return;
          const b = majorBadgeFor(item.getAttribute('data-value'));
          badge.innerHTML = b
            ? '<span class="inline-flex shrink-0 items-center rounded-full border px-2 py-0.5 text-[10px] font-semibold ' + b.cls + '">' + b.text + '</span>'
            : '';
        });
      }

      function majorVisibleOptions(){
        return Array.from(document.querySelectorAll('.major-option')).filter(function(o){ return o.style.display !== 'none'; });
      }

      function majorUpdateLabel(name){
        if(!majorLabel) return;
        if(name){
          majorLabel.textContent = name;
          majorLabel.classList.remove('text-eggplore-neutral-400');
          majorLabel.classList.add('text-eggplore-neutral-900');
        } else {
          majorLabel.textContent = '-- Pilih Jurusan --';
          majorLabel.classList.add('text-eggplore-neutral-400');
          majorLabel.classList.remove('text-eggplore-neutral-900');
        }
      }

      function majorMarkSelected(value){
        Array.from(document.querySelectorAll('.major-option')).forEach(function(item){
          const selected = String(item.getAttribute('data-value')) === String(value);
          item.setAttribute('aria-selected', selected ? 'true' : 'false');
          item.classList.toggle('bg-eggplore-primary-50', selected);
          const check = item.querySelector('.major-check');
          if(check){ check.classList.toggle('hidden', !selected); check.classList.toggle('flex', selected); }
        });
      }

      function majorOpenPanel(){
        if(!majorTrigger || majorTrigger.disabled) return;
        majorOpen = true;
        majorPanel.style.gridTemplateRows = '1fr';
        majorTrigger.setAttribute('aria-expanded','true');
        const chev = majorTrigger.querySelector('.major-chevron');
        if(chev) chev.classList.add('rotate-180');
        majorActiveIdx = -1;
      }
      function majorClosePanel(){
        majorOpen = false;
        majorPanel.style.gridTemplateRows = '0fr';
        majorTrigger.setAttribute('aria-expanded','false');
        const chev = majorTrigger.querySelector('.major-chevron');
        if(chev) chev.classList.remove('rotate-180');
      }
      function majorSelectOption(item){
        if(!item) return;
        const value = item.getAttribute('data-value');
        const name  = item.querySelector('.major-name').textContent.trim();
        majorSelect.value = value;
        majorUpdateLabel(name);
        majorMarkSelected(value);
        majorSelect.dispatchEvent(new Event('change', { bubbles: true }));
        majorClosePanel();
        majorTrigger.focus();
      }
      function majorUpdateActive(){
        const v = majorVisibleOptions();
        v.forEach(function(o, i){
          const active = i === majorActiveIdx;
          o.classList.toggle('bg-eggplore-primary-100', active);
          if(active){ majorTrigger.setAttribute('aria-activedescendant', o.id); o.scrollIntoView({block:'nearest'}); }
        });
      }

      if(majorTrigger){
        majorTrigger.addEventListener('click', function(){
          majorOpen ? majorClosePanel() : majorOpenPanel();
        });
        majorTrigger.addEventListener('keydown', function(e){
          const v = majorVisibleOptions();
          if(e.key === 'Enter' || e.key === ' '){ e.preventDefault(); majorOpen ? majorClosePanel() : majorOpenPanel(); }
          else if(e.key === 'Escape'){ majorClosePanel(); }
          else if(e.key === 'ArrowDown'){ e.preventDefault(); if(!majorOpen) majorOpenPanel(); majorActiveIdx = Math.min(majorActiveIdx + 1, v.length - 1); majorUpdateActive(); }
          else if(e.key === 'ArrowUp'){ e.preventDefault(); if(!majorOpen) majorOpenPanel(); majorActiveIdx = Math.max(majorActiveIdx - 1, 0); majorUpdateActive(); }
          else if(e.key === 'Home'){ majorActiveIdx = 0; majorUpdateActive(); }
          else if(e.key === 'End'){ majorActiveIdx = v.length - 1; majorUpdateActive(); }
        });
      }

      // Klik item
      var majorListboxEl = document.getElementById('major-listbox');
      if(majorListboxEl) majorListboxEl.addEventListener('click', function(e){
        const item = e.target.closest('.major-option');
        if(item && item.style.display !== 'none') majorSelectOption(item);
      });

      // Klik di luar -> tutup
      document.addEventListener('click', function(e){
        const sec = document.getElementById('major-section');
        if(sec && !sec.contains(e.target)) majorClosePanel();
      });

      function syncQuota(){
        if(!majorSelect || !quotaHint) return;
        if(!levelNeedsMajor()){
          quotaHint.innerHTML = '<i class="fa-solid fa-circle-info text-[11px]"></i> Jenjang ini tidak memerlukan pemilihan jurusan.';
          quotaHint.className = 'mt-1 flex items-center gap-1.5 text-xs text-eggplore-neutral-500';
          return;
        }
        const tid = getTrackId();
        const mid = majorSelect.value;
        if(!tid || !mid){
          quotaHint.innerHTML = '<i class="fa-solid fa-circle-info text-[11px]"></i> Pilih jalur dan jurusan untuk melihat sisa kuota jalur tersebut.';
          quotaHint.className = 'mt-1 flex items-center gap-1.5 text-xs text-eggplore-neutral-500';
          syncOptions(); return;
        }
        const quota = quotaMap[mid] && quotaMap[mid][tid] !== undefined ? quotaMap[mid][tid] : null;
        const used = acceptedByMajorTrack[mid] && acceptedByMajorTrack[mid][tid] !== undefined ? acceptedByMajorTrack[mid][tid] : 0;
        const tname = tracks[tid] || 'jalur ini';
        if(quota===null || quota===0){
          quotaHint.innerHTML = '<i class="fa-solid fa-circle-info text-[11px]"></i> ' + tname + ': tanpa batas kuota.';
          quotaHint.className = 'mt-1 flex items-center gap-1.5 text-xs text-eggplore-neutral-500';
        }
        else {
          const open = Math.max(0, quota - used);
          const isFull = open === 0;
          quotaHint.innerHTML = (isFull ? '<i class="fa-solid fa-circle-exclamation text-[11px]"></i> ' : '<i class="fa-solid fa-circle-check text-[11px]"></i> ')
            + tname + ' — Sisa kuota: <span class="font-mono font-semibold">' + open + ' / ' + quota + '</span>' + (isFull ? ' (PENUH — pilih jalur lain)' : '');
          quotaHint.className = isFull
            ? 'mt-1 flex items-center gap-1.5 text-xs font-medium text-eggplore-danger'
            : 'mt-1 flex items-center gap-1.5 text-xs text-eggplore-success';
        }
        syncOptions();
      }
      function syncOptions(){
        const tid = getTrackId();
        if(!tid || !majorSelect) return;
        Array.from(majorSelect.options).forEach(function(opt){
          if(!opt.value) return;
          const mid = opt.value;
          const base = opt.textContent.split(' —')[0].trim();
          const quota = quotaMap[mid] && quotaMap[mid][tid] !== undefined ? quotaMap[mid][tid] : null;
          const used = acceptedByMajorTrack[mid] && acceptedByMajorTrack[mid][tid] !== undefined ? acceptedByMajorTrack[mid][tid] : 0;
          if(quota===null || quota===0) opt.textContent = base + ' (Tanpa batas)';
          else {
            const open = Math.max(0, quota - used);
            opt.textContent = base + ' — Sisa ' + tracks[tid] + ': ' + open + '/' + quota + (open===0?' (PENUH)':'');
          }
        });
        // Sinkron badge kuota di item custom listbox
        majorSyncBadges();
      }
      document.querySelectorAll('input[name="registration_track_id"]').forEach(function(r){ r.addEventListener('change', syncQuota); r.addEventListener('change', syncCards); });
      if(schoolSelect) schoolSelect.addEventListener('change', syncMajors);
      if(majorSelect) majorSelect.addEventListener('change', syncQuota);
      syncPeriodHint();
      syncAgeHint();
      syncSchools();
      syncMajorSection();
      syncTracks();
      syncQuota();
      syncSubmit();
      syncCards();
    })();
    </script>
    @endpush

    @push('scripts')
    <script>
    (function(){
      // ---------- Ringkasan Pendaftaran (sidebar) real-time ----------
      var $sum = {
        period: document.getElementById('sum-period'),
        track:  document.getElementById('sum-track'),
        school: document.getElementById('sum-school'),
        major:  document.getElementById('sum-major')
      };
      function setVal(el, text){
        if(!el) return;
        if(text){
          el.textContent = text;
          el.classList.remove('placeholder');
        } else {
          el.textContent = 'Belum dipilih';
          el.classList.add('placeholder');
        }
      }
      function updateSummary(){
        var pr = document.querySelector('input[name="registration_period_id"]:checked:not(:disabled)');
        setVal($sum.period, pr ? (pr.getAttribute('data-name') || 'Dipilih') : '');
        var tr = document.querySelector('input[name="registration_track_id"]:checked');
        var trItem = tr ? tr.closest('.track-item') : null;
        setVal($sum.track, trItem ? (trItem.getAttribute('data-name') || 'Dipilih') : '');
        var sl = document.getElementById('school-label');
        setVal($sum.school, sl && sl.textContent && sl.textContent.indexOf('Pilih Sekolah') === -1 ? sl.textContent.trim() : '');
        var ml = document.getElementById('major-label');
        setVal($sum.major, ml && ml.textContent && ml.textContent.indexOf('Pilih Jurusan') === -1 ? ml.textContent.trim() : '');
      }
      var form = document.getElementById('reg-form');
      if(form){
        form.addEventListener('change', function(e){
          var t = e.target;
          if(t && t.name === 'registration_period_id') updateSummary();
          else if(t && t.name === 'registration_track_id') updateSummary();
          else if(t && (t.id === 'school-select' || t.name === 'school_id')) updateSummary();
          else if(t && (t.id === 'major-select' || t.name === 'major_id')) updateSummary();
        });
      }
      // init
      updateSummary();

      // ---------- Wizard stepper ----------
      var steps = Array.prototype.slice.call(document.querySelectorAll('.cre-step-item[data-target]'));
      var targets = {};
      steps.forEach(function(s){
        var id = s.getAttribute('data-target');
        var el = document.getElementById(id);
        if(el) targets[id] = el;
        s.addEventListener('click', function(){
          var target = document.getElementById(id);
          if(target) target.scrollIntoView({behavior:'smooth', block:'start'});
        });
        s.addEventListener('keydown', function(e){
          if(e.key === 'Enter' || e.key === ' '){ e.preventDefault(); s.click(); }
        });
      });
      function setActive(id){
        steps.forEach(function(s){
          var on = s.getAttribute('data-target') === id;
          s.classList.toggle('active', on);
          var done = !on;
          s.classList.toggle('done', done);
        });
      }
      // Scroll-based active detection
      var secs = [
        {id:'step-jenjang', step:'step-jenjang'},
        {id:'school-dd', step:'school-dd'},
        {id:'major-section', step:'cre-side'}
      ];
      var lastActive = 'step-jenjang';
      function onScroll(){
        var vh = window.innerHeight;
        var cur = lastActive;
        secs.forEach(function(s){
          var el = document.getElementById(s.id);
          if(!el) return;
          var r = el.getBoundingClientRect();
          // step 1 covers top+jenjang section; step 2 covers school section; step 3 sidebar
          if(r.top < vh * 0.45 && r.bottom > 0) cur = s.step;
        });
        if(cur !== lastActive){
          lastActive = cur;
          setActive(cur);
        }
      }
      window.addEventListener('scroll', onScroll, {passive:true});
      // initial active = step 1
      setActive('step-jenjang');
    })();
    </script>
    @endpush
    </x-student-layout>

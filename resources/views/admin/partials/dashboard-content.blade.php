<style>
  /* ===================== DASHBOARD — Bringova (no cards, blended) ===================== */
  .dash {
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
    --ink: #1a1a2e;
    --muted: #8a8f9d;
    --divider: rgba(26, 26, 46, 0.10);

    position: relative;
    border-radius: 24px;
    padding: 28px 28px 44px;
    background: #f6f7fb;
    overflow: hidden;
  }

  /* ---------- header ---------- */
  .dash .d-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
  .dash .d-crumb a { color: var(--coral); text-decoration: none; }
  .dash .d-crumb a:hover { text-decoration: underline; }
  .dash .d-crumb .sep { color: #d3d6de; }
  .dash .d-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 2px; }
  .dash .d-meta { font-size: 13px; color: var(--muted); margin-bottom: 22px; }
  .dash .d-meta b { color: var(--ink); font-weight: 600; }

  /* ---------- tabs (underline, no box) ---------- */
  .dash .d-tabs { display: flex; gap: 22px; border-bottom: 1px solid var(--divider); margin-bottom: 24px; }
  .dash .d-tab {
    padding: 9px 2px 11px;
    font-size: 13.5px; font-weight: 600; color: var(--muted);
    text-decoration: none; display: inline-flex; align-items: center; gap: 7px;
    border-bottom: 2.5px solid transparent; margin-bottom: -1px;
    transition: color .18s ease;
  }
  .dash .d-tab:hover { color: var(--ink); }
  .dash .d-tab.active { color: var(--coral); border-bottom-color: var(--coral); }
  .dash .d-tab .badge { background: transparent; border: 1px solid currentColor; color: var(--coral); border-radius: 20px; padding: 1px 8px; font-size: 10.5px; font-weight: 700; }
  .dash .d-tab.active .badge { background: transparent; border: 1px solid currentColor; color: #fff; }

  /* ---------- summary stats (no box, icon badge only) ---------- */
  .dash .d-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 8px; }
  @media (max-width: 1024px) { .dash .d-stats { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 640px)  { .dash .d-stats { grid-template-columns: 1fr; } }

  .dash .d-stat {
    display: flex; align-items: center; gap: 13px;
    text-decoration: none; padding: 12px 14px; border-radius: 16px;
    transition: background-color .18s ease;
  }
  .dash .d-stat:hover { background: rgba(255, 255, 255, 0.55); }
  .dash .d-ic {
    flex: 0 0 auto; width: 48px; height: 48px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center; font-size: 19px;
  }
  .dash .d-stat.t-coral   .d-ic { background: var(--coral-soft);  color: var(--coral); }
  .dash .d-stat.t-amber   .d-ic { background: var(--amber-soft);  color: var(--amber); }
  .dash .d-stat.t-green   .d-ic { background: var(--green-soft);  color: var(--green); }
  .dash .d-stat.t-blue    .d-ic { background: var(--blue-soft);   color: var(--blue); }
  .dash .d-stat.t-purple  .d-ic { background: var(--purple-soft); color: var(--purple); }
  .dash .d-stat.t-red     .d-ic { background: var(--red-soft);    color: var(--red); }
  .dash .d-body { min-width: 0; }
  .dash .d-label { font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 2px; }
  .dash .d-value { font-size: 23px; font-weight: 800; color: var(--ink); line-height: 1.1; }
  .dash .d-sub { font-size: 11px; color: var(--muted); margin-top: 2px; }

  /* ---------- sections (no box, separated by divider) ---------- */
  .dash .d-sec { border-top: 1px solid var(--divider); padding: 26px 0 4px; }
  .dash .d-sec-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
  .dash .d-sec-title { font-size: 16px; font-weight: 700; color: var(--ink); display: flex; align-items: center; gap: 9px; }
  .dash .d-sec-title i { color: var(--coral); }

  /* rows (deadline / recent) — separated by divider line */
  .dash .d-row { display: flex; align-items: center; gap: 14px; padding: 13px 0; border-bottom: 1px solid var(--divider); }
  .dash .d-row:last-child { border-bottom: none; }
  .dash .d-row-ic { flex: 0 0 auto; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
  .dash .d-row.warning .d-row-ic { background: var(--amber-soft); color: var(--amber); }
  .dash .d-row.danger   .d-row-ic { background: var(--red-soft);   color: var(--red); }
  .dash .d-row.upcoming .d-row-ic { background: var(--blue-soft);  color: var(--blue); }
  .dash .d-row-info { flex: 1; min-width: 0; }
  .dash .d-row-name { font-size: 13.5px; font-weight: 600; color: var(--ink); }
  .dash .d-row-name .num { font-size: 11px; color: var(--muted); font-weight: 500; margin-left: 6px; }
  .dash .d-row-meta { font-size: 12px; color: var(--muted); margin-top: 2px; }
  .dash .d-row-meta span { margin: 0 4px; }
  .dash .d-tag {
    flex: 0 0 auto; font-size: 11px; font-weight: 700; padding: 4px 11px;
    border-radius: 20px; white-space: nowrap;
  }
  .dash .d-row.warning .d-tag { background: var(--amber-soft); color: var(--amber); }
  .dash .d-row.danger   .d-tag { background: var(--red-soft);   color: var(--red); }
  .dash .d-row.upcoming .d-tag { background: var(--blue-soft);  color: var(--blue); }

  /* status pills (recent) */
  .dash .d-pill { flex: 0 0 auto; font-size: 11px; font-weight: 700; padding: 5px 11px; border-radius: 20px; white-space: nowrap; }
  .dash .d-pill.p-pending  { background: transparent; border: 1px solid currentColor; color: var(--amber); }
  .dash .d-pill.p-verified { background: transparent; border: 1px solid currentColor;   color: var(--blue); }
  .dash .d-pill.p-accepted { background: transparent; border: 1px solid currentColor;  color: var(--green); }
  .dash .d-pill.p-rejected { background: transparent; border: 1px solid currentColor;    color: var(--red); }
  .dash .d-pill.p-canceled { background: transparent; border: 1px solid currentColor;            color: var(--muted); }

  .dash .d-empty { text-align: center; padding: 30px 16px; color: var(--muted); font-size: 13px; }
  .dash .d-link { font-size: 13px; font-weight: 600; color: var(--coral); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
  .dash .d-link:hover { color: #e8555b; }

  /* verification form (blended input) */
  .dash .d-verify-input {
    width: 100%; margin-top: 6px; padding: 11px 13px;
    border: 1px solid rgba(26, 26, 46, 0.14); border-radius: 12px;
    background: rgba(255, 255, 255, 0.35); color: var(--ink);
    font-family: 'Inter', monospace; letter-spacing: 1.5px; text-transform: uppercase;
    font-size: 14px; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
    transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
  }
  .dash .d-verify-input:focus {
    outline: none; border-color: var(--coral);
    box-shadow: 0 0 0 4px rgba(255, 107, 107, 0.14); background: rgba(255, 255, 255, 0.55);
  }
  .dash .d-btn-coral {
    background: linear-gradient(135deg, var(--coral), var(--coral-2));
    color: #fff; border: none; border-radius: 12px; padding: 11px 22px;
    font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 7px;
    cursor: pointer; box-shadow: 0 8px 18px -8px rgba(255, 107, 107, 0.6);
    transition: transform .15s ease, filter .15s ease;
  }
  .dash .d-btn-coral:hover { filter: brightness(1.03); transform: translateY(-1px); }
  .dash .d-btn-coral:active { transform: translateY(0); }
</style>

<div class="dash">

  <div class="d-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span>Overview</span>
  </div>
  <h1 class="d-title">Dashboard Admin</h1>
  <p class="d-meta">Terakhir diperbarui <b>{{ now()->format('d M, Y') }}</b></p>

  <div class="d-tabs">
    <a href="{{ route('admin.dashboard') }}" class="d-tab active" data-nav="dashboard">Overview</a>
    <a href="{{ route('admin.registrations.index') }}" class="d-tab" data-nav="registrations">Pendaftaran <span class="badge">{{ $stats['total'] }}</span></a>
  </div>

  <div class="d-stats">
    <a class="d-stat t-coral" href="{{ route('admin.registrations.index') }}">
      <span class="d-ic"><x-hi name="file-01" /></span>
      <div class="d-body">
        <div class="d-label">Total Pendaftaran</div>
        <div class="d-value">{{ $stats['total'] }}</div>
      </div>
    </a>
    <a class="d-stat t-amber" href="{{ route('admin.registrations.index', ['status' => 'pending']) }}">
      <span class="d-ic"><x-hi name="clock-01" /></span>
      <div class="d-body">
        <div class="d-label">Menunggu Verifikasi</div>
        <div class="d-value">{{ $stats['pending'] }}</div>
      </div>
    </a>
    <a class="d-stat t-green" href="{{ route('admin.registrations.index', ['status' => 'accepted']) }}">
      <span class="d-ic"><x-hi name="checkmark" /></span>
      <div class="d-body">
        <div class="d-label">Diterima</div>
        <div class="d-value">{{ $stats['accepted'] }}</div>
      </div>
    </a>
    <a class="d-stat t-blue" href="{{ route('admin.payments.index', ['status' => 'verified']) }}">
      <span class="d-ic"><x-hi name="money-01" /></span>
      <div class="d-body">
        <div class="d-label">Pembayaran Lunas</div>
        <div class="d-value">{{ $stats['payment_paid'] }}</div>
      </div>
    </a>
    <a class="d-stat t-purple" href="{{ route('admin.registrations.index', ['status' => 're_registration_complete']) }}">
      <span class="d-ic"><x-hi name="user-check-01" /></span>
      <div class="d-body">
        <div class="d-label">Daftar Ulang</div>
        <div class="d-value">{{ $stats['registered'] }}</div>
      </div>
    </a>
    <a class="d-stat t-red" href="{{ route('admin.registrations.index', ['deadline' => 1]) }}">
      <span class="d-ic"><x-hi name="alert-02" /></span>
      <div class="d-body">
        <div class="d-label">Deadline</div>
        <div class="d-value">{{ $deadlineTotal }}</div>
        <div class="d-sub">dengan batas waktu</div>
      </div>
    </a>
  </div>

  @if ($expiredRegistrations->isNotEmpty() || $nearDeadlineRegistrations->isNotEmpty() || $upcomingDeadlineRegistrations->isNotEmpty())
    <div class="d-sec">
      <div class="d-sec-head">
        <div class="d-sec-title"><x-hi name="notification-01" /> Notifikasi Deadline</div>
      </div>
      @foreach ($nearDeadlineRegistrations as $reg)
        <div class="d-row warning">
          <span class="d-row-ic"><x-hi name="clock-01" /></span>
          <div class="d-row-info">
            <div class="d-row-name">{{ $reg->applicant->full_name ?? 'N/A' }} <span class="num">{{ $reg->registration_number }}</span></div>
            <div class="d-row-meta">Sisa {{ $reg->getDeadlineLabel() }} &middot; batas {{ $reg->deadline_at->format('d M Y H:i') }}</div>
          </div>
          <span class="d-tag">Hampir Lewat</span>
          <a href="{{ route('admin.registrations.show', $reg) }}" class="d-btn-coral" style="padding:7px 14px;font-size:12px;">Detail</a>
        </div>
      @endforeach
      @foreach ($expiredRegistrations as $reg)
        <div class="d-row danger">
          <span class="d-row-ic"><x-hi name="alert-02" /></span>
          <div class="d-row-info">
            <div class="d-row-name">{{ $reg->applicant->full_name ?? 'N/A' }} <span class="num">{{ $reg->registration_number }}</span></div>
            <div class="d-row-meta">Menunggu pembatalan otomatis &middot; batas {{ $reg->deadline_at->format('d M Y H:i') }}</div>
          </div>
          <span class="d-tag">Lewat Batas</span>
          <a href="{{ route('admin.registrations.show', $reg) }}" class="d-btn-coral" style="padding:7px 14px;font-size:12px;">Detail</a>
        </div>
      @endforeach
      @foreach ($upcomingDeadlineRegistrations as $reg)
        <div class="d-row upcoming">
          <span class="d-row-ic"><x-hi name="calendar-01" /></span>
          <div class="d-row-info">
            <div class="d-row-name">{{ $reg->applicant->full_name ?? 'N/A' }} <span class="num">{{ $reg->registration_number }}</span></div>
            <div class="d-row-meta">Batas {{ $reg->deadline_at->format('d M Y H:i') }} &middot; sisa {{ $reg->getDeadlineLabel() }}</div>
          </div>
          <span class="d-tag">Akan Datang</span>
          <a href="{{ route('admin.registrations.show', $reg) }}" class="d-btn-coral" style="padding:7px 14px;font-size:12px;">Detail</a>
        </div>
      @endforeach
    </div>
  @endif

  <div class="d-sec">
    <div class="d-sec-head">
      <div class="d-sec-title"><x-hi name="qr-code" /> Verifikasi Kode Daftar Ulang (Offline)</div>
    </div>
    @if (session('success'))
      <div class="alert alert-success" style="margin-bottom:12px;">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="alert alert-error" style="margin-bottom:12px;">{{ session('error') }}</div>
    @endif
    @if (session('info'))
      <div class="alert alert-info" style="margin-bottom:12px;">{{ session('info') }}</div>
    @endif
    <p style="font-size:13px;color:var(--muted);margin-bottom:12px;">Masukkan kode verifikasi yang tertera pada kartu daftar ulang siswa. Setelah diverifikasi, status siswa otomatis menjadi terdaftar.</p>
    <form method="POST" action="{{ route('admin.re-registrations.verify-code') }}" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
      @csrf
      <div style="flex:1;min-width:220px;">
        <label for="verification_code" style="font-size:12px;color:var(--muted);font-weight:600;">Kode Verifikasi</label>
        <input id="verification_code" name="verification_code" type="text" required maxlength="20" placeholder="Mis. K7QZ2LMX" value="{{ old('verification_code') }}" class="d-verify-input">
        @error('verification_code')<div style="font-size:12px;color:var(--red);margin-top:5px;">{{ $message }}</div>@enderror
      </div>
      <button type="submit" class="d-btn-coral"><x-hi name="shield-01" /> Verifikasi</button>
    </form>
  </div>

  <div class="d-sec">
    <div class="d-sec-head">
      <div class="d-sec-title"><x-hi name="file-01" /> Pendaftaran Terbaru</div>
      <a href="{{ route('admin.registrations.index') }}" class="d-link">Lihat Semua <x-hi name="arrow-right-01" style="font-size:10px" /></a>
    </div>

    @if ($recentRegistrations->isEmpty())
      <div class="d-empty">Belum ada pendaftaran</div>
    @else
      @foreach ($recentRegistrations as $reg)
      <div class="d-row">
        <span class="d-row-ic" style="background:var(--coral-soft);color:var(--coral);"><x-hi name="file-01" /></span>
        <div class="d-row-info">
          <div class="d-row-name">{{ $reg->applicant->full_name ?? 'N/A' }}</div>
          <div class="d-row-meta">{{ $reg->registration_number }}<span>&middot;</span>{{ $reg->registrationPeriod->schoolLevel->name ?? '-' }}<span>&middot;</span>{{ $reg->created_at->format('d M Y') }}</div>
        </div>
        @php
          $smap = [
            'pending' => ['label' => 'Menunggu', 'pill' => 'p-pending'],
            'verified' => ['label' => 'Terverifikasi', 'pill' => 'p-verified'],
            'rejected' => ['label' => 'Ditolak', 'pill' => 'p-rejected'],
            'accepted' => ['label' => 'Diterima', 'pill' => 'p-accepted'],
            're_registration_complete' => ['label' => 'Daftar Ulang', 'pill' => 'p-accepted'],
            'canceled' => ['label' => 'Dibatalkan', 'pill' => 'p-canceled'],
            'withdrawn' => ['label' => 'Mengundurkan', 'pill' => 'p-rejected'],
          ];
          $s = $smap[$reg->status] ?? ['label' => ucfirst(str_replace('_',' ',$reg->status)), 'pill' => 'p-pending'];
        @endphp
        <span class="d-pill {{ $s['pill'] }}">{{ $s['label'] }}</span>
      </div>
      @endforeach
    @endif
  </div>

</div>
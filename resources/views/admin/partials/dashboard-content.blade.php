<div class="breadcrumb">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <span class="sep">/</span>
  <span>Overview</span>
</div>
<h1 class="page-title">Dashboard Admin</h1>

<div class="deal-meta">
  <span class="created">Last updated <span>{{ now()->format('d M, Y') }}</span></span>
</div>

<div class="tabs">
  <a href="{{ route('admin.dashboard') }}" class="tab active" data-nav="dashboard">Overview</a>
  <a href="{{ route('admin.registrations.index') }}" class="tab" data-nav="registrations">Pendaftaran <span class="tab-badge">{{ $stats['total'] }}</span></a>
</div>

<div class="summary-cards">
  <a class="summary-card" href="{{ route('admin.registrations.index') }}">
    <div class="label"><i class="fa-regular fa-file-alt"></i> Total Pendaftaran</div>
    <div class="value">{{ $stats['total'] }}</div>
  </a>
  <a class="summary-card" href="{{ route('admin.registrations.index', ['status' => 'pending']) }}">
    <div class="label"><i class="fa-regular fa-clock"></i> Menunggu Verifikasi</div>
    <div class="value">{{ $stats['pending'] }}</div>
  </a>
  <a class="summary-card" href="{{ route('admin.registrations.index', ['status' => 'accepted']) }}">
    <div class="label"><i class="fa-solid fa-check"></i> Diterima</div>
    <div class="value">{{ $stats['accepted'] }}</div>
  </a>
  <a class="summary-card" href="{{ route('admin.payments.index', ['status' => 'verified']) }}">
    <div class="label"><i class="fa-solid fa-money-bill"></i> Pembayaran Lunas</div>
    <div class="value">{{ $stats['payment_paid'] }}</div>
  </a>
  <a class="summary-card" href="{{ route('admin.registrations.index', ['status' => 're_registration_complete']) }}">
    <div class="label"><i class="fa-solid fa-user-check"></i> Daftar Ulang</div>
    <div class="value">{{ $stats['registered'] }}</div>
  </a>
  <a class="summary-card" href="{{ route('admin.registrations.index', ['deadline' => 1]) }}">
    <div class="label"><i class="fa-solid fa-triangle-exclamation"></i> Deadline</div>
    <div class="value">
      {{ $deadlineTotal }}
      <small>dengan batas waktu</small>
    </div>
  </a>
</div>

@if ($expiredRegistrations->isNotEmpty() || $nearDeadlineRegistrations->isNotEmpty() || $upcomingDeadlineRegistrations->isNotEmpty())
  <div class="deadline-notif">
    <div class="section-header">
      <h3><i class="fa-solid fa-bell"></i> Notifikasi Deadline</h3>
    </div>
    <div class="deadline-list">
      @foreach ($nearDeadlineRegistrations as $reg)
        <div class="deadline-item warning">
          <div class="deadline-icon"><i class="fa-solid fa-clock"></i></div>
          <div class="deadline-info">
            <div class="deadline-name">{{ $reg->applicant->full_name ?? 'N/A' }} <span class="deadline-num">{{ $reg->registration_number }}</span></div>
            <div class="deadline-meta">Hampir melewati batas waktu &middot; sisa {{ $reg->getDeadlineLabel() }} &middot; batas {{ $reg->deadline_at->format('d M Y H:i') }}</div>
          </div>
          <a href="{{ route('admin.registrations.show', $reg) }}" class="btn btn-outline">Detail</a>
        </div>
      @endforeach
      @foreach ($expiredRegistrations as $reg)
        <div class="deadline-item danger">
          <div class="deadline-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
          <div class="deadline-info">
            <div class="deadline-name">{{ $reg->applicant->full_name ?? 'N/A' }} <span class="deadline-num">{{ $reg->registration_number }}</span></div>
            <div class="deadline-meta">Melewati batas waktu &middot; menunggu pembatalan otomatis &middot; batas {{ $reg->deadline_at->format('d M Y H:i') }}</div>
          </div>
          <a href="{{ route('admin.registrations.show', $reg) }}" class="btn btn-outline">Detail</a>
        </div>
      @endforeach
      @foreach ($upcomingDeadlineRegistrations as $reg)
        <div class="deadline-item upcoming">
          <div class="deadline-icon"><i class="fa-regular fa-calendar"></i></div>
          <div class="deadline-info">
            <div class="deadline-name">{{ $reg->applicant->full_name ?? 'N/A' }} <span class="deadline-num">{{ $reg->registration_number }}</span></div>
            <div class="deadline-meta">Batas waktu {{ $reg->deadline_at->format('d M Y H:i') }} &middot; sisa {{ $reg->getDeadlineLabel() }}</div>
          </div>
          <a href="{{ route('admin.registrations.show', $reg) }}" class="btn btn-outline">Detail</a>
        </div>
      @endforeach
    </div>
  </div>
@endif

<div class="section-header" style="margin-top:24px;">
  <h3><i class="fa-solid fa-qrcode"></i> Verifikasi Kode Daftar Ulang (Offline)</h3>
</div>
<div style="background:var(--card-bg);border:1px solid var(--border);border-radius:10px;padding:16px;margin-bottom:24px;">
  @if (session('success'))
    <div class="alert alert-success" style="margin-bottom:12px;">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="alert alert-error" style="margin-bottom:12px;">{{ session('error') }}</div>
  @endif
  @if (session('info'))
    <div class="alert alert-info" style="margin-bottom:12px;">{{ session('info') }}</div>
  @endif
  <p style="font-size:13px;color:var(--tx2);margin-bottom:10px;">Masukkan kode verifikasi yang tertera pada kartu daftar ulang siswa. Setelah diverifikasi, status siswa otomatis menjadi terdaftar.</p>
  <form method="POST" action="{{ route('admin.re-registrations.verify-code') }}" style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;">
    @csrf
    <div style="flex:1;min-width:200px;">
      <label for="verification_code" style="font-size:12px;color:var(--tx2);">Kode Verifikasi</label>
      <input id="verification_code" name="verification_code" type="text" required maxlength="20" placeholder="Mis. K7QZ2LMX" value="{{ old('verification_code') }}" style="width:100%;margin-top:4px;padding:8px 10px;border:1px solid var(--input-border);border-radius:6px;background:var(--input-bg);color:var(--tx-body);font-family:monospace;letter-spacing:1px;text-transform:uppercase;">
      @error('verification_code')<div style="font-size:12px;color:var(--danger);margin-top:4px;">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn btn-primary">Verifikasi</button>
  </form>
</div>

<div class="section-header">
  <h3>Pendaftaran Terbaru</h3>
  <a href="{{ route('admin.registrations.index') }}" class="go-to-deals">Lihat Semua <i class="fa-solid fa-chevron-right" style="font-size:10px"></i></a>
</div>

@if ($recentRegistrations->isEmpty())
  <div class="empty-state">Belum ada pendaftaran</div>
@else
  <div class="doc-list">
    @foreach ($recentRegistrations as $reg)
    <div class="doc-row">
      <div class="doc-icon"><i class="fa-regular fa-file-lines"></i></div>
      <div class="doc-info">
        <div class="doc-name">{{ $reg->applicant->full_name ?? 'N/A' }}</div>
        <div class="doc-meta">{{ $reg->registration_number }} <span>&middot;</span> {{ $reg->registrationPeriod->schoolLevel->name ?? '-' }} <span>&middot;</span> {{ $reg->created_at->format('d M Y') }}</div>
      </div>
      <div class="doc-status">
        @php
          $smap = [
            'pending' => ['label' => 'Menunggu Verifikasi', 'cls' => 'status-pending'],
            'verified' => ['label' => 'Terverifikasi', 'cls' => 'status-verified'],
            'rejected' => ['label' => 'Ditolak', 'cls' => 'status-rejected'],
            'accepted' => ['label' => 'Diterima', 'cls' => 'status-accepted'],
            're_registration_complete' => ['label' => 'Daftar Ulang Selesai', 'cls' => 'status-accepted'],
            'canceled' => ['label' => 'Dibatalkan', 'cls' => 'status-rejected'],
            'withdrawn' => ['label' => 'Mengundurkan Diri', 'cls' => 'status-rejected'],
          ];
          $s = $smap[$reg->status] ?? ['label' => ucfirst(str_replace('_',' ',$reg->status)), 'cls' => 'status-pending'];
        @endphp
        <span class="status-badge {{ $s['cls'] }}">{{ $s['label'] }}</span>
      </div>
    </div>
    @endforeach
  </div>
@endif

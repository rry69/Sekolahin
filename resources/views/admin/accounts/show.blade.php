@extends('layouts.dashboard')
@section('title', 'Detail Akun Siswa')
@section('content')
@php
    $regStatusMap = [
        'pending' => 'status-pending',
        'verified' => 'status-verified',
        'rejected' => 'status-rejected',
        'accepted' => 'status-accepted',
        're_registration_complete' => 'status-accepted',
        'canceled' => 'status-pending',
        'withdrawn' => 'status-pending',
    ];
    $genderLabel = fn ($g) => $g === 'L' ? 'Laki-laki' : ($g === 'P' ? 'Perempuan' : $g);
    $alamatParts = [];
    if ($applicant) {
        $alamatParts = array_filter([
            $applicant->address,
            trim(($applicant->rt ? 'RT ' . $applicant->rt : '') . ($applicant->rw ? ' / RW ' . $applicant->rw : '')) ?: null,
            $applicant->village,
            $applicant->district,
            $applicant->city,
            $applicant->province,
            $applicant->postal_code,
        ], fn ($v) => $v !== null && $v !== '');
    }
    $resetPasswordShown = !empty(session('reset_password_' . $user->id)) ? session('reset_password_' . $user->id) : null;
    $allDocs = $registrations->flatMap(fn ($r) => $r->documents->map(fn ($d) => ['doc' => $d, 'reg' => $r]));
@endphp

<style>
  /* ===================== DETAIL AKUN SISWA — Bringova (no cards, scoped) ===================== */
  .acd {
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
  .acd .acd-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
  .acd .acd-crumb a { color: var(--coral); text-decoration: none; }
  .acd .acd-crumb a:hover { text-decoration: underline; }
  .acd .acd-crumb .sep { color: #d3d6de; }
  .acd .acd-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 2px; }
  .acd .acd-sub { font-size: 13px; color: var(--muted); }
  .acd .acd-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
  .acd .acd-head-actions { display: flex; gap: 8px; flex-wrap: wrap; }
  .acd .acd-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px; font-weight: 500; }
  .acd .acd-alert i { margin-top: 2px; }
  .acd .acd-alert.success { background: var(--green-soft); color: var(--green); }
  .acd .acd-alert.error { background: var(--red-soft); color: var(--red); }
  .acd .acd-alert.info { background: var(--blue-soft); color: var(--blue); }
  .acd .acd-alert.info code { font-family: 'JetBrains Mono', monospace; font-weight: 700; letter-spacing: 1px; font-size: 14px; color: var(--ink); }
  .acd .acd-sec { border-top: 1px solid var(--divider); padding: 22px 0 6px; }
  .acd .acd-sec:first-of-type { border-top: none; padding-top: 4px; }
  .acd .acd-sec-title { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--ink); text-transform: uppercase; letter-spacing: .4px; margin-bottom: 16px; }
  .acd .acd-sec-title i { color: var(--coral); font-size: 13px; }
  .acd .acd-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px 24px; }
  .acd .acd-field { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .3px; margin-bottom: 3px; }
  .acd .acd-value { font-weight: 600; color: var(--ink); font-size: 13px; overflow-wrap: anywhere; }
  .acd .acd-note { font-size: 11px; color: var(--muted); margin-top: 2px; }
  .acd .acd-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 700; white-space: nowrap; }
  .acd .acd-pill.green { background: transparent; border: 1px solid currentColor; color: var(--green); }
  .acd .acd-pill.red { background: transparent; border: 1px solid currentColor; color: var(--red); }
  .acd .acd-pill.amber { background: transparent; border: 1px solid currentColor; color: #b45309; }
  .acd .acd-pill.blue { background: transparent; border: 1px solid currentColor; color: var(--blue); }
  .acd .acd-pill.gray { background: transparent; border: 1px solid currentColor; color: var(--gray); }
  .acd .acd-btn { display: inline-flex; align-items: center; gap: 6px; border: none; cursor: pointer; border-radius: 11px; padding: 9px 15px; font-size: 12.5px; font-weight: 700; text-decoration: none; transition: transform .15s, filter .15s, background-color .15s; }
  .acd .acd-btn:hover { transform: translateY(-1px); }
  .acd .acd-btn.ghost { background: rgba(255,255,255,0.65); color: var(--ink); box-shadow: 0 2px 10px -8px rgba(26,26,46,0.3); }
  .acd .acd-btn.ghost:hover { background: #fff; color: var(--coral); }
  .acd .acd-btn.coral { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 8px 18px -8px rgba(255,107,107,0.6); }
  .acd .acd-btn.coral:hover { filter: brightness(1.04); }
  .acd .acd-btn.red { background: var(--red-soft); color: var(--red); }
  .acd .acd-btn.red:hover { background: #fecaca; }
  .acd .acd-btn.amber { background: var(--amber-soft); color: #b45309; }
  .acd .acd-btn.green { background: var(--green-soft); color: var(--green); }
  .acd .acd-btn.sm { padding: 6px 11px; font-size: 11.5px; border-radius: 9px; }
  .acd .acd-list { display: flex; flex-direction: column; }
  .acd .acd-row { display: flex; align-items: center; gap: 14px; padding: 14px 4px; border-bottom: 1px solid var(--divider); }
  .acd .acd-row:last-child { border-bottom: none; }
  .acd .acd-row-ic { flex: 0 0 auto; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 15px; background: var(--gray-soft); color: var(--gray); }
  .acd .acd-row-body { flex: 1; min-width: 0; }
  .acd .acd-row-name { font-size: 13.5px; font-weight: 700; color: var(--ink); }
  .acd .acd-row-sub { font-size: 12px; color: var(--muted); margin-top: 2px; display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
  .acd .acd-row-sub .dot { color: #d3d6de; }
  .acd .acd-empty { text-align: center; color: var(--muted); font-size: 13px; padding: 28px 0; }
  .acd .acd-empty i { display: block; font-size: 24px; margin-bottom: 8px; color: #d3d6de; }
  /* doc rows */
  .acd .doc-list { display: flex; flex-direction: column; }
  .acd .doc-row { display: flex; align-items: flex-start; gap: 14px; padding: 14px 4px; border-bottom: 1px solid var(--divider); }
  .acd .doc-row:last-child { border-bottom: none; }
  .acd .doc-icon { flex: 0 0 auto; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 15px; background: var(--gray-soft); color: var(--gray); }
  .acd .doc-info { flex: 1; min-width: 0; }
  .acd .doc-name { font-size: 13px; font-weight: 700; color: var(--ink); }
  .acd .doc-meta { font-size: 11.5px; color: var(--muted); margin-top: 2px; }
  .acd .doc-meta span { margin: 0 4px; color: #d3d6de; }
  .acd .doc-actions { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; flex-shrink: 0; }
  .acd .acct-reject-form { width: 100%; margin-top: 10px; padding: 12px; background: var(--red-soft); border: 1px solid rgba(239,68,68,0.2); border-radius: 10px; display: none; }
  .acd .acct-reject-form.open { display: block; }
  .acd .acct-reject-form p { font-size: 12px; color: var(--red); font-weight: 600; margin-bottom: 8px; }
  .acd .acct-reject-row { display: flex; gap: 8px; flex-wrap: wrap; }
  /* timeline */
  .acd .acd-timeline { position: relative; padding-left: 22px; }
  .acd .acd-timeline::before { content: ''; position: absolute; left: 6px; top: 6px; bottom: 6px; width: 1px; background: var(--divider); }
  .acd .acd-tl-item { position: relative; padding: 0 0 16px; }
  .acd .acd-tl-item:last-child { padding-bottom: 0; }
  .acd .acd-tl-dot { position: absolute; left: -22px; top: 4px; width: 13px; height: 13px; border-radius: 50%; background: #fff; border: 2px solid var(--coral); }
  .acd .acd-tl-item.warn .acd-tl-dot { border-color: var(--amber); }
  .acd .acd-tl-item.danger .acd-tl-dot { border-color: var(--red); }
  .acd .acd-tl-desc { font-size: 13px; color: var(--ink); }
  .acd .acd-tl-meta { font-size: 11px; color: var(--muted); margin-top: 3px; }
  /* modal */
  .acd .acd-modal-overlay { position: fixed; inset: 0; z-index: 90; background: rgba(26,26,46,0.36); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; padding: 16px; }
  .acd .acd-modal-overlay.open { display: flex; }
  .acd .acd-modal { width: 100%; max-width: 420px; background: #fff; border-radius: 18px; padding: 22px; box-shadow: 0 24px 60px -18px rgba(26,26,46,0.4); animation: acdModalPop .2s cubic-bezier(.22,1.2,.36,1); }
  @keyframes acdModalPop { from { opacity: 0; transform: scale(0.97) translateY(4px); } to { opacity: 1; transform: scale(1) translateY(0); } }
  .acd .acd-modal h3 { font-size: 15px; font-weight: 700; color: var(--ink); margin-bottom: 8px; }
  .acd .acd-modal p { font-size: 13px; color: var(--muted); margin-bottom: 16px; line-height: 1.5; }
  .acd .acd-modal-foot { display: flex; justify-content: flex-end; gap: 8px; }
  .acd .acd-input { width: 100%; padding: 9px 12px; border: 1px solid rgba(26,26,46,0.14); border-radius: 10px; font-size: 13px; background: rgba(255,255,255,0.7); color: var(--ink); margin-bottom: 12px; box-sizing: border-box; transition: border-color .18s, box-shadow .18s; }
  .acd .acd-input:focus { outline: none; border-color: var(--coral); background: #fff; box-shadow: 0 0 0 3px rgba(255,107,107,0.12); }
  @media (max-width: 900px) { .acd .acd-grid { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 600px) {
    .acd { padding: 20px 16px 32px; }
    .acd .acd-grid { grid-template-columns: 1fr; }
    .acd .acd-row { flex-wrap: wrap; }
    .acd .doc-row { flex-direction: column; align-items: flex-start; }
    .acd .doc-actions { margin-left: 0; }
  }
</style>

<div class="acd">
  <div class="acd-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <a href="{{ route('admin.accounts.index') }}">Akun Siswa</a>
    <span class="sep">/</span>
    <span>Detail</span>
  </div>

  <div class="acd-head">
    <div>
      <h1 class="acd-title">{{ $applicant->full_name ?? $user->name }}</h1>
      <p class="acd-sub">{{ $user->email }}</p>
    </div>
    <div class="acd-head-actions">
      <button type="button" class="acd-btn ghost sm" onclick="openAcctModal('reset')"><x-hi name="key-01" style="font-size:10px;" /> Reset Password</button>
      <button type="button" class="acd-btn red sm" onclick="openAcctModal('delete')"><x-hi name="delete-02" style="font-size:10px;" /> Hapus Akun</button>
    </div>
  </div>

  @if (session('success'))
    <div class="acd-alert success"><x-hi name="checkmark-circle-02" /><span>{{ session('success') }}</span></div>
  @endif
  @if (session('error'))
    <div class="acd-alert error"><x-hi name="alert-02" /><span>{{ session('error') }}</span></div>
  @endif

  @if ($resetPasswordShown)
    <div class="acd-alert info"><x-hi name="key-01" /><span>Password baru: <code>{{ $resetPasswordShown }}</code></span></div>
  @endif

  {{-- ========== INFORMASI PROFIL ========== --}}
  <div class="acd-sec">
    <h4 class="acd-sec-title"><x-hi name="user" /> Informasi Profil</h4>
    @if (! $applicant)
      <div class="acd-empty"><x-hi name="folder-open" />Data profil belum diisi siswa</div>
    @else
      <div class="acd-grid">
        <div><p class="acd-field">Nama Lengkap</p><p class="acd-value">{{ $applicant->full_name }}</p></div>
        <div><p class="acd-field">NIK</p><p class="acd-value">{{ $applicant->nik }}</p></div>
        <div>
          <p class="acd-field">NISN</p>
          <p class="acd-value">{{ $applicant->nisn }}</p>
          @if ($applicant->nisn_verification_status === 'verified')
            <p class="acd-note" style="color:var(--green);">NISN terverifikasi Kemendikdasmen @if($applicant->nisn_verified_at)· {{ $applicant->nisn_verified_at->format('d M Y H:i') }}@endif</p>
          @elseif ($applicant->nisn_verification_status === 'failed')
            <p class="acd-note" style="color:var(--red);">Verifikasi NISN gagal</p>
          @endif
        </div>
        @if ($applicant->phone)<div><p class="acd-field">Nomor HP</p><p class="acd-value">{{ $applicant->phone }}</p></div>@endif
        @if ($applicant->birth_place || $applicant->birth_date)
          <div><p class="acd-field">Tempat, Tanggal Lahir</p><p class="acd-value">{{ $applicant->birth_place }}{{ $applicant->birth_place && $applicant->birth_date ? ', ' : '' }}{{ $applicant->birth_date?->format('d M Y') }}</p></div>
        @endif
        @if ($applicant->gender)<div><p class="acd-field">Jenis Kelamin</p><p class="acd-value">{{ $genderLabel($applicant->gender) }}</p></div>@endif
        @if ($applicant->religion)<div><p class="acd-field">Agama</p><p class="acd-value">{{ $applicant->religion }}</p></div>@endif
        @if ($applicant->previous_school)<div><p class="acd-field">Asal Sekolah</p><p class="acd-value">{{ $applicant->previous_school }}</p></div>@endif
        @if (count($alamatParts))
          <div style="grid-column:1/-1;"><p class="acd-field">Alamat</p><p class="acd-value">{{ implode(', ', $alamatParts) }}</p></div>
        @endif
        @if ($applicant->father_name || $applicant->father_occupation)
          <div><p class="acd-field">Nama Ayah</p><p class="acd-value">{{ $applicant->father_name }}</p>@if($applicant->father_occupation)<p class="acd-note">Pekerjaan: {{ $applicant->father_occupation }}</p>@endif</div>
        @endif
        @if ($applicant->mother_name || $applicant->mother_occupation)
          <div><p class="acd-field">Nama Ibu</p><p class="acd-value">{{ $applicant->mother_name }}</p>@if($applicant->mother_occupation)<p class="acd-note">Pekerjaan: {{ $applicant->mother_occupation }}</p>@endif</div>
        @endif
        @if ($applicant->parent_name || $applicant->parent_phone)
          <div><p class="acd-field">Orang Tua / Wali</p><p class="acd-value">{{ $applicant->parent_name }}</p>@if($applicant->parent_phone)<p class="acd-note">HP: {{ $applicant->parent_phone }}</p>@endif</div>
        @endif
      </div>
    @endif
  </div>

  {{-- ========== RINGKASAN ========== --}}
  <div class="acd-sec">
    <h4 class="acd-sec-title"><x-hi name="chart-up" /> Ringkasan</h4>
    <div class="acd-grid">
      <div><p class="acd-field">Tanggal Terdaftar</p><p class="acd-value">{{ $user->created_at->format('d M Y H:i') }}</p></div>
      <div><p class="acd-field">Jumlah Pendaftaran</p><p class="acd-value">{{ $registrations->count() }}</p></div>
      <div>
        <p class="acd-field">Verifikasi Email</p>
        @if ($user->email_verified_at)
          <p class="acd-value">{{ $user->email_verified_at->format('d M Y H:i') }}</p>
        @else
          <p class="acd-value" style="color:var(--amber);">Belum terverifikasi</p>
        @endif
      </div>
      <div>
        <p class="acd-field">Terakhir Login</p>
        @if ($lastLogin)
          <p class="acd-value">{{ $lastLogin->created_at->format('d M Y H:i') }}</p>
          <p class="acd-note">IP {{ $lastLogin->ip_address }}</p>
        @else
          <p class="acd-value" style="color:var(--muted);">Belum ada catatan login</p>
        @endif
      </div>
    </div>
  </div>

  {{-- ========== DAFTAR PENDAFTARAN ========== --}}
  <div class="acd-sec">
    <h4 class="acd-sec-title"><x-hi name="file-01" /> Daftar Pendaftaran</h4>
    @if ($registrations->isEmpty())
      <div class="acd-empty"><x-hi name="folder-open" />Belum ada pendaftaran</div>
    @else
      <div class="acd-list">
        @foreach ($registrations as $registration)
          <div class="acd-row">
            <span class="acd-row-ic"><x-hi name="file-01" /></span>
            <div class="acd-row-body">
              <div class="acd-row-name">{{ $registration->registration_number }}</div>
              <div class="acd-row-sub">
                <span>{{ $registration->registrationTrack?->name ?? '-' }}</span>
                <span class="dot">·</span>
                <span>{{ $registration->registrationPeriod?->schoolLevel?->name ?? '-' }}</span>
                <span class="dot">·</span>
                <span>{{ $registration->registrationPeriod?->name ?? '-' }}</span>
                <span class="dot">·</span>
                <span class="acd-pill {{ $regStatusMap[$registration->status] ?? 'gray' }}" style="padding:2px 9px;font-size:11px;">{{ \App\Models\Registration::statusLabel($registration->status) }}</span>
                <span class="dot">·</span>
                <span>{{ $registration->created_at->format('d M Y') }}</span>
              </div>
            </div>
            <div style="display:flex;gap:6px;flex-shrink:0;">
              <a href="{{ route('admin.registrations.show', $registration) }}" class="acd-btn ghost sm"><x-hi name="view" style="font-size:10px;" /> Lihat</a>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- ========== DOKUMEN ========== --}}
  <div class="acd-sec">
    <h4 class="acd-sec-title"><x-hi name="folder-open" /> Dokumen</h4>
    @if ($allDocs->isEmpty())
      <div class="acd-empty"><x-hi name="folder-open" />Belum ada dokumen yang diunggah</div>
    @else
      <div class="doc-list">
        @foreach ($allDocs as $item)
        @php $d = $item['doc']; $r = $item['reg']; $docTypeName = ucfirst(str_replace('_', ' ', $d->document_type)); @endphp
        <div class="doc-row" id="acct-doc-{{ $d->id }}">
          <div class="doc-icon"><x-hi name="file-01" /></div>
          <div class="doc-info">
            <div class="doc-name">{{ $docTypeName }}</div>
            <div class="doc-meta">{{ $d->file_name }}<span>·</span>{{ number_format($d->file_size / 1024, 0) }} KB<span>·</span>Pendaftaran {{ $r->registration_number }}</div>
            @if (!$d->verified_at && $d->verification_notes)
              <div class="doc-meta" style="color:var(--red);">{{ $d->verification_notes }}</div>
            @endif
          </div>
          <div class="doc-actions">
            @if ($d->verified_at)
              <span class="acd-pill green" style="padding:2px 9px;font-size:11px;">Diterima</span>
              <form action="{{ route('admin.documents.unverify', $d) }}" method="POST" onsubmit="return confirm('Batalkan verifikasi dokumen {{ $docTypeName }}?')">
                @csrf @method('PATCH')
                <button type="submit" class="acd-btn ghost sm">Batal Verifikasi</button>
              </form>
            @elseif ($d->verification_notes)
              <span class="acd-pill red" style="padding:2px 9px;font-size:11px;">Ditolak</span>
              <form action="{{ route('admin.documents.verify', $d) }}" method="POST" onsubmit="return confirm('Setujui dokumen {{ $docTypeName }}?')">
                @csrf @method('PATCH')
                <button type="submit" class="acd-btn green sm">Approve</button>
              </form>
            @else
              <span class="acd-pill amber" style="padding:2px 9px;font-size:11px;">Belum dicek</span>
              <form action="{{ route('admin.documents.verify', $d) }}" method="POST" onsubmit="return confirm('Setujui dokumen {{ $docTypeName }}?')">
                @csrf @method('PATCH')
                <button type="submit" class="acd-btn green sm">Approve</button>
              </form>
              <button type="button" class="acd-btn red sm" onclick="toggleAcctReject({{ $d->id }})">Tolak</button>
            @endif
            <button type="button" class="acd-btn ghost sm" onclick="showFileModal('{{ route('registration.documents.download', [$r, $d]) }}', '{{ addslashes($docTypeName) }}')">Pratinjau</button>
          </div>
          @if (!$d->verified_at && !$d->verification_notes)
          <div class="acct-reject-form" id="acct-reject-{{ $d->id }}">
            <p>Tolak dokumen — beri alasan (file akan dihapus permanen dan pendaftaran ditolak):</p>
            <form action="{{ route('admin.documents.reject', $d) }}" method="POST" onsubmit="return confirm('Yakin tolak dokumen {{ $docTypeName }}? File dihapus permanen.')">
              @csrf @method('PATCH')
              <input type="text" name="verification_notes" placeholder="Alasan penolakan (wajib)" required maxlength="500" class="acd-input">
              <div class="acct-reject-row">
                <button type="submit" class="acd-btn red sm">Kirim Penolakan</button>
                <button type="button" class="acd-btn ghost sm" onclick="toggleAcctReject({{ $d->id }})">Batal</button>
              </div>
            </form>
          </div>
          @endif
        </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- ========== RIWAYAT AKTIVITAS ========== --}}
  @php
      $actionIconMap = [
          'auth.register' => ['user-add-01', ''],
          'auth.login' => ['login-01', ''],
          'auth.logout' => ['logout-01', ''],
          'applicant.profile_update' => ['edit-02', 'warn'],
          'registration.create' => ['file-add', ''],
          'registration.verify' => ['checkmark-circle-01', ''],
          'registration.accepted' => ['mortarboard-01', ''],
          'registration.reset' => ['rotate-left-01', 'warn'],
          'registration.withdraw' => ['walking', 'warn'],
          'document.upload' => ['file-upload', ''],
          'document.verify' => ['file-verified', ''],
          'document.unverify' => ['file-remove', 'warn'],
          'document.reject' => ['file-remove', 'danger'],
          'document.delete' => ['delete-02', 'danger'],
          'payment.create_online' => ['money-02', ''],
          'payment.upload_proof' => ['image-01', ''],
          'payment.verify' => ['checkmark-circle-02', ''],
          'payment.reject' => ['cancel-circle', 'danger'],
          'payment.reset' => ['rotate-left-01', 'warn'],
          're_registration.verify' => ['task-done-01', ''],
          'account.reset_password' => ['key-01', 'warn'],
          'account.delete' => ['user-block-01', 'danger'],
      ];
  @endphp
  <div class="acd-sec">
    <h4 class="acd-sec-title"><x-hi name="work-history" /> Riwayat Aktivitas</h4>
    @if ($activities->isEmpty())
      <div class="acd-empty"><x-hi name="folder-open" />Belum ada aktivitas tercatat</div>
    @else
      <div class="acd-timeline">
        @foreach ($activities as $log)
        @php [$tlIcon, $tone] = $actionIconMap[$log->action] ?? ['more-horizontal-circle-01', '']; @endphp
        <div class="acd-tl-item {{ $tone }}">
          <span class="acd-tl-dot"></span>
          <p class="acd-tl-desc">{{ $log->description ?? $log->action }}</p>
          <p class="acd-tl-meta">
            {{ $log->created_at->format('d M Y H:i') }}
            @if ($log->user) · oleh {{ $log->user->name }} @endif
            @if ($log->ip_address) · IP {{ $log->ip_address }} @endif
          </p>
        </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- ========== MODAL KONFIRMASI 2 LANGKAH ========== --}}
  <div class="acd-modal-overlay" id="acctModalOverlay">
    <div class="acd-modal">
      <div id="acctModalStep1">
        <h3 id="acctModalTitle"></h3>
        <p id="acctModalBody"></p>
        <div class="acd-modal-foot">
          <button type="button" class="acd-btn ghost sm" onclick="closeAcctModal()">Batal</button>
          <button type="button" class="acd-btn coral sm" id="acctModalNext"></button>
        </div>
      </div>
      <div id="acctModalStep2" style="display:none;">
        <h3>Konfirmasi Terakhir</h3>
        <p>Ketik <strong>{{ $applicant->full_name ?? $user->name }}</strong> untuk melanjutkan.</p>
        <input type="text" id="acctConfirmInput" class="acd-input" autocomplete="off" placeholder="Ketik nama di sini">
        <div class="acd-modal-foot">
          <button type="button" class="acd-btn ghost sm" onclick="closeAcctModal()">Batal</button>
          <form action="{{ route('admin.accounts.destroy', $user) }}" method="POST" id="acctDeleteForm" style="margin:0;">
            @csrf @method('DELETE')
            <button type="submit" class="acd-btn red sm" id="acctDeleteBtn" disabled style="opacity:.5;">Hapus Permanen</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <form action="{{ route('admin.accounts.reset-password', $user) }}" method="POST" id="acctResetForm" style="display:none;">@csrf</form>
</div>

<script>
(function () {
  var expectedName = @js($applicant->full_name ?? $user->name);
  var overlay = document.getElementById('acctModalOverlay');
  window.openAcctModal = function (kind) {
    var t1 = document.getElementById('acctModalStep1');
    var t2 = document.getElementById('acctModalStep2');
    var next = document.getElementById('acctModalNext');
    if (kind === 'delete') {
      document.getElementById('acctModalTitle').textContent = 'Hapus Akun Siswa?';
      document.getElementById('acctModalBody').textContent = 'Seluruh data akan terhapus permanen: profil, ' + @js($registrations->count()) + ' pendaftaran beserta dokumennya, dan riwayat pembayaran. Tindakan ini tidak dapat dibatalkan.';
      next.textContent = 'Ya, Lanjutkan';
      next.dataset.kind = 'delete';
      t1.style.display = 'block';
      t2.style.display = 'none';
      overlay.classList.add('open');
    } else {
      document.getElementById('acctModalTitle').textContent = 'Reset Password Akun?';
      document.getElementById('acctModalBody').textContent = 'Password baru acak akan dibuat dan dikirim ke email siswa (' + @js($user->email) + '). Password lama tidak berlaku lagi.';
      next.textContent = 'Ya, Reset Password';
      next.dataset.kind = 'reset';
      t1.style.display = 'block';
      t2.style.display = 'none';
      overlay.classList.add('open');
    }
  };
  window.closeAcctModal = function () {
    overlay.classList.remove('open');
    var inp = document.getElementById('acctConfirmInput');
    if (inp) inp.value = '';
    syncDeleteBtn();
  };
  function syncDeleteBtn() {
    var inp = document.getElementById('acctConfirmInput');
    var btn = document.getElementById('acctDeleteBtn');
    if (!inp || !btn) return;
    var match = (inp.value || '').trim() === expectedName;
    btn.disabled = !match;
    btn.style.opacity = match ? '1' : '.5';
  }
  document.getElementById('acctModalNext').addEventListener('click', function () {
    if (this.dataset.kind === 'reset') {
      closeAcctModal();
      document.getElementById('acctResetForm').submit();
      return;
    }
    document.getElementById('acctModalStep1').style.display = 'none';
    document.getElementById('acctModalStep2').style.display = 'block';
    setTimeout(function(){ document.getElementById('acctConfirmInput').focus(); }, 50);
  });
  document.getElementById('acctConfirmInput').addEventListener('input', syncDeleteBtn);
  document.getElementById('acctDeleteForm').addEventListener('submit', function () {
    var btn = document.getElementById('acctDeleteBtn');
    btn.disabled = true;
    btn.textContent = 'Menghapus...';
  });
  overlay.addEventListener('click', function (e) { if (e.target === overlay) closeAcctModal(); });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && overlay.classList.contains('open')) closeAcctModal();
  });
})();
function toggleAcctReject(id) {
  var el = document.getElementById('acct-reject-' + id);
  if (el) el.classList.toggle('open');
}
</script>
@endsection
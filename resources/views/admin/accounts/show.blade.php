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
  /* Spesifik halaman Detail Akun Siswa */
  .acct-head { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin-bottom:20px; flex-wrap:wrap; }
  .acct-head-actions { display:flex; gap:8px; flex-wrap:wrap; }
  .acct-section { border-bottom:1px solid var(--border); padding-bottom:20px; margin-bottom:20px; }
  .acct-section:last-of-type { border-bottom:none; }
  .acct-section-title { font-size:11px; font-weight:600; color:var(--tx3); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px; display:flex; align-items:center; gap:6px; }
  .acct-section-title i { font-size:11px; }
  .acct-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px 24px; }
  .acct-field { font-size:12px; color:var(--tx2); margin-bottom:4px; }
  .acct-value { font-weight:500; color:var(--tx-body); font-size:13px; overflow-wrap:anywhere; }
  .acct-sub { font-size:11px; color:var(--tx4); margin-top:2px; }
  .acct-btn-xs { padding:3px 10px; font-size:11px; border-radius:6px; cursor:pointer; border:none; display:inline-flex; align-items:center; gap:4px; font-weight:500; background:var(--panel-2); color:var(--tx-body); }
  .acct-btn-ok { background:var(--success-bg); color:var(--success-fg); }
  .acct-btn-danger-ghost { background:transparent; border:1px solid var(--badge-rejected-fg); color:var(--badge-rejected-fg); }
  .acct-reject-form { width:100%; margin-top:10px; padding:12px; background:var(--badge-rejected-bg); border:1px solid var(--error-border); border-radius:8px; display:none; }
  .acct-reject-form.open { display:block; }
  .acct-reject-form p { font-size:12px; color:var(--badge-rejected-fg); font-weight:600; margin-bottom:8px; }
  .acct-reject-row { display:flex; gap:8px; flex-wrap:wrap; }
  .acct-timeline { position:relative; padding-left:22px; }
  .acct-timeline::before { content:''; position:absolute; left:6px; top:6px; bottom:6px; width:1px; background:var(--border); }
  .acct-tl-item { position:relative; padding:0 0 16px; }
  .acct-tl-item:last-child { padding-bottom:0; }
  .acct-tl-dot { position:absolute; left:-22px; top:4px; width:13px; height:13px; border-radius:50%; background:var(--panel-2); border:2px solid var(--accent); }
  .acct-tl-item.warn .acct-tl-dot { border-color:var(--warning); }
  .acct-tl-item.danger .acct-tl-dot { border-color:var(--danger); }
  .acct-tl-desc { font-size:13px; color:var(--tx-body); }
  .acct-tl-meta { font-size:11px; color:var(--tx4); margin-top:3px; }
  .acct-modal-overlay { position:fixed; inset:0; z-index:1000; background:var(--overlay); display:none; align-items:center; justify-content:center; padding:16px; }
  .acct-modal-overlay.open { display:flex; }
  .acct-modal { background:var(--panel); border-radius:10px; box-shadow:var(--shadow-lg); max-width:420px; width:100%; padding:24px; }
  .acct-modal h3 { font-size:15px; font-weight:600; color:var(--tx1); margin-bottom:8px; }
  .acct-modal p { font-size:13px; color:var(--tx2); margin-bottom:16px; }
  .acct-modal-foot { display:flex; justify-content:flex-end; gap:8px; }
  .acct-input { width:100%; padding:7px 12px; border:1px solid var(--input-border); border-radius:6px; font-size:13px; background:var(--input-bg); color:var(--tx-body); margin-bottom:12px; box-sizing:border-box; }
  .acct-input:focus { outline:none; border-color:var(--accent); }
  @media (max-width:900px){ .acct-grid { grid-template-columns:repeat(2,1fr); } }
  @media (max-width:600px){
    .acct-grid { grid-template-columns:1fr; }
    .acct-table-wrap { overflow-x:auto; }
    .data-table { min-width:560px; }
    .doc-row.acct-doc-row { flex-direction:column; align-items:flex-start; gap:10px; }
    .doc-row.acct-doc-row .acct-doc-actions { margin-left:0; flex-wrap:wrap; }
  }
</style>

<div class="breadcrumb">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <span class="sep">/</span>
  <a href="{{ route('admin.accounts.index') }}">Akun Siswa</a>
  <span class="sep">/</span>
  <span>Detail</span>
</div>

<div class="acct-head">
  <div>
    <h1 class="page-title" style="margin-bottom:2px;">{{ $applicant->full_name ?? $user->name }}</h1>
    <p style="font-size:13px;color:var(--tx2);">{{ $user->email }}</p>
  </div>
  <div class="acct-head-actions">
    <button type="button" class="btn btn-outline" onclick="openAcctModal('reset')"><i class="fa-solid fa-key" style="font-size:10px;"></i> Reset Password</button>
    <button type="button" class="btn btn-danger" onclick="openAcctModal('delete')"><i class="fa-regular fa-trash-can" style="font-size:10px;"></i> Hapus Akun</button>
  </div>
</div>

@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif

@if ($resetPasswordShown)
<div class="alert alert-info" style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;">
  <span>Password baru:</span>
  <code style="font-family:'JetBrains Mono',monospace;font-size:14px;font-weight:700;letter-spacing:1px;">{{ $resetPasswordShown }}</code>
</div>
@endif

{{-- ========== INFORMASI PROFIL ========== --}}
<div class="acct-section">
  <h4 class="acct-section-title"><i class="fa-regular fa-user"></i> Informasi Profil</h4>
  @if (! $applicant)
    <div class="empty-state">Data profil belum diisi siswa</div>
  @else
    <div class="acct-grid">
      <div><p class="acct-field">Nama Lengkap</p><p class="acct-value">{{ $applicant->full_name }}</p></div>
      <div><p class="acct-field">NIK</p><p class="acct-value">{{ $applicant->nik }}</p></div>
      <div>
        <p class="acct-field">NISN</p>
        <p class="acct-value">{{ $applicant->nisn }}</p>
        @if ($applicant->nisn_verification_status === 'verified')
          <p class="acct-sub" style="color:var(--success-fg);">NISN terverifikasi Kemendikdasmen @if($applicant->nisn_verified_at)· {{ $applicant->nisn_verified_at->format('d M Y H:i') }}@endif</p>
        @elseif ($applicant->nisn_verification_status === 'failed')
          <p class="acct-sub" style="color:var(--error-fg);">Verifikasi NISN gagal</p>
        @endif
      </div>
      @if ($applicant->phone)<div><p class="acct-field">Nomor HP</p><p class="acct-value">{{ $applicant->phone }}</p></div>@endif
      @if ($applicant->birth_place || $applicant->birth_date)
        <div><p class="acct-field">Tempat, Tanggal Lahir</p><p class="acct-value">{{ $applicant->birth_place }}{{ $applicant->birth_place && $applicant->birth_date ? ', ' : '' }}{{ $applicant->birth_date?->format('d M Y') }}</p></div>
      @endif
      @if ($applicant->gender)<div><p class="acct-field">Jenis Kelamin</p><p class="acct-value">{{ $genderLabel($applicant->gender) }}</p></div>@endif
      @if ($applicant->religion)<div><p class="acct-field">Agama</p><p class="acct-value">{{ $applicant->religion }}</p></div>@endif
      @if ($applicant->previous_school)<div><p class="acct-field">Asal Sekolah</p><p class="acct-value">{{ $applicant->previous_school }}</p></div>@endif
      @if (count($alamatParts))
        <div style="grid-column:1/-1;"><p class="acct-field">Alamat</p><p class="acct-value">{{ implode(', ', $alamatParts) }}</p></div>
      @endif
      @if ($applicant->father_name || $applicant->father_occupation)
        <div><p class="acct-field">Nama Ayah</p><p class="acct-value">{{ $applicant->father_name }}</p>@if($applicant->father_occupation)<p class="acct-sub">Pekerjaan: {{ $applicant->father_occupation }}</p>@endif</div>
      @endif
      @if ($applicant->mother_name || $applicant->mother_occupation)
        <div><p class="acct-field">Nama Ibu</p><p class="acct-value">{{ $applicant->mother_name }}</p>@if($applicant->mother_occupation)<p class="acct-sub">Pekerjaan: {{ $applicant->mother_occupation }}</p>@endif</div>
      @endif
      @if ($applicant->parent_name || $applicant->parent_phone)
        <div><p class="acct-field">Orang Tua / Wali</p><p class="acct-value">{{ $applicant->parent_name }}</p>@if($applicant->parent_phone)<p class="acct-sub">HP: {{ $applicant->parent_phone }}</p>@endif</div>
      @endif
    </div>
  @endif
</div>

{{-- ========== RINGKASAN ========== --}}
<div class="acct-section">
  <h4 class="acct-section-title"><i class="fa-solid fa-chart-simple"></i> Ringkasan</h4>
  <div class="acct-grid">
    <div><p class="acct-field">Tanggal Terdaftar</p><p class="acct-value">{{ $user->created_at->format('d M Y H:i') }}</p></div>
    <div><p class="acct-field">Jumlah Pendaftaran</p><p class="acct-value">{{ $registrations->count() }}</p></div>
    <div>
      <p class="acct-field">Verifikasi Email</p>
      @if ($user->email_verified_at)
        <p class="acct-value">{{ $user->email_verified_at->format('d M Y H:i') }}</p>
      @else
        <p class="acct-value" style="color:var(--badge-pending-fg);">Belum terverifikasi</p>
      @endif
    </div>
    <div>
      <p class="acct-field">Terakhir Login</p>
      @if ($lastLogin)
        <p class="acct-value">{{ $lastLogin->created_at->format('d M Y H:i') }}</p>
        <p class="acct-sub">IP {{ $lastLogin->ip_address }}</p>
      @else
        <p class="acct-value" style="color:var(--tx4);">Belum ada catatan login</p>
      @endif
    </div>
  </div>
</div>

{{-- ========== DAFTAR PENDAFTARAN ========== --}}
<div class="acct-section">
  <h4 class="acct-section-title"><i class="fa-regular fa-file-lines"></i> Daftar Pendaftaran</h4>
  @if ($registrations->isEmpty())
    <div class="empty-state">Belum ada pendaftaran</div>
  @else
    <div class="acct-table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>No. Registrasi</th>
            <th>Jalur</th>
            <th>Jenjang</th>
            <th>Tahun Ajaran</th>
            <th>Status</th>
            <th>Tanggal Daftar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($registrations as $registration)
          <tr>
            <td style="font-weight:500;">{{ $registration->registration_number }}</td>
            <td>{{ $registration->registrationTrack?->name ?? '-' }}</td>
            <td>{{ $registration->registrationPeriod?->schoolLevel?->name ?? '-' }}</td>
            <td>{{ $registration->registrationPeriod?->name ?? '-' }}</td>
            <td><span class="status-badge {{ $regStatusMap[$registration->status] ?? 'status-pending' }}">{{ \App\Models\Registration::statusLabel($registration->status) }}</span></td>
            <td style="font-size:12px;color:var(--tx2);">{{ $registration->created_at->format('d M Y') }}</td>
            <td>
              <a href="{{ route('admin.registrations.show', $registration) }}" class="btn btn-outline" style="padding:4px 10px;font-size:11px;">
                <i class="fa-regular fa-eye" style="font-size:10px;"></i> Lihat
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>

{{-- ========== DOKUMEN ========== --}}
<div class="acct-section">
  <h4 class="acct-section-title"><i class="fa-regular fa-folder-open"></i> Dokumen</h4>
  @if ($allDocs->isEmpty())
    <div class="empty-state">Belum ada dokumen yang diunggah</div>
  @else
    <div class="doc-list">
      @foreach ($allDocs as $item)
      @php $d = $item['doc']; $r = $item['reg']; $docTypeName = ucfirst(str_replace('_', ' ', $d->document_type)); @endphp
      <div class="doc-row acct-doc-row" id="acct-doc-{{ $d->id }}">
        <div class="doc-icon"><i class="fa-regular fa-file-lines"></i></div>
        <div class="doc-info">
          <div class="doc-name">{{ $docTypeName }}</div>
          <div class="doc-meta">{{ $d->file_name }}<span>&middot;</span>{{ number_format($d->file_size / 1024, 0) }} KB<span>&middot;</span>Pendaftaran {{ $r->registration_number }}</div>
          @if (!$d->verified_at && $d->verification_notes)
            <div class="doc-meta" style="color:var(--error-fg);">{{ $d->verification_notes }}</div>
          @endif
        </div>
        <div style="display:flex;align-items:center;gap:8px;" class="acct-doc-actions">
          @if ($d->verified_at)
            <span class="acct-btn-xs acct-btn-ok">Diterima</span>
            <form action="{{ route('admin.documents.unverify', $d) }}" method="POST"
                  onsubmit="return confirm('Batalkan verifikasi dokumen {{ $docTypeName }}?')">
              @csrf @method('PATCH')
              <button type="submit" class="acct-btn-xs">Batal Verifikasi</button>
            </form>
          @elseif ($d->verification_notes)
            <span class="acct-btn-xs" style="background:var(--badge-rejected-bg);color:var(--badge-rejected-fg);">Ditolak</span>
            <form action="{{ route('admin.documents.verify', $d) }}" method="POST"
                  onsubmit="return confirm('Setujui dokumen {{ $docTypeName }}?')">
              @csrf @method('PATCH')
              <button type="submit" class="acct-btn-xs acct-btn-ok">Approve</button>
            </form>
          @else
            <span class="acct-btn-xs" style="background:var(--badge-pending-bg);color:var(--badge-pending-fg);">Belum dicek</span>
            <form action="{{ route('admin.documents.verify', $d) }}" method="POST"
                  onsubmit="return confirm('Setujui dokumen {{ $docTypeName }}?')">
              @csrf @method('PATCH')
              <button type="submit" class="acct-btn-xs acct-btn-ok">Approve</button>
            </form>
            <button type="button" class="acct-btn-xs acct-btn-danger-ghost" onclick="toggleAcctReject({{ $d->id }})">Reject</button>
          @endif
          <button type="button" class="acct-btn-xs" onclick="showFileModal('{{ route('registration.documents.download', [$r, $d]) }}', '{{ addslashes($docTypeName) }}')">Pratinjau</button>
        </div>
        @if (!$d->verified_at && !$d->verification_notes)
        <div class="acct-reject-form" id="acct-reject-{{ $d->id }}">
          <p>Tolak dokumen — beri alasan (file akan dihapus permanen dan pendaftaran ditolak):</p>
          <form action="{{ route('admin.documents.reject', $d) }}" method="POST" onsubmit="return confirm('Yakin tolak dokumen {{ $docTypeName }}? File dihapus permanen.')">
            @csrf @method('PATCH')
            <input type="text" name="verification_notes" placeholder="Alasan penolakan (wajib)" required maxlength="500"
                   style="width:100%;padding:7px 12px;border:1px solid var(--input-border);border-radius:6px;font-size:13px;background:var(--input-bg);margin-bottom:8px;box-sizing:border-box;">
            <div class="acct-reject-row">
              <button type="submit" class="btn btn-danger" style="padding:5px 12px;font-size:11px;">Kirim Penolakan</button>
              <button type="button" class="btn btn-outline" style="padding:5px 12px;font-size:11px;" onclick="toggleAcctReject({{ $d->id }})">Batal</button>
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
        'auth.register' => ['fa-user-plus', ''],
        'auth.login' => ['fa-right-to-bracket', ''],
        'auth.logout' => ['fa-right-from-bracket', ''],
        'applicant.profile_update' => ['fa-pen', 'warn'],
        'registration.create' => ['fa-file-circle-plus', ''],
        'registration.verify' => ['fa-check-double', ''],
        'registration.accepted' => ['fa-graduation-cap', ''],
        'registration.reset' => ['fa-rotate-left', 'warn'],
        'registration.withdraw' => ['fa-person-walking-arrow-right', 'warn'],
        'document.upload' => ['fa-file-arrow-up', ''],
        'document.verify' => ['fa-file-circle-check', ''],
        'document.unverify' => ['fa-file-circle-exclamation', 'warn'],
        'document.reject' => ['fa-file-circle-xmark', 'danger'],
        'document.delete' => ['fa-trash', 'danger'],
        'payment.create_online' => ['fa-money-check-dollar', ''],
        'payment.upload_proof' => ['fa-image', ''],
        'payment.verify' => ['fa-circle-check', ''],
        'payment.reject' => ['fa-circle-xmark', 'danger'],
        'payment.reset' => ['fa-rotate-left', 'warn'],
        're_registration.verify' => ['fa-clipboard-check', ''],
        'account.reset_password' => ['fa-key', 'warn'],
        'account.delete' => ['fa-user-slash', 'danger'],
    ];
@endphp
<div class="acct-section">
  <h4 class="acct-section-title"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Aktivitas</h4>
  @if ($activities->isEmpty())
    <div class="empty-state">Belum ada aktivitas tercatat</div>
  @else
    <div class="acct-timeline">
      @foreach ($activities as $log)
      @php [$tlIcon, $tone] = $actionIconMap[$log->action] ?? ['fa-circle-dot', '']; @endphp
      <div class="acct-tl-item {{ $tone }}">
        <span class="acct-tl-dot"></span>
        <p class="acct-tl-desc">{{ $log->description ?? $log->action }}</p>
        <p class="acct-tl-meta">
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
<div class="acct-modal-overlay" id="acctModalOverlay">
  <div class="acct-modal">
    {{-- LANGKAH 1: penjelasan dampak --}}
    <div id="acctModalStep1">
      <h3 id="acctModalTitle"></h3>
      <p id="acctModalBody"></p>
      <div class="acct-modal-foot">
        <button type="button" class="btn btn-outline" onclick="closeAcctModal()">Batal</button>
        <button type="button" class="btn btn-primary" id="acctModalNext"></button>
      </div>
    </div>
    {{-- LANGKAH 2 (hapus saja): verifikasi akhir ketik nama --}}
    <div id="acctModalStep2" style="display:none;">
      <h3>Konfirmasi Terakhir</h3>
      <p>Ketik <strong>{{ $applicant->full_name ?? $user->name }}</strong> untuk melanjutkan.</p>
      <input type="text" id="acctConfirmInput" class="acct-input" autocomplete="off" placeholder="Ketik nama di sini">
      <div class="acct-modal-foot">
        <button type="button" class="btn btn-outline" onclick="closeAcctModal()">Batal</button>
        <form action="{{ route('admin.accounts.destroy', $user) }}" method="POST" id="acctDeleteForm" style="margin:0;">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger" id="acctDeleteBtn" disabled style="opacity:.5;">Hapus Permanen</button>
        </form>
      </div>
    </div>
  </div>
</div>

<form action="{{ route('admin.accounts.reset-password', $user) }}" method="POST" id="acctResetForm" style="display:none;">@csrf</form>

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
      // Reset password: konfirmasi 1 langkah lalu submit
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

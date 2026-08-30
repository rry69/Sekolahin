@php
  $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
  if(!$errors->hasBag('updatePassword')) $errors->put('updatePassword', new \Illuminate\Support\MessageBag);
  if(!$errors->hasBag('userDeletion')) $errors->put('userDeletion', new \Illuminate\Support\MessageBag);
  $hasProfileErr = $errors->has('name') || $errors->has('email');
  $hasPassErr = $errors->updatePassword->any();
  $hasDeleteErr = $errors->userDeletion->isNotEmpty();
  $activeTab = $hasDeleteErr ? 'delete' : ($hasPassErr ? 'password' : 'info');
  $isAdminUser = ($user->role?->name === 'Admin');

  // Variabel untuk kartu ringkasan admin
  $adminInitials = $isAdminUser ? collect(preg_split('/\s+/', trim($user->name)))->filter()->take(2)->map(function($w){ return mb_strtoupper(mb_substr($w,0,1)); })->implode('') : '';
  $adminAvatar = $isAdminUser ? $user->avatar_url : null;
  $adminLastLogin = $isAdminUser && $user->last_login_at ? $user->last_login_at->translatedFormat('d M Y, H:i') : null;
  $adminSession = null;
  if ($isAdminUser) {
      $adminSession = \Illuminate\Support\Facades\DB::table('sessions')
          ->where('user_id', $user->id)
          ->orderByDesc('last_activity')
          ->first();
  }
@endphp

<style>
  .prf {
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
    --divider: rgba(26,26,46,0.10);
    position: relative;
    border-radius: 24px;
    padding: 28px 28px 36px;
    background: #f6f7fb;
    width: 100%;
    max-width: 760px;
    margin: 0 auto;
  }
  .prf .prf-crumb { display:flex; align-items:center; gap:8px; font-size:12.5px; color:var(--muted); margin-bottom:6px; font-weight:500; }
  .prf .prf-crumb a { color:var(--coral); text-decoration:none; }
  .prf .prf-crumb a:hover { text-decoration:underline; }
  .prf .prf-crumb .sep { color:#d3d6de; }
  .prf .prf-title { font-size:26px; font-weight:800; color:var(--ink); letter-spacing:-0.01em; margin-bottom:2px; }
  .prf .prf-meta { font-size:13px; color:var(--muted); margin-bottom:18px; line-height:1.5; }
  .prf .prf-alert { display:flex; align-items:flex-start; gap:10px; padding:12px 16px; border-radius:12px; font-size:13px; margin-bottom:14px; font-weight:500; }
  .prf .prf-alert svg.hi { margin-top:2px; }
  .prf .prf-alert.success { background:var(--green-soft); color:var(--green); }
  .prf .prf-alert.error { background:var(--red-soft); color:var(--red); }
  .prf .prf-alert.info { background:var(--blue-soft); color:var(--blue); }
  .prf .prf-tabs { display:flex; gap:22px; border-bottom:1px solid var(--divider); margin-bottom:0; }
  .prf .prf-tab { appearance:none; background:none; border:none; border-bottom:2.5px solid transparent; margin-bottom:-1px; padding:9px 2px 11px; font-size:13px; font-weight:600; color:var(--muted); cursor:pointer; transition: color .18s, border-color .18s; white-space:nowrap; }
  .prf .prf-tab.is-active { color:var(--coral); border-bottom-color:var(--coral); }
  .prf .prf-panel { display:none; padding-top:22px; }
  .prf .prf-panel.is-active { display:block; }
  .prf .prf-sec-label { font-size:16px; font-weight:700; color:var(--ink); margin-bottom:2px; }
  .prf .prf-sec-desc { font-size:12.5px; color:var(--muted); margin-bottom:16px; line-height:1.5; }
  .prf .prf-field { margin-bottom:14px; }
  .prf .prf-label { display:block; font-size:12px; font-weight:600; color:var(--ink); margin-bottom:6px; }
  .prf .prf-label .req { color:var(--red); margin-left:2px; }
  .prf .prf-input { width:100%; background:transparent; border:none; border-bottom:1px solid rgba(26,26,46,0.18); border-radius:0; padding:9px 4px; font-size:13.5px; color:var(--ink); outline:none; -webkit-tap-highlight-color:transparent; transition:border-color .18s, box-shadow .18s; }
  .prf .prf-input::placeholder { color:#b8bcc9; }
  .prf .prf-input:focus { outline:none; box-shadow:none; border-bottom-color:var(--coral); }
  .prf .prf-input:focus-visible { outline:none; box-shadow:none; border-bottom-color:var(--coral); }
  .prf .prf-input.is-error { border-bottom-color:var(--red); }
  .prf .prf-input.is-error:focus { border-bottom-color:var(--coral); }
  .prf .prf-error { font-size:12px; color:var(--red); margin-top:6px; }
  .prf .prf-hint { font-size:12px; color:var(--muted); margin-top:6px; line-height:1.5; }
  .prf .prf-hint a { color:var(--coral); font-weight:600; text-decoration:none; }
  .prf .prf-hint a:hover { text-decoration:underline; }
  .prf .prf-actions { display:flex; align-items:center; gap:10px; margin-top:18px; }
  .prf .prf-btn { display:inline-flex; align-items:center; gap:7px; border:none; cursor:pointer; border-radius:11px; padding:10px 18px; font-size:13px; font-weight:700; text-decoration:none; transition:transform .15s, filter .15s, background .15s; }
  .prf .prf-btn:hover { transform: translateY(-1px); }
  .prf .prf-btn.coral { background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; box-shadow:0 8px 18px -8px rgba(255,107,107,.6); }
  .prf .prf-btn.coral:hover { filter:brightness(1.04); }
  .prf .prf-btn.ghost { background:rgba(255,255,255,.6); color:var(--ink); }
  .prf .prf-btn.ghost:hover { background:#fff; color:var(--coral); }
  .prf .prf-btn.red { background:var(--red); color:#fff; }
  .prf .prf-btn.red:hover { background:#dc2626; }
  .prf .prf-btn.red:disabled { background:#fca5a5; cursor:not-allowed; transform:none; }
  .prf .prf-saved { font-size:12.5px; color:var(--green); font-weight:600; }
  .prf .prf-danger { margin-top:22px; padding:16px; border:1px dashed rgba(239,68,68,0.22); border-radius:14px; background:rgba(254,226,226,0.32); }
  .prf .prf-danger-title { font-size:13px; font-weight:700; color:var(--red); display:flex; align-items:center; gap:7px; margin-bottom:4px; }
  .prf .prf-danger-desc { font-size:12.5px; color:var(--muted); line-height:1.5; margin-bottom:12px; }
  .prf .prf-modal-backdrop { position:fixed; inset:0; z-index:90; background:rgba(26,26,46,0.36); backdrop-filter:blur(3px); -webkit-backdrop-filter:blur(3px); display:none; align-items:center; justify-content:center; padding:16px; }
  .prf .prf-modal-backdrop.is-open { display:flex; }
  .prf .prf-modal { width:100%; max-width:420px; background:#fff; border-radius:18px; padding:22px; box-shadow:0 24px 60px -18px rgba(26,26,46,.4); animation:prfPop .2s cubic-bezier(.22,1.2,.36,1); }
  @keyframes prfPop { from{opacity:0;transform:scale(.97) translateY(4px);} to{opacity:1;transform:scale(1) translateY(0);} }
  .prf .prf-modal-body { display:flex; align-items:flex-start; gap:13px; margin-bottom:16px; }
  .prf .prf-modal-ic { flex:0 0 auto; display:inline-flex; align-items:center; justify-content:center; font-size:22px; line-height:1; background:none; border-radius:0; box-shadow:none; width:auto; height:auto; color:var(--red); }
  .prf .prf-modal-title { font-size:15px; font-weight:700; color:var(--ink); }
  .prf .prf-modal-msg { font-size:13px; color:var(--muted); margin-top:3px; line-height:1.5; }
  .prf .prf-modal-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:6px; }
  .prf .prf-modal .prf-field { margin-bottom:0; }

  @if($isAdminUser)
  /* ============ Admin Profile Dashboard (Bringova multi-kolom) ============ */
  .prf.prfa { max-width: 1080px; padding: 28px 28px 44px; }
  .prfa .prfa-grid { display:grid; grid-template-columns:1fr; gap:24px; margin-top:22px; }
  .prfa .prfa-side { display:flex; flex-direction:column; gap:16px; min-width:0; }
  .prfa .prfa-main { min-width:0; }

  /* --- Card dasar (soft, sesuai Bringova — tanpa putih memantul) --- */
  .prfa .prfa-card {
    background: rgba(255,255,255,.55);
    border: 1px solid var(--divider);
    border-radius: 18px;
    padding: 18px;
  }
  .prfa .prfa-card-head { display:flex; align-items:center; gap:10px; margin-bottom:14px; }
  .prfa .prfa-card-ic { flex:0 0 auto; display:inline-flex; align-items:center; justify-content:center; font-size:20px; line-height:1; background:none; border-radius:0; box-shadow:none; width:auto; height:auto; color:var(--coral); }
  .prfa .prfa-card-title { font-size:14px; font-weight:700; color:var(--ink); }
  .prfa .prfa-card-sub { font-size:11.5px; color:var(--muted); }

  /* --- Profile summary card --- */
  .prfa-summary { text-align:center; }
  .prfa-avatar-wrap { position:relative; width:88px; height:88px; margin:0 auto 14px; }
  .prfa-avatar {
    width:88px; height:88px; border-radius:50%; overflow:hidden;
    display:flex; align-items:center; justify-content:center;
    background:linear-gradient(135deg,var(--coral),var(--coral-2));
    color:#fff; font-size:32px; font-weight:800; letter-spacing:.02em;
    box-shadow:0 10px 24px -8px rgba(255,107,107,.6);
    border:3px solid #fff;
  }
  .prfa-avatar img { width:100%; height:100%; object-fit:cover; }
  .prfa-avatar.placeholder-avatar { display:flex; }
  .prfa-status-dot {
    position:absolute; right:2px; bottom:6px; width:18px; height:18px; border-radius:50%;
    background:var(--green); border:3px solid #fff; box-shadow:0 2px 8px rgba(16,185,129,.4);
  }
  .prfa-summary-name { font-size:18px; font-weight:800; color:var(--ink); margin-bottom:2px; word-break:break-word; }
  .prfa-summary-mail { font-size:12.5px; color:var(--muted); margin-bottom:12px; word-break:break-word; }
  .prfa-summary-pills { display:flex; justify-content:center; flex-wrap:wrap; gap:8px; margin-bottom:12px; }
  .prfa-login { font-size:12px; color:var(--muted); display:flex; align-items:center; justify-content:center; gap:7px; margin-bottom:16px; }
  .prfa-login svg.hi { color:var(--blue); }

  /* --- Unggah foto --- */
  .prfa-upload { margin-top:4px; }
  .prfa-upload .prf-btn { width:100%; justify-content:center; }
  .prfa-upload .prf-btn:disabled { background:#fca5a5; cursor:wait; transform:none; }
  .prfa-upload-hint { font-size:11.5px; color:var(--muted); text-align:center; margin-top:8px; }

  /* --- Security widget --- */
  .prfa .prfa-sec-row { display:flex; align-items:center; gap:11px; padding:11px 0; border-bottom:1px solid var(--divider); }
  .prfa .prfa-sec-row:last-child { border-bottom:none; padding-bottom:2px; }
  .prfa .prfa-sec-ic { flex:0 0 auto; display:inline-flex; align-items:center; justify-content:center; font-size:18px; line-height:1; background:none; border-radius:0; box-shadow:none; width:auto; height:auto; color:var(--gray); }
  .prfa .prfa-sec-ic.green { color:var(--green); }
  .prfa .prfa-sec-ic.amber { color:#b45309; }
  .prfa .prfa-sec-ic.blue { color:var(--blue); }
  .prfa .prfa-sec-ic.coral { color:var(--coral); }
  .prfa .prfa-sec-body { flex:1; min-width:0; }
  .prfa .prfa-sec-name { font-size:12.5px; font-weight:600; color:var(--ink); }
  .prfa .prfa-sec-val { font-size:11.5px; color:var(--muted); margin-top:1px; line-height:1.4; }

  /* --- 2FA toggle switch --- */
  .prfa-2fa { flex:0 0 auto; position:relative; width:40px; height:23px; border-radius:9999px; background:#d1d5db; border:none; cursor:pointer; transition:background .2s; }
  .prfa-2fa .prfa-2fa-knob { position:absolute; top:2px; left:2px; width:19px; height:19px; border-radius:50%; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,.25); transition:left .2s; }
  .prfa-2fa.on { background:var(--green); }
  .prfa-2fa.on .prfa-2fa-knob { left:19px; }

  /* --- Quick support widget --- */
  .prfa .prfa-support-item { display:flex; align-items:flex-start; gap:10px; padding:9px 0; border-bottom:1px solid var(--divider); }
  .prfa .prfa-support-item:last-child { border-bottom:none; padding-bottom:2px; }
  .prfa .prfa-support-ic { flex:0 0 auto; display:inline-flex; align-items:center; justify-content:center; font-size:17px; line-height:1; background:none; border-radius:0; box-shadow:none; width:auto; height:auto; color:var(--blue); }
  .prfa .prfa-support-q { font-size:12.5px; font-weight:600; color:var(--ink); }
  .prfa .prfa-support-a { font-size:11.5px; color:var(--muted); margin-top:2px; line-height:1.45; }
  .prfa .prfa-support-foot { margin-top:12px; padding-top:12px; border-top:1px solid var(--divider); }
  .prfa .prfa-support-foot a { color:var(--coral); font-weight:600; font-size:12px; text-decoration:none; }

  /* --- Vertical mini-nav --- */
  .prfa .prfa-nav { display:flex; flex-wrap:wrap; gap:6px; background:rgba(255,255,255,.55); border:1px solid var(--divider); border-radius:16px; padding:10px; }
  .prfa .prfa-nav button {
    appearance:none; border:none; background:transparent; cursor:pointer;
    display:flex; align-items:center; gap:9px; width:100%; text-align:left;
    padding:9px 12px; border-radius:11px; font-size:13px; font-weight:600; color:var(--muted);
    transition: background .16s, color .16s, transform .12s; white-space:nowrap;
  }
  .prfa .prfa-nav button svg.hi { width:17px; text-align:center; }
  .prfa .prfa-nav button:hover { background:var(--coral-soft); color:var(--coral); }
  .prfa .prfa-nav button.is-active { background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; box-shadow:0 6px 14px -6px rgba(255,107,107,.6); }
  .prfa .prfa-nav button.danger.is-active { background:var(--red); box-shadow:0 6px 14px -6px rgba(239,68,68,.6); }
  .prfa .prfa-nav button.danger:hover { background:var(--red-soft); color:var(--red); }

  /* --- Panel area --- */
  .prfa .prfa-panel { display:none; }
  .prfa .prfa-panel.is-active { display:block; }
  .prfa .prfa-panel-card { background:rgba(255,255,255,.55); border:1px solid var(--divider); border-radius:18px; padding:20px 22px; }
  .prfa .prfa-panel-card .prf-sec-desc { margin-bottom:18px; }

  /* --- Danger zone (admin inline) --- */
  .prfa .prfa-danger { border:1px solid rgba(239,68,68,.25); border-radius:18px; background:rgba(254,226,226,.28); padding:20px 22px; }
  .prfa .prfa-danger-head { display:flex; align-items:flex-start; gap:13px; margin-bottom:16px; }
  .prfa .prfa-danger-ic { flex:0 0 auto; display:inline-flex; align-items:center; justify-content:center; font-size:22px; line-height:1; background:none; border-radius:0; box-shadow:none; width:auto; height:auto; color:var(--red); }
  .prfa .prfa-danger-title { font-size:15px; font-weight:700; color:var(--red); }
  .prfa .prfa-danger-desc { font-size:12.5px; color:var(--muted); margin-top:3px; line-height:1.5; }
  .prfa .prfa-danger-warn { display:flex; align-items:flex-start; gap:8px; font-size:12px; color:#b45309; background:var(--amber-soft); border-radius:10px; padding:9px 12px; margin-bottom:16px; }
  .prfa .prfa-danger-confirm { margin-bottom:14px; }
  .prfa .prfa-danger-form .prf-btn.red { width:100%; justify-content:center; }

  @media (min-width: 1024px) {
    .prfa .prfa-grid { grid-template-columns: 1fr 2fr; gap: 24px; align-items: start; }
    .prfa .prfa-main { display:flex; flex-direction:row; align-items:flex-start; gap:18px; }
    .prfa .prfa-nav { flex:0 0 auto; width:200px; flex-direction:column; position:sticky; top:24px; }
    .prfa .prfa-panel-wrap { flex:1; min-width:0; }
  }
  @endif

  @media (max-width:620px){ .prf{ padding:22px 18px 28px; } .prf.prfa{ padding:22px 18px 34px; } }
</style>

@if($isAdminUser)

<div class="prf prfa">
  <div class="prf-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span>Pengaturan Profil</span>
  </div>
  <h1 class="prf-title">Pengaturan Profil</h1>
  <p class="prf-meta">Kelola informasi akun, kata sandi, dan pengaturan keamanan.</p>

  @if (session('status') === 'profile-updated')
    <div class="prf-alert success"><x-hi icon="fa-circle-check" /><span>Profil berhasil diperbarui.</span></div>
  @elseif (session('status') === 'password-updated')
    <div class="prf-alert success"><x-hi icon="fa-circle-check" /><span>Kata sandi berhasil diperbarui.</span></div>
  @elseif (session('status') === 'verification-link-sent')
    <div class="prf-alert info"><x-hi icon="fa-envelope" /><span>Link verifikasi baru telah dikirim ke email Anda.</span></div>
  @endif

  <div class="prfa-grid">
    {{-- ============ KOLOM KIRI (1/3) ============ --}}
    <aside class="prfa-side">
      {{-- Profile Summary Card --}}
      <div class="prfa-card prfa-summary">
        <div class="prfa-avatar-wrap">
          <div class="prfa-avatar" id="prfa-avatar-box">
            @if($adminAvatar)
              <img id="prfa-avatar-img" src="{{ $adminAvatar }}" alt="Foto profil">
            @else
              <span id="prfa-avatar-ini" class="placeholder-avatar">{{ $adminInitials ?: mb_strtoupper(mb_substr($user->name,0,1)) }}</span>
            @endif
          </div>
          <span class="prfa-status-dot" title="Online / Aktif"></span>
        </div>
        <div class="prfa-summary-name">{{ $user->name }}</div>
        <div class="prfa-summary-mail">{{ $user->email }}</div>
        <div class="prfa-summary-pills">
          <span class="prfa-sec-name" style="display:none"></span>
          <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 11px;border-radius:20px;font-size:11.5px;font-weight:700;background:var(--coral-soft);color:var(--coral);"><x-hi icon="fa-user-shield" /> Admin</span>
          <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 11px;border-radius:20px;font-size:11.5px;font-weight:700;background:var(--green-soft);color:var(--green);"><span style="width:7px;height:7px;border-radius:50%;background:var(--green);display:inline-block"></span> Online</span>
        </div>
        <div class="prfa-login">
          <x-hi icon="fa-clock-rotate-left" />
          Terakhir masuk: <b>{{ $adminLastLogin ?: 'Belum tercatat' }}</b>
        </div>

        {{-- Unggah foto profil --}}
        <form id="prfa-avatar-form" class="prfa-upload" action="{{ route('profile.avatar') }}" method="post" enctype="multipart/form-data">
          @csrf
          <label for="prfa-avatar-input" style="cursor:pointer">
            <input id="prfa-avatar-input" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" style="display:none">
            <span class="prf-btn coral"><x-hi icon="fa-camera" /> Unggah Foto Profil Baru</span>
          </label>
          <div class="prfa-upload-hint">JPG, PNG, atau WebP · maks 2 MB</div>
        </form>
      </div>

      {{-- Widget Informasi Keamanan --}}
      <div class="prfa-card prfa-security">
        <div class="prfa-card-head">
          <span class="prfa-card-ic"><x-hi icon="fa-shield-halved" /></span>
          <div>
            <div class="prfa-card-title">Keamanan Akun</div>
            <div class="prfa-card-sub">Status keamanan &amp; sesi login</div>
          </div>
        </div>

        <div class="prfa-sec-row">
          <span class="prfa-sec-ic {{ $user->two_factor_enabled ? 'green' : 'gray' }}"><x-hi icon="fa-mobile-screen" /></span>
          <div class="prfa-sec-body">
            <div class="prfa-sec-name">Autentikasi Dua Faktor (2FA)</div>
            <div class="prfa-sec-val">{{ $user->two_factor_enabled ? 'Aktif — lapisan keamanan tambahan menyala' : 'Nonaktif — disarankan untuk diaktifkan' }}</div>
          </div>
          <button type="button" class="prfa-2fa {{ $user->two_factor_enabled ? 'on' : '' }}" data-prfa-2fa data-url="{{ route('profile.two-factor') }}" role="switch" aria-checked="{{ $user->two_factor_enabled ? 'true' : 'false' }}" aria-label="Alihkan autentikasi dua faktor">
            <span class="prfa-2fa-knob"></span>
          </button>
        </div>

        <div class="prfa-sec-row">
          <span class="prfa-sec-ic blue"><x-hi icon="fa-envelope-circle-check" /></span>
          <div class="prfa-sec-body">
            <div class="prfa-sec-name">Email Terverifikasi</div>
            <div class="prfa-sec-val">{{ $user->hasVerifiedEmail() ? 'Email telah diverifikasi' : 'Email belum diverifikasi' }}</div>
          </div>
          @if($user->hasVerifiedEmail())
            <span class="prfa-sec-name" style="color:var(--green);font-size:11px"><x-hi icon="fa-circle-check" /></span>
          @endif
        </div>

        <div class="prfa-sec-row">
          <span class="prfa-sec-ic amber"><x-hi icon="fa-laptop" /></span>
          <div class="prfa-sec-body">
            <div class="prfa-sec-name">Sesi Login Terakhir</div>
            <div class="prfa-sec-val">
              {{ $adminLastLogin ?: 'Belum tercatat' }}
              @if($adminSession && $adminSession->ip_address)
                · {{ $adminSession->ip_address }}
              @endif
            </div>
          </div>
        </div>

        <div class="prfa-sec-row">
          <span class="prfa-sec-ic coral"><x-hi icon="fa-key" /></span>
          <div class="prfa-sec-body">
            <div class="prfa-sec-name">Kata Sandi</div>
            <div class="prfa-sec-val">Gunakan kombinasi huruf, angka, &amp; simbol yang panjang.</div>
          </div>
        </div>
      </div>

      {{-- Widget Panduan / Quick Support --}}
      <div class="prfa-card prfa-support">
        <div class="prfa-card-head">
          <span class="prfa-card-ic"><x-hi icon="fa-circle-question" /></span>
          <div>
            <div class="prfa-card-title">Bantuan &amp; FAQ</div>
            <div class="prfa-card-sub">Jawaban singkat untuk hal yang sering ditanyakan</div>
          </div>
        </div>

        <div class="prfa-support-item">
          <span class="prfa-support-ic"><x-hi icon="fa-camera" /></span>
          <div><div class="prfa-support-q">Bagaimana cara ganti foto profil?</div><div class="prfa-support-a">Klik tombol "Unggah Foto Profil Baru" di kartu ringkasan, pilih file gambar, lalu tunggu proses selesai.</div></div>
        </div>
        <div class="prfa-support-item">
          <span class="prfa-support-ic"><x-hi icon="fa-lock" /></span>
          <div><div class="prfa-support-q">Lupa kata sandi?</div><div class="prfa-support-a">Gunakan menu "Keamanan" untuk mengubah kata sandi — Anda perlu memasukkan kata sandi lama terlebih dahulu.</div></div>
        </div>
        <div class="prfa-support-item">
          <span class="prfa-support-ic"><x-hi icon="fa-shield-halved" /></span>
          <div><div class="prfa-support-q">Apakah 2FA wajib?</div><div class="prfa-support-a">Opsional, tetapi sangat disarankan untuk melindungi akun admin dari akses tidak sah.</div></div>
        </div>
        <div class="prfa-support-foot">
          <a href="#" data-support-contact><x-hi icon="fa-headset" style="margin-right:5px" /> Hubungi Panitia SPMB</a>
        </div>
      </div>
    </aside>

    {{-- ============ KOLOM KANAN (2/3) ============ --}}
    <div class="prfa-main">
      <nav class="prfa-nav" role="tablist" aria-label="Pengaturan profil">
        <button type="button" role="tab" class="{{ $activeTab==='info' ? 'is-active' : '' }}" data-prfa-tab="info" aria-selected="{{ $activeTab==='info' ? 'true' : 'false' }}"><x-hi icon="fa-user" /> Profil</button>
        <button type="button" role="tab" class="{{ $activeTab==='password' ? 'is-active' : '' }}" data-prfa-tab="password" aria-selected="{{ $activeTab==='password' ? 'true' : 'false' }}"><x-hi icon="fa-key" /> Keamanan</button>
        <button type="button" role="tab" class="danger {{ $activeTab==='delete' ? 'is-active' : '' }}" data-prfa-tab="delete" aria-selected="{{ $activeTab==='delete' ? 'true' : 'false' }}"><x-hi icon="fa-trash-can" /> Danger Zone</button>
      </nav>

      <div class="prfa-panel-wrap">
        <div id="prfa-panel-info" class="prfa-panel {{ $activeTab==='info' ? 'is-active' : '' }}">
          <div class="prfa-panel-card">
            @include('profile.partials.update-profile-information-form')
          </div>
        </div>
        <div id="prfa-panel-password" class="prfa-panel {{ $activeTab==='password' ? 'is-active' : '' }}">
          <div class="prfa-panel-card">
            @include('profile.partials.update-password-form')
          </div>
        </div>
        <div id="prfa-panel-delete" class="prfa-panel {{ $activeTab==='delete' ? 'is-active' : '' }}">
          @include('profile.partials.delete-user-form-admin')
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  function esc(s){ return String(s).replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }
  var CSRF = function(){ var m=document.querySelector('meta[name="csrf-token"]'); return m ? m.getAttribute('content') : ''; };

  // --- Tab / panel switching (delegated, aman untuk render full & AJAX) ---
  document.addEventListener('click', function(e){
    var tab = e.target.closest('[data-prfa-tab]');
    if(!tab) return;
    var key = tab.getAttribute('data-prfa-tab');
    document.querySelectorAll('.prfa-nav [data-prfa-tab]').forEach(function(t){
      var on = t === tab;
      t.classList.toggle('is-active', on);
      t.setAttribute('aria-selected', on ? 'true' : 'false');
    });
    ['info','password','delete'].forEach(function(k){
      var p = document.getElementById('prfa-panel-' + k);
      if(p) p.classList.toggle('is-active', k === key);
    });
    try { history.replaceState(null, '', '#' + key); } catch(err){}
  });

  // Aktifkan hash awal (mis. #delete)
  var initHash = (location.hash||'').replace('#','');
  if(initHash && ['info','password','delete'].indexOf(initHash) !== -1){
    var initBtn = document.querySelector('[data-prfa-tab="' + initHash + '"]');
    if(initBtn) initBtn.click();
  }

  // --- Unggah foto profil (AJAX) ---
  document.addEventListener('change', function(e){
    var input = e.target;
    if(!input || input.id !== 'prfa-avatar-input') return;
    var form = document.getElementById('prfa-avatar-form');
    if(!form || !input.files || !input.files.length) return;
    var btn = form.querySelector('.prf-btn');
    if(btn){ btn.disabled = true; btn.innerHTML = hiSvg('fa-spinner', 'class="animate-spin"') + ' Mengunggah...'; }
    var fd = new FormData(form);
    fetch(form.getAttribute('action'), {
      method: 'POST',
      body: fd,
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF() }
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
      if(btn){ btn.disabled = false; btn.innerHTML = hiSvg('fa-camera') + ' Unggah Foto Profil Baru'; }
      input.value = '';
      if(d && d.success){
        var img = document.getElementById('prfa-avatar-img');
        if(img){ img.src = d.avatar_url + '?t=' + Date.now(); img.style.display = 'block'; }
        var ini = document.getElementById('prfa-avatar-ini');
        if(ini) ini.style.display = 'none';
        var sideImg = document.getElementById('sidebar-avatar-img');
        if(sideImg){ sideImg.src = d.avatar_url + '?t=' + Date.now(); sideImg.style.display = 'block'; }
        var sideIni = document.getElementById('sidebar-avatar-ini');
        if(sideIni) sideIni.style.display = 'none';
        if(window.showToast) window.showToast(d.message);
      } else {
        if(window.showToast) window.showToast('Gagal mengunggah foto. Periksa format & ukuran file.');
      }
    })
    .catch(function(){
      if(btn){ btn.disabled = false; btn.innerHTML = hiSvg('fa-camera') + ' Unggah Foto Profil Baru'; }
      input.value = '';
      if(window.showToast) window.showToast('Gagal terhubung ke server');
    });
  });

  // --- Toggle 2FA (AJAX) ---
  document.addEventListener('click', function(e){
    var t = e.target.closest('[data-prfa-2fa]');
    if(!t) return;
    var url = t.getAttribute('data-url');
    var prev = t.classList.contains('on');
    // optimistic
    t.classList.toggle('on', !prev);
    t.setAttribute('aria-checked', prev ? 'false' : 'true');
    fetch(url, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF() }
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
      if(d && d.success){
        var on = !!d.two_factor_enabled;
        t.classList.toggle('on', on);
        t.setAttribute('aria-checked', on ? 'true' : 'false');
        if(window.showToast) window.showToast(d.message);
      } else {
        t.classList.toggle('on', prev);
        t.setAttribute('aria-checked', prev ? 'true' : 'false');
        if(window.showToast) window.showToast('Gagal mengubah pengaturan 2FA');
      }
    })
    .catch(function(){
      t.classList.toggle('on', prev);
      t.setAttribute('aria-checked', prev ? 'true' : 'false');
      if(window.showToast) window.showToast('Gagal terhubung ke server');
    });
  });

  // --- Zona bahaya: aktifkan tombol hanya jika diketik "HAPUS" ---
  document.addEventListener('input', function(e){
    if(!e.target || e.target.id !== 'prfa-delete-confirm') return;
    var btn = document.getElementById('prfa-delete-btn');
    if(btn) btn.disabled = e.target.value.trim().toUpperCase() !== 'HAPUS';
  });
})();
</script>

@else

<div class="prf">
    <div class="prf-crumb">
        @if($isAdminUser)
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span class="sep">/</span>
            <span>Pengaturan Profil</span>
        @else
            <a href="{{ route('registration.index') }}"><x-hi icon="fa-arrow-left" style="margin-right:4px" /> Pendaftaran</a>
            <span class="sep">/</span>
            <span>Profil</span>
        @endif
    </div>
    <h1 class="prf-title">{{ $isAdminUser ? 'Pengaturan Profil' : 'Profil' }}</h1>
    <p class="prf-meta">Kelola informasi akun, kata sandi, dan pengaturan keamanan.</p>

    @if (session('status') === 'profile-updated')
        <div class="prf-alert success"><x-hi icon="fa-circle-check" /><span>Profil berhasil diperbarui.</span></div>
    @elseif (session('status') === 'password-updated')
        <div class="prf-alert success"><x-hi icon="fa-circle-check" /><span>Kata sandi berhasil diperbarui.</span></div>
    @elseif (session('status') === 'verification-link-sent')
        <div class="prf-alert info"><x-hi icon="fa-envelope" /><span>Link verifikasi baru telah dikirim ke email Anda.</span></div>
    @endif

    <div class="prf-tabs" role="tablist">
        <button type="button" role="tab" class="prf-tab {{ $activeTab==='info' ? 'is-active' : '' }}" data-prf-tab="info" aria-selected="{{ $activeTab==='info' ? 'true' : 'false' }}"><x-hi icon="fa-user" style="margin-right:5px" /> Informasi Profil</button>
        <button type="button" role="tab" class="prf-tab {{ $activeTab==='password' ? 'is-active' : '' }}" data-prf-tab="password" aria-selected="{{ $activeTab==='password' ? 'true' : 'false' }}"><x-hi icon="fa-key" style="margin-right:5px" /> Kata Sandi</button>
        <button type="button" role="tab" class="prf-tab {{ $activeTab==='delete' ? 'is-active' : '' }}" data-prf-tab="delete" aria-selected="{{ $activeTab==='delete' ? 'true' : 'false' }}"><x-hi icon="fa-trash-can" style="margin-right:5px" /> Hapus Akun</button>
    </div>

    <div id="prf-panel-info" class="prf-panel {{ $activeTab==='info' ? 'is-active' : '' }}">
        @include('profile.partials.update-profile-information-form')
    </div>
    <div id="prf-panel-password" class="prf-panel {{ $activeTab==='password' ? 'is-active' : '' }}">
        @include('profile.partials.update-password-form')
    </div>
    <div id="prf-panel-delete" class="prf-panel {{ $activeTab==='delete' ? 'is-active' : '' }}">
        @include('profile.partials.delete-user-form')
    </div>
</div>

<script>
(function(){
  var tabs = document.querySelectorAll('[data-prf-tab]');
  var panels = { info: document.getElementById('prf-panel-info'), password: document.getElementById('prf-panel-password'), delete: document.getElementById('prf-panel-delete') };
  function activate(key){
    tabs.forEach(function(t){ var on = t.getAttribute('data-prf-tab')===key; t.classList.toggle('is-active', on); t.setAttribute('aria-selected', on ? 'true' : 'false'); });
    Object.keys(panels).forEach(function(k){ if(panels[k]) panels[k].classList.toggle('is-active', k===key); });
    try { history.replaceState(null,'','#'+key); } catch(e){}
  }
  tabs.forEach(function(t){ t.addEventListener('click', function(){ activate(t.getAttribute('data-prf-tab')); }); });
  var h = (location.hash||'').replace('#','');
  if(h && panels[h]) activate(h);
})();
</script>

@endif

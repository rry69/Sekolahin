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
  .prf .prf-alert i { margin-top:2px; }
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
  .prf .prf-input { width:100%; background:transparent; border:none; border-bottom:1px solid rgba(26,26,46,0.18); border-radius:0; padding:9px 4px; font-size:13.5px; color:var(--ink); outline:none; transition:border-color .18s; }
  .prf .prf-input::placeholder { color:#b8bcc9; }
  .prf .prf-input:focus { border-bottom-color:var(--coral); }
  .prf .prf-input.is-error { border-bottom-color:var(--red); }
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
  .prf .prf-saved { font-size:12.5px; color:var(--green); font-weight:600; }
  .prf .prf-danger { margin-top:22px; padding:16px; border:1px dashed rgba(239,68,68,0.22); border-radius:14px; background:rgba(254,226,226,0.32); }
  .prf .prf-danger-title { font-size:13px; font-weight:700; color:var(--red); display:flex; align-items:center; gap:7px; margin-bottom:4px; }
  .prf .prf-danger-desc { font-size:12.5px; color:var(--muted); line-height:1.5; margin-bottom:12px; }
  .prf .prf-modal-backdrop { position:fixed; inset:0; z-index:90; background:rgba(26,26,46,0.36); backdrop-filter:blur(3px); -webkit-backdrop-filter:blur(3px); display:none; align-items:center; justify-content:center; padding:16px; }
  .prf .prf-modal-backdrop.is-open { display:flex; }
  .prf .prf-modal { width:100%; max-width:420px; background:#fff; border-radius:18px; padding:22px; box-shadow:0 24px 60px -18px rgba(26,26,46,.4); animation:prfPop .2s cubic-bezier(.22,1.2,.36,1); }
  @keyframes prfPop { from{opacity:0;transform:scale(.97) translateY(4px);} to{opacity:1;transform:scale(1) translateY(0);} }
  .prf .prf-modal-body { display:flex; align-items:flex-start; gap:13px; margin-bottom:16px; }
  .prf .prf-modal-ic { flex:0 0 auto; width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:17px; background:var(--red-soft); color:var(--red); }
  .prf .prf-modal-title { font-size:15px; font-weight:700; color:var(--ink); }
  .prf .prf-modal-msg { font-size:13px; color:var(--muted); margin-top:3px; line-height:1.5; }
  .prf .prf-modal-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:6px; }
  .prf .prf-modal .prf-field { margin-bottom:0; }
  @media (max-width:620px){ .prf{ padding:22px 18px 28px; } }
</style>

@php
  $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
  if(!$errors->hasBag('updatePassword')) $errors->put('updatePassword', new \Illuminate\Support\MessageBag);
  if(!$errors->hasBag('userDeletion')) $errors->put('userDeletion', new \Illuminate\Support\MessageBag);
  $hasProfileErr = $errors->has('name') || $errors->has('email');
  $hasPassErr = $errors->updatePassword->any();
  $hasDeleteErr = $errors->userDeletion->isNotEmpty();
  $activeTab = $hasDeleteErr ? 'delete' : ($hasPassErr ? 'password' : 'info');
  $isAdminUser = ($user->role?->name === 'Admin');
@endphp
<div class="prf">
    <div class="prf-crumb">
        @if($isAdminUser)
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span class="sep">/</span>
            <span>Pengaturan Profil</span>
        @else
            <a href="{{ route('registration.index') }}"><i class="fa-solid fa-arrow-left" style="margin-right:4px"></i> Pendaftaran</a>
            <span class="sep">/</span>
            <span>Profil</span>
        @endif
    </div>
    <h1 class="prf-title">{{ $isAdminUser ? 'Pengaturan Profil' : 'Profil' }}</h1>
    <p class="prf-meta">Kelola informasi akun, kata sandi, dan pengaturan keamanan.</p>

    @if (session('status') === 'profile-updated')
        <div class="prf-alert success"><i class="fa-solid fa-circle-check"></i><span>Profil berhasil diperbarui.</span></div>
    @elseif (session('status') === 'password-updated')
        <div class="prf-alert success"><i class="fa-solid fa-circle-check"></i><span>Kata sandi berhasil diperbarui.</span></div>
    @elseif (session('status') === 'verification-link-sent')
        <div class="prf-alert info"><i class="fa-solid fa-envelope"></i><span>Link verifikasi baru telah dikirim ke email Anda.</span></div>
    @endif

    <div class="prf-tabs" role="tablist">
        <button type="button" role="tab" class="prf-tab {{ $activeTab==='info' ? 'is-active' : '' }}" data-prf-tab="info" aria-selected="{{ $activeTab==='info' ? 'true' : 'false' }}"><i class="fa-regular fa-user" style="margin-right:5px"></i> Informasi Profil</button>
        <button type="button" role="tab" class="prf-tab {{ $activeTab==='password' ? 'is-active' : '' }}" data-prf-tab="password" aria-selected="{{ $activeTab==='password' ? 'true' : 'false' }}"><i class="fa-solid fa-key" style="margin-right:5px"></i> Kata Sandi</button>
        <button type="button" role="tab" class="prf-tab {{ $activeTab==='delete' ? 'is-active' : '' }}" data-prf-tab="delete" aria-selected="{{ $activeTab==='delete' ? 'true' : 'false' }}"><i class="fa-solid fa-trash-can" style="margin-right:5px"></i> Hapus Akun</button>
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

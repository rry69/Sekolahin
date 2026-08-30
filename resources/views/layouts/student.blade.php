<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name', 'SPMB') }} - {{ $title ?? 'Siswa' }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="icon" type="image/png" href="{{ asset('images/web_logo.png') }}">
<script src="https://cdn.tailwindcss.com"></script>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
  :root {
    --page: #f6f7fb;
    --panel: #ffffff;
    --panel-2: #f8f9fb;
    --border: #e8e8e8;
    --hairline: #f0f0f0;
    --input-border: #e0e0e0;
    --tx1: #1a1a2e;
    --tx2: #555555;
    --tx3: #8a8f9d;
    --tx4: #aaaaaa;
    --coral: #FF6B6B;
    --coral-2: #FF8E6E;
    --coral-soft: #FFE5E3;
    --ink: #1a1a2e;
    --muted: #8a8f9d;
    --divider: rgba(26, 26, 46, 0.10);
    --green: #10B981;
    --green-soft: #D1FAE5;
    --red: #EF4444;
    --red-soft: #FEE2E2;
    --gray: #6b7280;
    --gray-soft: #F3F4F6;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    background: var(--page);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: #333333;
    font-size: 14px;
    -webkit-font-smoothing: antialiased;
  }
  button { font: inherit; color: inherit; background: none; border: none; cursor: pointer; }
  a { text-decoration: none; color: inherit; }
  :focus-visible { outline: 2px solid rgba(255, 107, 107, 0.55); outline-offset: 2px; border-radius: 8px; }

  /* ===================== TOPBAR (Bringova minimal) ===================== */
  .st-top {
    position: sticky;
    top: 0;
    z-index: 40;
    background: var(--panel);
    border-bottom: 1px solid var(--border);
    backdrop-filter: saturate(140%) blur(8px);
  }
  .st-top-in { max-width: 1080px; margin: 0 auto; padding: 0 24px; display: flex; align-items: center; gap: 20px; height: 64px; }
  .st-brand { display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 15px; color: var(--ink); letter-spacing: -0.01em; }
  .st-brand .st-logo-img { width: 34px; height: 34px; object-fit: contain; flex: 0 0 auto; }
  .st-brand .st-logo-text { height: 22px; width: auto; max-width: 148px; object-fit: contain; display: block; }
  @media (max-width: 520px){ .st-brand .st-logo-text{ display:none; } }
  .st-nav { display: flex; align-items: center; gap: 4px; margin-left: 6px; }
  .st-nav a { display: inline-flex; align-items: center; gap: 7px; padding: 8px 12px; border-radius: 10px; font-size: 13px; font-weight: 600; color: var(--tx3); transition: color .15s, background .15s; }
  .st-nav a svg.hi { font-size: 14px; opacity: .9; }
  .st-nav a:hover { color: var(--coral); background: var(--coral-soft); }
  .st-nav a.active { color: var(--coral); background: var(--coral-soft); }
  .st-right { margin-left: auto; display: flex; align-items: center; gap: 8px; }

  /* notification bell (Bringova style) */
  .st-bell { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 11px; color: var(--tx3); transition: background .15s, color .15s; }
  .st-bell:hover { background: var(--coral-soft); color: var(--coral); }
  .st-bell .st-badge { position: absolute; top: 4px; right: 4px; min-width: 16px; height: 16px; padding: 0 4px; border-radius: 8px; background: var(--red); color: #fff; font-size: 9px; font-weight: 700; display: flex; align-items: center; justify-content: center; }

  /* profile dropdown */
  .st-user { display: inline-flex; align-items: center; gap: 9px; padding: 5px 8px 5px 5px; border-radius: 12px; transition: background .15s; }
  .st-user:hover { background: var(--panel-2); }
  .st-ava { width: 32px; height: 32px; border-radius: 10px; background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; overflow: hidden; flex: 0 0 auto; }
  .st-ava img { width: 100%; height: 100%; object-fit: cover; }
  .st-user .st-uname { font-size: 13px; font-weight: 600; color: var(--ink); line-height: 1.1; }
  .st-user .st-umail { font-size: 11px; color: var(--tx3); line-height: 1.2; }
  .st-caret { color: var(--tx4); font-size: 11px; }

  .st-dropdown { position: absolute; top: calc(100% + 10px); right: 0; width: 240px; background: #fff; border-radius: 14px; box-shadow: 0 20px 50px -16px rgba(26,26,46,.32), 0 0 0 1px rgba(26,26,46,.06); padding: 6px; z-index: 60; animation: stPop .18s cubic-bezier(.22,1.2,.36,1); }
  @keyframes stPop { from { opacity: 0; transform: translateY(-6px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
  .st-dropdown a, .st-dropdown form button { display: flex; align-items: center; gap: 10px; width: 100%; padding: 9px 11px; border-radius: 9px; font-size: 13px; font-weight: 500; color: var(--ink); text-align: left; transition: background .15s; }
  .st-dropdown a svg.hi, .st-dropdown form button svg.hi { width: 18px; text-align: center; color: var(--tx3); }
  .st-dropdown a:hover, .st-dropdown form button:hover { background: var(--gray-soft); color: var(--ink); }
  .st-dropdown a:hover svg.hi, .st-dropdown form button:hover svg.hi { color: var(--coral); }
  .st-dropdown .st-dd-sep { height: 1px; background: var(--divider); margin: 5px 4px; }
  .st-dropdown form { margin: 0; }

  /* content */
  .st-main { max-width: 1080px; margin: 0 auto; padding: 24px 24px 56px; }

  /* ===================== GLOBAL PICKER (Bringova) ===================== */
  .picker-backdrop { position: fixed; inset: 0; z-index: 80; background: rgba(26,26,46,0.32); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); display: none; align-items: flex-start; justify-content: center; padding: 80px 16px 16px; }
  .picker-backdrop.is-open { display: flex; }
  .picker-panel { width: 100%; max-width: 380px; max-height: min(520px, calc(100vh - 120px)); display: flex; flex-direction: column; background: #fff; border-radius: 18px; box-shadow: 0 20px 50px -16px rgba(26,26,46,.35), 0 0 0 1px rgba(26,26,46,.06); overflow: hidden; animation: stPop .22s cubic-bezier(.22,1.2,.36,1); }
  .picker-head { display: flex; align-items: center; gap: 10px; padding: 14px 16px; border-bottom: 1px solid var(--divider); }
  .picker-title { font-size: 14px; font-weight: 700; color: var(--ink); flex: 1; }
  .picker-close { width: 30px; height: 30px; border-radius: 8px; border: none; background: transparent; color: var(--muted); cursor: pointer; font-size: 12px; display: inline-flex; align-items: center; justify-content: center; }
  .picker-close:hover { background: var(--gray-soft); color: var(--ink); }
  .picker-search { position: relative; padding: 10px 14px; border-bottom: 1px solid var(--divider); }
  .picker-search svg.hi { position: absolute; left: 24px; top: 50%; transform: translateY(-50%); color: var(--muted); pointer-events: none; }
  .picker-search input { width: 100%; padding: 9px 12px 9px 32px; border: 1px solid rgba(26,26,46,.14); border-radius: 10px; font-size: 13px; color: var(--ink); background: rgba(255,255,255,.7); }
  .picker-search input:focus { outline: none; border-color: var(--coral); background: #fff; box-shadow: 0 0 0 3px rgba(255,107,107,.12); }
  .picker-list { flex: 1; overflow-y: auto; padding: 6px 8px; }
  .picker-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; font-size: 13px; color: var(--ink); cursor: pointer; user-select: none; }
  .picker-item:hover { background: var(--coral-soft); color: var(--coral); }
  .picker-item.is-selected { background: var(--coral); color: #fff; font-weight: 600; }
  .picker-item .pi-label { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .picker-item svg.hi.pi-check { font-size: 11px; opacity: 0; }
  .picker-item.is-selected svg.hi.pi-check { opacity: 1; }
  .picker-empty { padding: 26px 12px; text-align: center; color: var(--muted); }
  .picker-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 10px 14px; border-top: 1px solid var(--divider); }
  .picker-foot .picker-clear-all { color: var(--muted); font-weight: 600; padding: 6px 8px; border-radius: 8px; }
  .picker-foot .picker-clear-all:hover { color: var(--red); background: var(--red-soft); }
  .picker-foot .picker-done { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; border-radius: 9px; padding: 8px 16px; font-weight: 700; box-shadow: 0 8px 18px -8px rgba(255,107,107,.6); }

  /* r-pick trigger */
  .r-pick { display: inline-flex; align-items: center; gap: 8px; flex-wrap: nowrap; padding: 9px 4px; border: none; border-bottom: 1px solid rgba(26,26,46,0.18); border-radius: 0; font-size: 13px; color: var(--ink); background: transparent; width: 100%; cursor: pointer; text-align: left; min-height: 38px; transition: border-color .18s ease, color .18s ease; }
  .r-pick:hover { border-bottom-color: var(--coral); }
  .r-pick:focus { outline: none; border-bottom-color: var(--coral); }
  .r-pick .pick-label { flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .r-pick .pick-label.is-placeholder { color: var(--muted); }
  .r-pick .pick-caret { color: var(--muted); font-size: 12px; }
  .r-pick .pick-clear { flex: 0 0 auto; display: none; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 6px; background: var(--gray-soft); color: var(--gray); cursor: pointer; font-size: 9px; user-select: none; }
  .r-pick .pick-clear:hover { background: var(--red-soft); color: var(--red); }
  .r-pick.has-value .pick-clear { display: inline-flex; }
  .r-pick.has-value .pick-label.is-placeholder { display: none; }

  /* toast */
  #stToast { position: fixed; top: 24px; left: 50%; transform: translateX(-50%); background: var(--ink); color: #fff; padding: 12px 22px; border-radius: 12px; font-size: 13px; font-weight: 600; z-index: 999; box-shadow: 0 20px 40px rgba(0,0,0,.2); display: none; }

  .pv-wm{position:fixed;inset:0;z-index:9998;pointer-events:none;overflow:hidden}
  .pv-wm span{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) rotate(-18deg);font-size:clamp(18px,4.5vw,42px);font-weight:800;color:#FF6B6B;opacity:.13;white-space:nowrap;letter-spacing:.08em;text-transform:uppercase;border:3px solid currentColor;padding:10px 22px;border-radius:14px;background:rgba(255,255,255,.55);backdrop-filter:blur(1px)}
  /* responsive */
  @media (max-width: 720px) {
    .st-top-in { padding: 0 14px; }
    .st-nav { display: none; }
    .st-main { padding: 16px 14px 40px; }
  }
</style>
</head>
<body>
@php
    $user = auth()->user();
    $studentName = $user?->name ?? '';
    $initial = $user ? mb_strtoupper(mb_substr($user->name, 0, 2)) : '';
@endphp

<header class="st-top">
  <div class="st-top-in">
    <a href="{{ route('dashboard') }}" class="st-brand" aria-label="Sekolahin beranda">
      <img src="{{ asset('images/web_logo.png') }}" alt="Sekolahin" class="st-logo-img" width="34" height="34" loading="eager" decoding="async">
      <img src="{{ asset('images/logo_text.png') }}" alt="Sekolahin" class="st-logo-text" height="22" loading="eager" decoding="async">
    </a>
    <nav class="st-nav" aria-label="Navigasi siswa">
      <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><x-hi icon="fa-house" /> Beranda</a>
      <a href="{{ route('registration.index') }}" class="{{ request()->routeIs('registration.*') ? 'active' : '' }}"><x-hi icon="fa-folder-open" /> Pendaftaran</a>
      <a href="{{ route('applicant.profile') }}" class="{{ request()->routeIs('applicant.profile*') ? 'active' : '' }}"><x-hi icon="fa-id-card" /> Biodata</a>
    </nav>
    <div class="st-right">
      <div class="relative flex items-center">
        <x-notification-panel />
      </div>

      <div class="relative">
        <button type="button" class="st-user" id="stUserBtn" aria-haspopup="true" aria-expanded="false">
          <span class="st-ava">
            @if($user && $user->avatar_url)
              <img src="{{ $user->avatar_url }}" alt="">
            @else
              {{ $initial }}
            @endif
          </span>
          <span class="hidden sm:block text-left">
            <span class="block st-uname">{{ Str::limit($studentName, 18) }}</span>
          </span>
          <x-hi icon="fa-chevron-down" class="st-caret" />
        </button>
        <div class="st-dropdown" id="stUserDropdown" style="display:none;" role="menu">
          <a href="{{ route('applicant.profile') }}" role="menuitem"><x-hi icon="fa-id-card" /> Biodata Siswa</a>
          <a href="{{ route('profile.edit') }}" role="menuitem"><x-hi icon="fa-user-gear" /> Pengaturan Profil</a>
          <div class="st-dd-sep"></div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" role="menuitem"><x-hi icon="fa-arrow-right-from-bracket" /> Keluar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</header>

@if(($_pv['blur'] ?? false))<div class="pv-wm" aria-hidden="true"><span>Belum Berlisensi — Hubungi Admin</span></div>@endif
<main class="st-main">
  {{ $slot }}
</main>

{{-- Global Picker Modal (Bringova) --}}
<div id="pickerBackdrop" class="picker-backdrop" aria-hidden="true">
  <div class="picker-panel" role="dialog" aria-modal="true" aria-labelledby="pickerTitle">
    <div class="picker-head">
      <div class="picker-title" id="pickerTitle">Pilih item</div>
      <button type="button" class="picker-close" onclick="closePicker()" aria-label="Tutup"><x-hi icon="fa-xmark" /></button>
    </div>
    <div class="picker-search">
      <x-hi icon="fa-magnifying-glass" />
      <input id="pickerSearch" type="search" placeholder="Cari…" autocomplete="off">
    </div>
    <div class="picker-list" id="pickerList" role="listbox"></div>
    <div class="picker-foot">
      <button type="button" class="picker-clear-all" onclick="clearCurrentPicker()"><x-hi icon="fa-eraser" /> Bersihkan</button>
      <button type="button" class="picker-done" onclick="closePicker()">Selesai</button>
    </div>
  </div>
</div>

{{-- Toast --}}
<div id="stToast"></div>

@stack('scripts')

<script>
// === Ikon HugeIcons (JS) — konsisten dengan admin ===
window.__HI = @json(\App\Support\Hi::all());
window.__HI_MAP = @json(config('hugeicons'));
function hiSvg(name, attr) {
  var key = name || '';
  if (!(key in (window.__HI || {}))) {
    key = (window.__HI_MAP || {})[key] || '';
  }
  var body = (window.__HI || {})[key] || '';
  if (!body) return '';
  var a = attr ? ' ' + attr : '';
  return '<svg class="hi" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"' + a + '>' + body + '</svg>';
}
function hiHtml(name, attr) { return hiSvg(name, attr); }

(function () {
  // ================= PICKER (Bringova) =================
  var currentKey = null, currentTrigger = null, currentInput = null, currentValue = null;
  function $all(sel, root){ return Array.prototype.slice.call((root||document).querySelectorAll(sel)); }
  function getList(){ return document.getElementById('pickerList'); }
  function getSearch(){ return document.getElementById('pickerSearch'); }
  function getTitle(){ return document.getElementById('pickerTitle'); }
  function escapeHtml(s){ return String(s).replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }
  function findByValue(arr, val){ if(val===null||val===undefined) return null; var sv=String(val); for(var i=0;i<arr.length;i++){ if(String(arr[i].v)===sv) return arr[i]; } return null; }
  function syncTriggerLabel(trigger){
    if(!trigger) return;
    var key=trigger.getAttribute('data-picker');
    var input=document.querySelector('[data-picker-input="'+key+'"]');
    var data=(window.__pickerData||{})[key]||[];
    var labelEl=trigger.querySelector('.pick-label');
    var v=input?input.value:'';
    var found=findByValue(data, v);
    if(found && String(found.v)!==''){ labelEl.textContent=found.l; labelEl.classList.remove('is-placeholder'); trigger.classList.add('has-value'); }
    else { labelEl.textContent='Pilih '+((window.__pickerLabels||{})[key]||'item').toLowerCase().replace(/^pilih\s/,'')+'…'; labelEl.classList.add('is-placeholder'); trigger.classList.remove('has-value'); }
  }
  function renderList(filter){
    var data=(window.__pickerData||{})[currentKey]||[];
    var list=getList(); if(!list) return;
    list.innerHTML='';
    var f=(filter||'').toLowerCase().trim();
    var rows=data.filter(function(it){ if(!f) return true; return String(it.l).toLowerCase().indexOf(f)!==-1; });
    if(rows.length===0){ var e=document.createElement('div'); e.className='picker-empty'; e.innerHTML=hiSvg('fa-folder-open')+' Tidak ada item yang cocok'; list.appendChild(e); return; }
    rows.forEach(function(it){
      var d=document.createElement('div');
      d.className='picker-item'+(String(it.v)===String(currentValue)?' is-selected':'');
      d.setAttribute('role','option'); d.setAttribute('data-value',it.v);
      d.innerHTML='<span class="pi-label">'+escapeHtml(it.l)+'</span>'+hiSvg('fa-check','class="pi-check"');
      d.addEventListener('click', function(){ selectValue(it.v, it.l); });
      list.appendChild(d);
    });
  }
  function selectValue(v,l){
    currentValue=v;
    if(currentInput){ currentInput.value=v; try{ currentInput.dispatchEvent(new Event('change',{bubbles:true})); }catch(e){} }
    if(currentTrigger){
      currentTrigger.querySelector('.pick-label').textContent=l;
      currentTrigger.querySelector('.pick-label').classList.remove('is-placeholder');
      if(String(v)!=='') currentTrigger.classList.add('has-value'); else currentTrigger.classList.remove('has-value');
    }
    $all('.picker-item', getList()).forEach(function(el){ el.classList.toggle('is-selected', el.getAttribute('data-value')===String(v)); });
  }
  function openPicker(key,trigger){
    currentKey=key; currentTrigger=trigger; currentInput=document.querySelector('[data-picker-input="'+key+'"]');
    currentValue=currentInput?currentInput.value:'';
    var data=(window.__pickerData||{})[key]||[];
    if(getTitle()) getTitle().textContent=(window.__pickerLabels||{})[key]||'Pilih item';
    if(getSearch()) getSearch().value='';
    renderList('');
    var bd=document.getElementById('pickerBackdrop');
    if(bd){ bd.classList.add('is-open'); bd.setAttribute('aria-hidden','false'); }
    $all('.r-pick', document).forEach(function(t){ t.setAttribute('aria-expanded', t===trigger?'true':'false'); });
    setTimeout(function(){ if(getSearch()) getSearch().focus(); }, 30);
  }
  function closePicker(){
    var bd=document.getElementById('pickerBackdrop');
    if(bd){ bd.classList.remove('is-open'); bd.setAttribute('aria-hidden','true'); }
    $all('.r-pick', document).forEach(function(t){ t.setAttribute('aria-expanded','false'); });
    currentKey=null; currentTrigger=null; currentInput=null; currentValue=null;
  }
  function clearCurrent(){
    if(currentInput){ currentInput.value=''; try{ currentInput.dispatchEvent(new Event('change',{bubbles:true})); }catch(e){} }
    if(currentTrigger){ currentTrigger.classList.remove('has-value'); syncTriggerLabel(currentTrigger); }
    currentValue=''; renderList(getSearch()?getSearch().value:'');
  }
  function clearPicker(key){
    var input=document.querySelector('[data-picker-input="'+key+'"]');
    var trigger=document.querySelector('.r-pick[data-picker="'+key+'"]');
    if(input){ input.value=''; try{ input.dispatchEvent(new Event('change',{bubbles:true})); }catch(e){} }
    if(trigger) syncTriggerLabel(trigger);
  }
  window.openPicker=openPicker;
  window.closePicker=closePicker;
  window.clearPicker=clearPicker;
  window.clearCurrentPicker=clearCurrent;
  window.pickerInitAll=function(){ initPicker(); };
  function initPicker(){
    var dataEl=document.getElementById('reg-data');
    if(dataEl){ try{ window.__pickerData=JSON.parse(dataEl.getAttribute('data-picker')||'{}'); window.__pickerLabels=JSON.parse(dataEl.getAttribute('data-picker-labels')||'{}'); }catch(e){} }
    $all('.r-pick[data-picker]').forEach(function(trigger){
      if(trigger.__pickerBound) return;
      trigger.__pickerBound=true;
      var key=trigger.getAttribute('data-picker');
      syncTriggerLabel(trigger);
      trigger.addEventListener('click', function(e){
        var clearEl=e.target.closest('.pick-clear');
        if(clearEl){ e.preventDefault(); e.stopPropagation(); clearPicker(key); return; }
        openPicker(key, trigger);
      });
    });
    var search=getSearch();
    if(search && !search.__pickerBound){ search.__pickerBound=true; search.addEventListener('input', function(){ renderList(search.value); }); }
    var bd=document.getElementById('pickerBackdrop');
    if(bd && !bd.__pickerBound){ bd.__pickerBound=true; bd.addEventListener('click', function(e){ if(e.target===bd) closePicker(); }); }
    if(!window.__pickerKeyBound){ window.__pickerKeyBound=true; document.addEventListener('keydown', function(e){ if(e.key==='Escape' && document.getElementById('pickerBackdrop') && document.getElementById('pickerBackdrop').classList.contains('is-open')) closePicker(); }); }
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded', initPicker); else initPicker();

  // ================= USER DROPDOWN =================
  var userBtn=document.getElementById('stUserBtn');
  var userDD=document.getElementById('stUserDropdown');
  function toggleUserDD(force){
    var show = force!==undefined ? force : userDD.style.display==='none';
    userDD.style.display = show?'block':'none';
    userBtn.setAttribute('aria-expanded', show?'true':'false');
  }
  if(userBtn && userDD){
    userBtn.addEventListener('click', function(e){ e.stopPropagation(); toggleUserDD(); });
    document.addEventListener('click', function(e){ if(!userDD.contains(e.target)) toggleUserDD(false); });
  }
  // ================= TOAST =================
  window.showToast=function(msg){
    var t=document.getElementById('stToast');
    if(!t) return;
    t.textContent=msg; t.style.display='block';
    setTimeout(function(){ t.style.display='none'; }, 2500);
  };
})();
</script>
</body>
</html>

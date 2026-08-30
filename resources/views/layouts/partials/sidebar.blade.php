<button type="button" class="sb-hamburger" id="hamburger" aria-label="Buka menu navigasi" aria-controls="sidebar" aria-expanded="false">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
    <line x1="4" y1="6" x2="20" y2="6"></line>
    <line x1="4" y1="12" x2="20" y2="12"></line>
    <line x1="4" y1="18" x2="20" y2="18"></line>
  </svg>
</button>

<div class="backdrop" id="backdrop" aria-hidden="true"></div>

<aside class="sidebar expanded" id="sidebar" aria-label="Navigasi utama">
  <a href="{{ route('dashboard') }}" class="sb-brand" aria-label="Sekolahin">
    <img src="{{ asset('images/web_logo.png') }}" alt="Sekolahin" class="sb-brand-img" width="32" height="32" loading="eager" decoding="async">
    <img src="{{ asset('images/logo_text.png') }}" alt="Sekolahin" class="sb-brand-text" height="20" loading="eager" decoding="async">
  </a>

  <header class="sb-head">
    <span class="avatar" aria-hidden="true">
      <img id="sidebar-avatar-img" src="{{ Auth::user()->avatar_url }}" alt="" style="{{ Auth::user()->avatar_url ? 'width:100%;height:100%;object-fit:cover;border-radius:12px;display:block' : 'display:none' }}">
      <span id="sidebar-avatar-ini" style="{{ Auth::user()->avatar_url ? 'display:none' : '' }}">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}</span>
    </span>
    <div class="who">
      <span class="flabel name">{{ Auth::user()->name }}</span>
      <span class="flabel mail">{{ Auth::user()->email }}</span>
    </div>
    <button type="button" class="sb-close" id="closeBtn" aria-label="Tutup menu navigasi">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>
  </header>

  <nav class="sb-nav" id="nav">
    @php $menu = config('admin-menu'); @endphp

    @foreach ($menu['top'] as $item)
      <a href="{{ route($item['route']) }}"
         class="mi {{ request()->routeIs($item['routeIs']) ? 'active' : '' }}"
         data-menu-item data-action="select" data-label="{{ $item['label'] }}" data-tip="{{ $item['label'] }}">
        <span class="ic">{!! $item['svg'] ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">'.$item['svg'].'</svg>' : '<i class="'.$item['icon'].'"></i>' !!}</span>
        <span class="flabel">{{ $item['label'] }}</span>
      </a>
    @endforeach

    @foreach ($menu['groups'] as $groupLabel => $group)
      @php
        $groupActive = false;
        foreach ($group['items'] as $item) {
          if (request()->routeIs($item['routeIs'])) { $groupActive = true; break; }
        }
      @endphp
      <div class="section {{ $groupActive ? 'open' : '' }}">
        <button type="button" class="mi section-btn" data-action="toggle" aria-expanded="{{ $groupActive ? 'true' : 'false' }}" aria-controls="sub-{{ \Illuminate\Support\Str::slug($groupLabel) }}" data-tip="{{ $groupLabel }}">
          <span class="ic">{!! !empty($group['svg']) ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">'.$group['svg'].'</svg>' : '<i class="'.$group['icon'].'"></i>' !!}</span>
          <span class="flabel">{{ $groupLabel }}</span>
          <span class="chev">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
          </span>
        </button>
        <div class="submenu" id="sub-{{ \Illuminate\Support\Str::slug($groupLabel) }}">
          @foreach ($group['items'] as $item)
            <a href="{{ route($item['route']) }}"
               class="si {{ request()->routeIs($item['routeIs']) ? 'active' : '' }}"
               data-menu-item data-action="select" data-label="{{ $item['label'] }}" data-tip="{{ $item['label'] }}">
              <span class="dot"></span><span class="flabel">{{ $item['label'] }}</span>
            </a>
          @endforeach
        </div>
      </div>
    @endforeach
  </nav>

  <footer class="sb-foot">
    <a href="{{ route('profile.edit') }}" class="mi {{ request()->routeIs('profile.*') ? 'active' : '' }}" data-menu-item data-action="select" data-label="Pengaturan Profil" data-tip="Pengaturan Profil">
      <span class="ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
        </svg>
      </span>
      <span class="flabel">Pengaturan Profil</span>
    </a>

    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="mi danger" data-action="select" data-label="Keluar" data-tip="Keluar">
        <span class="ic">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
        </span>
        <span class="flabel">Keluar</span>
      </button>
    </form>
  </footer>
</aside>

<div id="tooltip" role="tooltip"></div>

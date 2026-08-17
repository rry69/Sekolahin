<button type="button" class="sb-hamburger" id="hamburger" aria-label="Buka menu navigasi" aria-controls="sidebar" aria-expanded="false">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
    <line x1="4" y1="6" x2="20" y2="6"></line>
    <line x1="4" y1="12" x2="20" y2="12"></line>
    <line x1="4" y1="18" x2="20" y2="18"></line>
  </svg>
</button>

<div class="backdrop" id="backdrop" aria-hidden="true"></div>

<aside class="sidebar" id="sidebar" aria-label="Navigasi utama">

  <button type="button" class="sb-toggle" aria-label="Perluas atau ciutkan sidebar">
    <span class="ico-expand">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M9 3v18"></path><path d="m13 9 3 3-3 3"></path>
      </svg>
    </span>
    <span class="ico-collapse">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M9 3v18"></path><path d="m11 9-3 3 3 3"></path>
      </svg>
    </span>
  </button>

  <header class="sb-head">
    <span class="avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}</span>
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
    <button type="button" class="mi theme-toggle" data-action="theme" data-label="Mode Terang/Gelap" data-tip="Mode Terang/Gelap">
      <span class="ic">
        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
        </svg>
        <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
        </svg>
      </span>
      <span class="flabel theme-label">{{ app()->getLocale() === 'id' ? 'Mode Terang/Gelap' : 'Light/Dark Mode' }}</span>
    </button>

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

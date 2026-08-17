<aside class="sidebar">
  <div class="sidebar-header">
    <div class="avatar"><img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4f6ef7&color=fff&size=112" alt="{{ Auth::user()->name }}"></div>
    <h2>{{ Auth::user()->name }}</h2>
    <div class="subtitle">{{ Auth::user()->email }}</div>
  </div>
  <nav class="sidebar-nav">
    @php $menu = config('admin-menu'); @endphp

    @foreach ($menu['top'] as $item)
      <a href="{{ route($item['route']) }}" class="nav-item {{ request()->routeIs($item['routeIs']) ? 'active' : '' }}" data-menu-item>
        <i class="{{ $item['icon'] }}"></i> {{ $item['label'] }}
      </a>
    @endforeach

    @foreach ($menu['groups'] as $groupLabel => $group)
      @php
        $groupActive = false;
        foreach ($group['items'] as $item) {
          if (request()->routeIs($item['routeIs'])) { $groupActive = true; break; }
        }
      @endphp
      <div class="nav-group {{ $groupActive ? '' : 'collapsed' }}">
        <button type="button" class="nav-group-header" data-group-toggle>
          @if (!empty($group['icon']))
            <i class="{{ $group['icon'] }}"></i>
          @endif
          <span>{{ $groupLabel }}</span>
          <i class="fa-solid fa-chevron-down nav-group-chevron"></i>
        </button>
        <div class="nav-group-body">
          @foreach ($group['items'] as $item)
            <a href="{{ route($item['route']) }}" class="nav-item nav-item-child {{ request()->routeIs($item['routeIs']) ? 'active' : '' }}" data-menu-item>
              <i class="{{ $item['icon'] }}"></i> {{ $item['label'] }}
            </a>
          @endforeach
        </div>
      </div>
    @endforeach
  </nav>
  <div class="sidebar-bottom">
    <a href="{{ route('profile.edit') }}" class="nav-item"><i class="fa-solid fa-user-gear"></i> Pengaturan Profil</a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="nav-item" style="width:100%;background:none;border:none;text-align:left;font-family:inherit;"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button>
    </form>
  </div>
</aside>

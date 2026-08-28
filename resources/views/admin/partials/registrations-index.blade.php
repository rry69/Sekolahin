<style>
  /* ===================== REGISTRATIONS INDEX — Bringova (no cards, scoped) ===================== */
  .reg {
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

  /* ---------- header ---------- */
  .reg .r-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
  .reg .r-crumb a { color: var(--coral); text-decoration: none; }
  .reg .r-crumb a:hover { text-decoration: underline; }
  .reg .r-crumb .sep { color: #d3d6de; }
  .reg .r-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 2px; }
  .reg .r-meta { font-size: 13px; color: var(--muted); margin-bottom: 22px; }
  .reg .r-meta b { color: var(--ink); font-weight: 600; }

  /* ---------- alerts (flash) ---------- */
  .reg .r-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px; font-weight: 500; }
  .reg .r-alert i { margin-top: 2px; }
  .reg .r-alert.success { background: var(--green-soft); color: var(--green); }
  .reg .r-alert.error   { background: var(--red-soft);   color: var(--red); }
  .reg .r-alert.info    { background: var(--blue-soft);  color: var(--blue); }

  /* ---------- toolbar: search + filter ---------- */
  .reg .r-toolbar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 16px; }
  .reg .r-search { position: relative; flex: 1; min-width: 200px; }
  .reg .r-search i {
    position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
    color: var(--muted); font-size: 13px; pointer-events: none;
  }
  .reg .r-search input {
    width: 100%; padding: 11px 14px 11px 38px; border: 1px solid rgba(26,26,46,0.14);
    border-radius: 12px; font-size: 13.5px; color: var(--ink); background: rgba(255,255,255,0.55);
    transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
  }
  .reg .r-search input::placeholder { color: var(--muted); }
  .reg .r-search input:focus { outline: none; border-color: var(--coral); box-shadow: 0 0 0 4px rgba(255,107,107,0.14); background: #fff; }
  .reg .r-fbtn, .reg .r-gobtn {
    display: inline-flex; align-items: center; gap: 7px; border: none; cursor: pointer;
    border-radius: 12px; padding: 11px 18px; font-size: 13px; font-weight: 700;
    transition: transform .15s ease, filter .15s ease;
  }
  .reg .r-fbtn { background: rgba(255,255,255,0.7); color: var(--ink); box-shadow: 0 4px 14px -10px rgba(26,26,46,0.3); }
  .reg .r-fbtn:hover { background: #fff; color: var(--coral); }
  .reg .r-gobtn { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 8px 18px -8px rgba(255,107,107,0.6); }
  .reg .r-gobtn:hover { filter: brightness(1.03); transform: translateY(-1px); }

  /* ---------- filter panel ---------- */
  .reg .r-filters {
    display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;
    padding: 18px; margin-bottom: 20px; border: 1px dashed rgba(26,26,46,0.14);
    border-radius: 14px; background: rgba(255,255,255,0.30);
  }
  .reg .r-field { display: flex; flex-direction: column; gap: 5px; }
  .reg .r-field label { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .3px; }
  .reg .r-field select, .reg .r-field input {
    padding: 9px 12px; border: 1px solid rgba(26,26,46,0.14); border-radius: 10px;
    font-size: 13px; color: var(--ink); background: #fff; min-width: 150px;
    transition: border-color .18s ease, box-shadow .18s ease;
  }
  .reg .r-field select:focus, .reg .r-field input:focus { outline: none; border-color: var(--coral); box-shadow: 0 0 0 3px rgba(255,107,107,0.12); }

  /* ---------- tabs (underline, no box) ---------- */
  .reg .r-tabs { display: flex; gap: 18px; border-bottom: 1px solid var(--divider); margin-bottom: 22px; flex-wrap: wrap; }
  .reg .r-tabs a.doc-tab,
  .reg .r-tabs a.r-tab {
    all: unset;
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 2px 11px; font-size: 13px; font-weight: 600; color: var(--muted);
    text-decoration: none; border-bottom: 2.5px solid transparent; margin-bottom: -1px;
    cursor: pointer; white-space: nowrap;
    transition: color .18s ease;
  }
  .reg .r-tabs a.r-tab:hover, .reg .r-tabs a.doc-tab:hover { color: var(--ink); }
  .reg .r-tabs a.r-tab.active, .reg .r-tabs a.doc-tab.active { color: var(--coral); border-bottom-color: var(--coral); }
  .reg .r-tabs a .badge { background: var(--coral-soft); color: var(--coral); border-radius: 20px; padding: 1px 8px; font-size: 10.5px; font-weight: 700; }
  .reg .r-tabs a.active .badge { background: var(--coral); color: #fff; }

  /* ---------- list rows (no card, divider) ---------- */
  .reg .r-list { display: flex; flex-direction: column; }
  .reg .r-row { display: flex; align-items: center; gap: 15px; padding: 16px 4px; border-bottom: 1px solid var(--divider); }
  .reg .r-row:last-child { border-bottom: none; }
  .reg .r-ic {
    flex: 0 0 auto; width: 46px; height: 46px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center; font-size: 17px;
  }
  .reg .r-ic.coral  { background: var(--coral-soft);  color: var(--coral); }
  .reg .r-ic.amber  { background: var(--amber-soft);  color: var(--amber); }
  .reg .r-ic.green  { background: var(--green-soft);  color: var(--green); }
  .reg .r-ic.blue   { background: var(--blue-soft);   color: var(--blue); }
  .reg .r-ic.purple { background: var(--purple-soft); color: var(--purple); }
  .reg .r-ic.red    { background: var(--red-soft);    color: var(--red); }
  .reg .r-body { flex: 1; min-width: 0; }
  .reg .r-name { font-size: 14px; font-weight: 700; color: var(--ink); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
  .reg .r-num { font-size: 11px; color: var(--muted); font-weight: 500; }
  .reg .r-sub { font-size: 12px; color: var(--muted); margin-top: 3px; }
  .reg .r-sub span { margin: 0 4px; }
  .reg .r-badges { display: flex; gap: 7px; flex-wrap: wrap; justify-content: flex-end; }
  .reg .r-pill { font-size: 11px; font-weight: 700; padding: 5px 11px; border-radius: 20px; white-space: nowrap; }
  .reg .r-pill.p-pending  { background: var(--amber-soft); color: var(--amber); }
  .reg .r-pill.p-verified { background: var(--blue-soft);   color: var(--blue); }
  .reg .r-pill.p-accepted { background: var(--green-soft);  color: var(--green); }
  .reg .r-pill.p-rejected { background: var(--red-soft);    color: var(--red); }
  .reg .r-pill.p-canceled { background: var(--gray-soft);   color: var(--gray); }
  .reg .r-actions { display: flex; gap: 7px; align-items: center; }
  .reg .r-act {
    display: inline-flex; align-items: center; gap: 5px; padding: 8px 13px; border-radius: 10px;
    font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; border: none;
    transition: filter .15s ease, background-color .15s ease;
  }
  .reg .r-act.detail { background: var(--coral-soft); color: var(--coral); }
  .reg .r-act.detail:hover { filter: brightness(0.97); }
  .reg .r-act.reset { background: var(--gray-soft); color: var(--gray); }
  .reg .r-act.reset:hover { background: var(--red-soft); color: var(--red); }

  .reg .r-empty { text-align: center; padding: 44px 16px; color: var(--muted); font-size: 13.5px; }
  .reg .r-empty i { font-size: 26px; display: block; margin-bottom: 10px; color: #d3d6de; }

  /* ---------- pagination (Bringova view) ---------- */
  .reg .r-pager { margin-top: 22px; display: flex; justify-content: center; }
  .reg .r-pager > nav { display: flex; justify-content: center; }

  @media (max-width: 720px) {
    .reg { padding: 20px 16px; }
    .reg .r-row { flex-wrap: wrap; }
    .reg .r-badges { justify-content: flex-start; }
  }
</style>

<div class="reg">
  <div class="r-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span>Pendaftaran</span>
  </div>
  <h1 class="r-title">Daftar Pendaftaran</h1>
  <p class="r-meta">Kelola & pantau seluruh pendaftaran siswa.</p>

  @if (session('success'))
  <div class="r-alert success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
  @endif
  @if (session('error'))
  <div class="r-alert error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
  @endif

  <form id="filterForm" method="GET" action="{{ route('admin.registrations.index') }}">
    <div class="r-toolbar">
      <div class="r-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, NISN, atau no. pendaftaran..." maxlength="100">
      </div>
      <button type="button" class="r-fbtn" onclick="toggleFilterPanel()"><i class="fa-solid fa-filter" style="font-size:12px"></i> Filter</button>
      <button type="submit" class="r-gobtn"><i class="fa-solid fa-magnifying-glass" style="font-size:12px"></i> Cari</button>
    </div>

    <div id="filterPanel" class="r-filters" style="display:none;">
      <div class="r-field">
        <label>Status</label>
        <select name="status">
          <option value="">Semua Status</option>
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
          <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
          <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Diterima</option>
          <option value="re_registration_complete" {{ request('status') == 're_registration_complete' ? 'selected' : '' }}>Daftar Ulang Selesai</option>
        </select>
      </div>
      <div class="r-field">
        <label>Pembayaran</label>
        <select name="payment_status">
          <option value="">Semua Status</option>
          <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
          <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
          <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
          <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Gagal</option>
        </select>
      </div>
      <div class="r-field">
        <label>Deadline</label>
        <select name="deadline">
          <option value="">Semua</option>
          <option value="1" {{ request('deadline') == '1' ? 'selected' : '' }}>Ada Batas Waktu</option>
        </select>
      </div>
      <div class="r-field">
        <label>Jalur</label>
        <select name="track_id">
          <option value="">Semua Jalur</option>
          @foreach (($tracks ?? collect()) as $trk)
            <option value="{{ $trk->id }}" {{ request('track_id') == $trk->id ? 'selected' : '' }}>{{ $trk->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="r-field">
        <label>Jurusan</label>
        <select name="major_id">
          <option value="">Semua Jurusan</option>
          @foreach (($majors ?? collect()) as $mjr)
            <option value="{{ $mjr->id }}" {{ request('major_id') == $mjr->id ? 'selected' : '' }}>{{ $mjr->name }}</option>
          @endforeach
        </select>
      </div>
    </div>
  </form>

  <div class="r-tabs">
    <a href="{{ route('admin.registrations.index') }}" class="r-tab doc-tab {{ !request('status') && !request('payment_status') && !request('deadline') && !request('search') && !request('track_id') && !request('major_id') ? 'active' : '' }}">Semua</a>
    <a href="{{ route('admin.registrations.index', ['status' => 'pending']) }}" class="r-tab doc-tab {{ request('status') == 'pending' ? 'active' : '' }}">Pending</a>
    <a href="{{ route('admin.registrations.index', ['status' => 'verified']) }}" class="r-tab doc-tab {{ request('status') == 'verified' ? 'active' : '' }}">Terverifikasi</a>
    <a href="{{ route('admin.registrations.index', ['status' => 'accepted']) }}" class="r-tab doc-tab {{ request('status') == 'accepted' ? 'active' : '' }}">Diterima</a>
    <a href="{{ route('admin.registrations.index', ['status' => 'rejected']) }}" class="r-tab doc-tab {{ request('status') == 'rejected' ? 'active' : '' }}">Ditolak</a>
    <a href="{{ route('admin.registrations.index', ['status' => 're_registration_complete']) }}" class="r-tab doc-tab {{ request('status') == 're_registration_complete' ? 'active' : '' }}">Daftar Ulang</a>
    <a href="{{ route('admin.registrations.index', ['deadline' => 1]) }}" class="r-tab doc-tab {{ request('deadline') ? 'active' : '' }}">Deadline</a>
  </div>

  @if ($registrations->isEmpty())
    <div class="r-empty">
      <i class="fa-regular fa-folder-open"></i>
      Tidak ada pendaftaran yang cocok
    </div>
  @else
    <div class="r-list">
      @foreach ($registrations as $reg)
      @php
        $name = $reg->applicant->full_name ?? '-';
        $email = $reg->applicant->user->email ?? '-';
        $track = $reg->registrationTrack->name ?? '-';
        $major = $reg->major->name ?? '-';
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
        $pm = ['unpaid' => ['label' => 'Belum Bayar', 'pill' => 'p-pending'], 'pending' => ['label' => 'Menunggu', 'pill' => 'p-pending'], 'paid' => ['label' => 'Lunas', 'pill' => 'p-accepted'], 'failed' => ['label' => 'Gagal', 'pill' => 'p-rejected']];
        $pay = $pm[$reg->payment_status] ?? ['label' => ucfirst($reg->payment_status), 'pill' => 'p-pending'];
      @endphp
      <div class="r-row">
        <span class="r-ic {{ $s['pill'] == 'p-rejected' ? 'red' : ($s['pill'] == 'p-pending' ? 'amber' : ($s['pill'] == 'p-accepted' ? 'green' : 'blue')) }}">
          <i class="fa-regular fa-file-lines"></i>
        </span>
        <div class="r-body">
          <div class="r-name">
            {{ $name }}
            <span class="r-num">{{ $reg->registration_number }}</span>
          </div>
          <div class="r-sub">
            {{ $email }}
            <span>&middot;</span>{{ $track }}
            <span>&middot;</span>{{ $major }}
            <span>&middot;</span>{{ $reg->created_at->format('d M Y H:i') }}
          </div>
        </div>
        <div class="r-badges">
          <span class="r-pill {{ $s['pill'] }}">{{ $s['label'] }}</span>
          <span class="r-pill {{ $pay['pill'] }}">{{ $pay['label'] }}</span>
        </div>
        <div class="r-actions">
          <a href="{{ route('admin.registrations.show', $reg) }}" class="r-act detail"><i class="fa-solid fa-eye" style="font-size:11px"></i> Detail</a>
          <button type="button" onclick="openResetModal({{ $reg->id }}, '{{ addslashes($reg->registration_number) }}', '{{ addslashes($name) }}')" class="r-act reset"><i class="fa-solid fa-rotate-left" style="font-size:11px"></i> Reset</button>
        </div>
      </div>
      @endforeach
    </div>

    <div class="r-pager">
      {{ $registrations->appends(request()->query())->links('vendor.pagination.bringova') }}
    </div>
  @endif
</div>

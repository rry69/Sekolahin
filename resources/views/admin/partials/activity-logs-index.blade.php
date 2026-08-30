<style>
  .alg { --coral:#FF6B6B; --coral-soft:#FFE5E3; --coral-2:#FF8E6E; --amber:#F59E0B; --amber-soft:#FEF3C7; --green:#10B981; --green-soft:#D1FAE5; --blue:#3B82F6; --blue-soft:#DBEAFE; --purple:#8B5CF6; --purple-soft:#EDE9FE; --red:#EF4444; --red-soft:#FEE2E2; --gray:#6b7280; --gray-soft:#F3F4F6; --ink:#1a1a2e; --muted:#8a8f9d; --divider:rgba(26,26,46,0.10); position:relative; border-radius:24px; padding:28px 28px 40px; background:#f6f7fb; }
  .alg .alg-crumb { display:flex; align-items:center; gap:8px; font-size:12.5px; color:var(--muted); margin-bottom:6px; font-weight:500; }
  .alg .alg-crumb a { color:var(--coral); text-decoration:none; }
  .alg .alg-crumb a:hover { text-decoration:underline; }
  .alg .alg-crumb .sep { color:#d3d6de; }
  .alg .alg-title { font-size:26px; font-weight:800; color:var(--ink); letter-spacing:-0.01em; margin-bottom:2px; }
  .alg .alg-meta { font-size:13px; color:var(--muted); margin-bottom:14px; }
  .alg .alg-head { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
  .alg .alg-head-actions { display:flex; align-items:center; gap:8px; }
  .alg .alg-count { font-size:12px; color:var(--muted); }
  .alg .alg-count strong { color:var(--ink); }
  .alg .alg-btn { display:inline-flex; align-items:center; gap:6px; border:none; cursor:pointer; border-radius:11px; padding:8px 14px; font-size:12.5px; font-weight:700; text-decoration:none; transition:transform .15s, filter .15s, background .15s; }
  .alg .alg-btn:hover { transform:translateY(-1px); }
  .alg .alg-btn.ghost { background:rgba(255,255,255,0.6); color:var(--ink); box-shadow:0 2px 10px -8px rgba(26,26,46,0.3); }
  .alg .alg-btn.ghost:hover { background:#fff; color:var(--coral); }
  .alg .alg-btn.sm { padding:7px 12px; font-size:12px; border-radius:9px; }
  .alg .alg-toolbar { display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:12px; }
  .alg .alg-search { position:relative; flex:1; min-width:180px; }
  .alg .alg-search i { position:absolute; left:13px; top:50%; transform:translateY(-50%); color:var(--muted); font-size:12px; pointer-events:none; }
  .alg .alg-search input { width:100%; padding:10px 14px 10px 36px; border:1px solid rgba(26,26,46,0.14); border-radius:11px; font-size:13px; color:var(--ink); background:rgba(255,255,255,0.55); box-sizing:border-box; transition:border-color .18s, box-shadow .18s, background .18s; }
  .alg .alg-search input::placeholder { color:var(--muted); }
  .alg .alg-search input:focus { outline:none; border-color:var(--coral); box-shadow:0 0 0 4px rgba(255,107,107,0.14); background:#fff; }
  .alg .alg-fbtn { display:inline-flex; align-items:center; gap:6px; padding:9px 14px; border-radius:10px; border:1px solid var(--divider); font-size:12.5px; font-weight:600; color:var(--muted); background:rgba(255,255,255,0.6); cursor:pointer; transition:all .15s; }
  .alg .alg-fbtn:hover { background:#fff; color:var(--coral); border-color:var(--coral); }
  .alg .alg-gobtn { display:inline-flex; align-items:center; gap:6px; padding:10px 18px; border-radius:11px; border:none; font-size:13px; font-weight:700; color:#fff; background:linear-gradient(135deg,var(--coral),var(--coral-2)); cursor:pointer; box-shadow:0 8px 18px -8px rgba(255,107,107,0.6); transition:filter .15s, transform .15s; }
  .alg .alg-gobtn:hover { filter:brightness(1.04); transform:translateY(-1px); }
  .alg .alg-filters { display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; padding:14px 16px; border:1px dashed rgba(26,26,46,0.14); border-radius:14px; background:rgba(255,255,255,0.35); margin-bottom:14px; }
  .alg .alg-field { display:flex; flex-direction:column; gap:5px; min-width:150px; }
  .alg .alg-field label { font-size:11px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.4px; }
  .alg .alg-field .x-date-picker input { padding:9px 12px; border:1px solid rgba(26,26,46,0.14); border-radius:10px; font-size:13px; }
  .alg .r-pick { display:inline-flex; align-items:center; gap:8px; flex-wrap:nowrap; padding:9px 4px; border:none; border-bottom:1px solid rgba(26,26,46,0.18); border-radius:0; font-size:13px; color:var(--ink); background:transparent; min-width:150px; max-width:200px; cursor:pointer; text-align:left; min-height:38px; transition:border-color .18s, color .18s; }
  .alg .r-pick:hover { border-bottom-color:var(--coral); }
  .alg .r-pick:focus { outline:none; border-bottom-color:var(--coral); }
  .alg .r-pick .pick-label { flex:1 1 auto; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .alg .r-pick .pick-label.is-placeholder { color:var(--muted); }
  .alg .r-pick .pick-caret { display:none; }
  .alg .r-pick .pick-clear { flex:0 0 auto; display:none; align-items:center; justify-content:center; width:18px; height:18px; border-radius:6px; background:var(--gray-soft); color:var(--gray); cursor:pointer; font-size:9px; }
  .alg .r-pick .pick-clear:hover { background:var(--red-soft); color:var(--red); }
  .alg .r-pick.has-value .pick-clear { display:inline-flex; }
  .alg .picker-backdrop { position:fixed; inset:0; z-index:80; background:rgba(26,26,46,0.32); backdrop-filter:blur(4px); -webkit-backdrop-filter:blur(4px); display:none; align-items:flex-start; justify-content:center; padding:80px 16px 16px; animation:algPickerFade .18s ease-out; }
  .alg .picker-backdrop.is-open { display:flex; }
  @keyframes algPickerFade { from{opacity:0} to{opacity:1} }
  .alg .picker-panel { width:100%; max-width:380px; max-height:min(520px,calc(100vh - 120px)); display:flex; flex-direction:column; background:#fff; border-radius:18px; box-shadow:0 20px 50px -16px rgba(26,26,46,0.35),0 0 0 1px rgba(26,26,46,0.06); overflow:hidden; animation:algPickerPop .22s cubic-bezier(.22,1.2,.36,1); }
  @keyframes algPickerPop { from{opacity:0; transform:translateY(-6px) scale(0.98)} to{opacity:1; transform:translateY(0) scale(1)} }
  .alg .picker-head { display:flex; align-items:center; gap:10px; padding:14px 16px; border-bottom:1px solid var(--divider); }
  .alg .picker-head .picker-title { font-size:14px; font-weight:700; color:var(--ink); flex:1; }
  .alg .picker-head .picker-close { display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:8px; border:none; background:transparent; color:var(--muted); cursor:pointer; font-size:12px; }
  .alg .picker-head .picker-close:hover { background:var(--gray-soft); color:var(--ink); }
  .alg .picker-search { position:relative; padding:10px 14px; border-bottom:1px solid var(--divider); }
  .alg .picker-search i { position:absolute; left:24px; top:50%; transform:translateY(-50%); color:var(--muted); font-size:12px; pointer-events:none; }
  .alg .picker-search input { width:100%; padding:9px 12px 9px 32px; border:1px solid rgba(26,26,46,0.14); border-radius:10px; font-size:13px; color:var(--ink); background:rgba(255,255,255,0.7); }
  .alg .picker-search input:focus { outline:none; border-color:var(--coral); background:#fff; box-shadow:0 0 0 3px rgba(255,107,107,0.12); }
  .alg .picker-list { flex:1; overflow-y:auto; padding:6px 8px; }
  .alg .picker-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; font-size:13px; color:var(--ink); cursor:pointer; }
  .alg .picker-item:hover, .alg .picker-item.is-active { background:var(--coral-soft); color:var(--coral); }
  .alg .picker-item.is-selected { background:var(--coral); color:#fff; font-weight:600; }
  .alg .picker-item .pi-label { flex:1; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .alg .picker-item .pi-check { font-size:11px; opacity:0; }
  .alg .picker-item.is-selected .pi-check { opacity:1; }
  .alg .picker-empty { padding:26px 12px; text-align:center; color:var(--muted); font-size:12.5px; }
  .alg .picker-empty i { display:block; font-size:20px; margin-bottom:6px; color:#d3d6de; }
  .alg .picker-foot { display:flex; align-items:center; justify-content:space-between; gap:8px; padding:10px 14px; border-top:1px solid var(--divider); background:rgba(255,255,255,0.5); }
  .alg .picker-foot .picker-clear-all { display:inline-flex; align-items:center; gap:6px; padding:7px 12px; border-radius:9px; border:none; background:transparent; color:var(--muted); font-size:12px; font-weight:600; cursor:pointer; }
  .alg .picker-foot .picker-clear-all:hover { color:var(--red); background:var(--red-soft); }
  .alg .picker-foot .picker-done { display:inline-flex; align-items:center; gap:6px; padding:7px 16px; border-radius:9px; border:none; background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; font-size:12px; font-weight:700; cursor:pointer; }
  .alg .alg-list { display:flex; flex-direction:column; }
  .alg .alg-row { display:flex; gap:14px; padding:14px 4px; border-bottom:1px solid var(--divider); align-items:flex-start; }
  .alg .alg-row:last-child { border-bottom:none; }
  .alg .alg-ic { flex:0 0 auto; width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:14px; background:var(--gray-soft); color:var(--gray); }
  .alg .alg-ic.hl-danger { background:var(--red-soft); color:var(--red); }
  .alg .alg-ic.hl-success { background:var(--green-soft); color:var(--green); }
  .alg .alg-body { flex:1; min-width:0; }
  .alg .alg-row-head { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
  .alg .alg-time { font-size:12px; color:var(--muted); }
  .alg .alg-pill { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:700; }
  .alg .alg-pill.green { background: transparent; border: 1px solid currentColor; color:var(--green); }
  .alg .alg-pill.red { background: transparent; border: 1px solid currentColor; color:var(--red); }
  .alg .alg-pill.amber { background: transparent; border: 1px solid currentColor; color:#b45309; }
  .alg .alg-pill.blue { background: transparent; border: 1px solid currentColor; color:var(--blue); }
  .alg .alg-pill.gray { background: transparent; border: 1px solid currentColor; color:var(--gray); }
  .alg .alg-desc { font-size:13px; color:var(--ink); margin-top:4px; }
  .alg .alg-sub { font-size:11.5px; color:var(--muted); margin-top:3px; display:flex; gap:6px; flex-wrap:wrap; }
  .alg .alg-sub .dot { color:#d3d6de; }
  .alg .alg-detail { margin-top:6px; }
  .alg .alg-detail summary { cursor:pointer; color:var(--coral); font-size:12px; font-weight:600; }
  .alg .alg-detail pre { background:rgba(255,255,255,0.6); padding:8px; border-radius:8px; margin-top:6px; max-width:100%; overflow:auto; font-size:11px; color:var(--ink); border:1px solid var(--divider); }
  .alg .alg-empty { text-align:center; color:var(--muted); font-size:13px; padding:36px 0; }
  .alg .alg-empty i { display:block; font-size:28px; margin-bottom:10px; color:#d3d6de; }
  .alg .alg-pager { margin-top:18px; display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; border-top:1px solid var(--divider); padding-top:12px; font-size:12px; color:var(--muted); }
  @media(max-width:720px){ .alg{padding:20px 16px 32px;} .alg .alg-row{flex-wrap:wrap;} }
</style>

<div class="alg">
  <div class="alg-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span>Log Aktivitas</span>
  </div>
  <div class="alg-head">
    <div>
      <h1 class="alg-title"><x-hi name="work-history" style="color:var(--coral);margin-right:8px;" />Log Aktivitas</h1>
      <p class="alg-meta">Catat semua aksi penting: upload dokumen, pembayaran, dan perubahan status beserta waktu & IP.</p>
    </div>
    <div class="alg-head-actions">
      <span class="alg-count">Menampilkan <strong>{{ $filtered }}</strong> dari <strong>{{ $total }}</strong> entri</span>
      <a href="{{ route('admin.activity-logs.export.csv', request()->query()) }}" class="alg-btn ghost sm"><x-hi name="csv-01" /> CSV</a>
      <a href="{{ route('admin.activity-logs.export.xlsx', request()->query()) }}" class="alg-btn ghost sm"><x-hi name="xls-01" /> XLSX</a>
    </div>
  </div>

  <form id="filterForm" method="GET" action="{{ route('admin.activity-logs.index') }}">
    <div class="alg-toolbar">
      <div class="alg-search">
        <x-hi name="search-01" />
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari deskripsi / aksi / IP..." autocomplete="off">
      </div>
      <button type="button" class="alg-fbtn" onclick="toggleFilterPanel()"><x-hi name="filter" style="font-size:10px" /> Filter</button>
      <button type="submit" class="alg-gobtn">Cari</button>
    </div>
    <div id="filterPanel" class="alg-filters" style="display:{{ request('action') || request('user_id') || request('date_from') || request('date_to') || request('per_page') != 30 && request('per_page') ? 'flex' : 'none' }};">
      <div class="alg-field">
        <label>Aksi</label>
        <button type="button" class="r-pick" data-picker="alg_action" aria-haspopup="listbox">
          <span class="pick-label {{ request('action') ? '' : 'is-placeholder' }}">{{ request('action') ? \App\Models\ActivityLog::make(['action' => request('action')])->label() : 'Semua aksi' }}</span>
          <span class="pick-clear" data-clear="alg_action" role="button" tabindex="0"><x-hi name="cancel-01" /></span>
        </button>
        <input type="hidden" name="action" data-picker-input="alg_action" value="{{ request('action') }}">
      </div>
      <div class="alg-field">
        <label>User</label>
        <button type="button" class="r-pick" data-picker="alg_user" aria-haspopup="listbox">
          <span class="pick-label {{ request('user_id') ? '' : 'is-placeholder' }}">{{ request('user_id') ? ($users->firstWhere('id', (int)request('user_id'))->name ?? 'User') : 'Semua user' }}</span>
          <span class="pick-clear" data-clear="alg_user" role="button" tabindex="0"><x-hi name="cancel-01" /></span>
        </button>
        <input type="hidden" name="user_id" data-picker-input="alg_user" value="{{ request('user_id') }}">
      </div>
      <div class="alg-field">
        <label>Dari</label>
        <x-date-picker name="date_from" :value="request('date_from')" label="Dari" />
      </div>
      <div class="alg-field">
        <label>Sampai</label>
        <x-date-picker name="date_to" :value="request('date_to')" label="Sampai" />
      </div>
      <div class="alg-field">
        <label>Per Halaman</label>
        <button type="button" class="r-pick" data-picker="alg_perpage" aria-haspopup="listbox">
          <span class="pick-label">{{ request('per_page', 30) }}</span>
          <span class="pick-clear" data-clear="alg_perpage" role="button" tabindex="0" style="display:none;"><x-hi name="cancel-01" /></span>
        </button>
        <input type="hidden" name="per_page" data-picker-input="alg_perpage" value="{{ request('per_page', 30) }}">
      </div>
      <button type="submit" class="alg-gobtn" style="padding:8px 16px;">Terapkan</button>
      <a href="{{ route('admin.activity-logs.index') }}" class="alg-btn ghost sm">Reset</a>
    </div>
  </form>

  @if($logs->isEmpty())
    <div class="alg-empty">
      <x-hi name="clipboard" />
      <div style="font-weight:600;color:var(--ink);margin-bottom:4px;">{{ request('search') || request('action') || request('user_id') || request('date_from') || request('date_to') ? 'Tidak ada hasil' : 'Belum ada log aktivitas' }}</div>
      <div style="font-size:12px;">{{ request('search') || request('action') || request('user_id') || request('date_from') || request('date_to') ? 'Coba ubah kata kunci atau hapus filter.' : 'Aksi penting akan tercatat otomatis di sini.' }}</div>
      @if(request('search') || request('action') || request('user_id') || request('date_from') || request('date_to'))
        <a href="{{ route('admin.activity-logs.index') }}" class="alg-btn ghost sm" style="margin-top:14px;">Reset Filter</a>
      @endif
    </div>
  @else
    <div class="alg-list">
      @foreach($logs as $log)
        <div class="alg-row" @if($log->isHighlight()) style="background:rgba(255,255,255,0.45);border-radius:12px;" @endif>
          <span class="alg-ic {{ $log->badgeClass() === 'status-rejected' ? 'hl-danger' : ($log->badgeClass() === 'status-accepted' ? 'hl-success' : '') }}"><x-hi :name="$log->icon()" /></span>
          <div class="alg-body">
            <div class="alg-row-head">
              <span class="alg-pill {{ $log->badgeClass() === 'status-rejected' ? 'red' : ($log->badgeClass() === 'status-accepted' ? 'green' : ($log->badgeClass() === 'status-pending' ? 'amber' : 'gray')) }}"><x-hi :name="$log->icon()" style="font-size:10px;" />{{ $log->label() }}</span>
              <span class="alg-time">{{ $log->created_at->format('d/m/Y H:i:s') }} · {{ $log->created_at->diffForHumans() }}</span>
            </div>
            <div class="alg-desc">{{ $log->description ?? '-' }}</div>
            <div class="alg-sub">
              <span>{{ $log->user ? $log->userName().' · '.$log->user->email : $log->userName() }}</span>
              <span class="dot">·</span>
              <span style="font-family:monospace;">{{ $log->displayIp() }}</span>
            </div>
            @if($log->properties)
              <details class="alg-detail">
                <summary>Lihat detail</summary>
                <pre>{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
              </details>
            @endif
          </div>
        </div>
      @endforeach
    </div>
    <div class="alg-pager">
      <span>{{ $logs->firstItem() ? 'Menampilkan ' . $logs->firstItem() . '–' . $logs->lastItem() . ' dari ' . $logs->total() : '0 entri' }}</span>
      <div>{{ $logs->links('vendor.pagination.bringova') }}</div>
    </div>
  @endif

<div id="pickerBackdrop" class="picker-backdrop" aria-hidden="true">
  <div class="picker-panel" role="dialog" aria-modal="true" aria-labelledby="pickerTitle">
    <div class="picker-head">
      <div class="picker-title" id="pickerTitle">Pilih item</div>
      <button type="button" class="picker-close" onclick="closePicker()" aria-label="Tutup"><x-hi name="cancel-01" /></button>
    </div>
    <div class="picker-search">
      <x-hi name="search-01" />
      <input id="pickerSearch" type="search" placeholder="Cari…" autocomplete="off">
    </div>
    <div class="picker-list" id="pickerList" role="listbox"></div>
    <div class="picker-foot">
      <button type="button" class="picker-clear-all" onclick="clearCurrentPicker()"><x-hi name="eraser-01" /> Bersihkan</button>
      <button type="button" class="picker-done" onclick="closePicker()">Selesai</button>
    </div>
  </div>
</div>

@php
  $pickActions = [['v'=>'','l'=>'Semua aksi']];
  foreach($actions as $act){ $pickActions[] = ['v'=>$act, 'l'=> \App\Models\ActivityLog::make(['action'=>$act])->label() . ' ('.$act.')']; }
  $pickUsers = [['v'=>'','l'=>'Semua user']];
  foreach($users as $u){ $pickUsers[] = ['v'=>(string)$u->id, 'l'=>$u->name . ' - ' . $u->email]; }
  $pickPerPage = [['v'=>'15','l'=>'15'],['v'=>'30','l'=>'30'],['v'=>'50','l'=>'50'],['v'=>'100','l'=>'100']];
  $pickerJson = ['alg_action'=>$pickActions, 'alg_user'=>$pickUsers, 'alg_perpage'=>$pickPerPage];
  $pickerLabels = ['alg_action'=>'Pilih Aksi', 'alg_user'=>'Pilih User', 'alg_perpage'=>'Per Halaman'];
@endphp
<div id="reg-data" hidden data-picker='@json($pickerJson)' data-picker-labels='@json($pickerLabels)'></div>
</div>
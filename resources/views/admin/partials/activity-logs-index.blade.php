<div class="page-title" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
  <span><i class="fa-solid fa-clock-rotate-left" style="color:var(--accent);margin-right:8px;"></i>Log Aktivitas</span>
  <div style="display:flex;align-items:center;gap:10px;">
    <span style="font-size:12px;color:var(--tx3);">
      Menampilkan <strong>{{ $filtered }}</strong> dari <strong>{{ $total }}</strong> entri
    </span>
    <a href="{{ route('admin.activity-logs.export.csv', request()->query()) }}" class="btn btn-outline" style="height:32px;font-size:12px;">
      <i class="fa-solid fa-file-csv"></i> CSV
    </a>
    <a href="{{ route('admin.activity-logs.export.xlsx', request()->query()) }}" class="btn btn-outline" style="height:32px;font-size:12px;">
      <i class="fa-solid fa-file-excel"></i> XLSX
    </a>
  </div>
</div>
<p style="font-size:13px;color:var(--tx3);margin-bottom:16px;">Catat semua aksi penting: upload dokumen, pembayaran, dan perubahan status beserta waktu & IP.</p>

<form id="filterForm" method="GET" action="{{ route('admin.activity-logs.index') }}" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;align-items:flex-end;background:var(--card-bg);border:1px solid var(--border);border-radius:10px;padding:12px;">
  <div style="flex:1;min-width:160px;">
    <label style="font-size:11px;color:var(--tx3);display:block;margin-bottom:4px;">Cari</label>
    <input type="text" name="search" id="activitySearch" value="{{ request('search') }}" placeholder="Deskripsi / aksi / IP..." style="width:100%;padding:7px 10px;border:1px solid var(--input-border);border-radius:6px;font-size:13px;background:var(--input-bg);color:var(--tx-body);">
  </div>
  <div style="min-width:150px;">
    <label style="font-size:11px;color:var(--tx3);display:block;margin-bottom:4px;">Aksi</label>
    <select name="action" style="width:100%;padding:7px 10px;border:1px solid var(--input-border);border-radius:6px;font-size:13px;background:var(--input-bg);color:var(--tx-body);">
      <option value="">Semua aksi</option>
      @foreach($actions as $act)
        <option value="{{ $act }}" @selected(request('action') === $act)>{{ \App\Models\ActivityLog::make(['action' => $act])->label() }} ({{ $act }})</option>
      @endforeach
    </select>
  </div>
  <div style="min-width:150px;">
    <label style="font-size:11px;color:var(--tx3);display:block;margin-bottom:4px;">User</label>
    <select name="user_id" style="width:100%;padding:7px 10px;border:1px solid var(--input-border);border-radius:6px;font-size:13px;background:var(--input-bg);color:var(--tx-body);">
      <option value="">Semua user</option>
      @foreach($users as $u)
        <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
      @endforeach
    </select>
  </div>
  <div style="min-width:140px;">
    <label style="font-size:11px;color:var(--tx3);display:block;margin-bottom:4px;">Dari</label>
    <x-date-picker name="date_from" :value="request('date_from')" label="Dari" />
  </div>
  <div style="min-width:140px;">
    <label style="font-size:11px;color:var(--tx3);display:block;margin-bottom:4px;">Sampai</label>
    <x-date-picker name="date_to" :value="request('date_to')" label="Sampai" />
  </div>
  <div style="min-width:110px;">
    <label style="font-size:11px;color:var(--tx3);display:block;margin-bottom:4px;">Per Halaman</label>
    <select name="per_page" style="width:100%;padding:7px 10px;border:1px solid var(--input-border);border-radius:6px;font-size:13px;background:var(--input-bg);color:var(--tx-body);">
      @foreach([15,30,50,100] as $pp)
        <option value="{{ $pp }}" @selected((int) request('per_page', 30) === $pp)>{{ $pp }}</option>
      @endforeach
    </select>
  </div>
  <div style="display:flex;gap:8px;">
    <button type="submit" class="btn btn-primary" style="height:34px;"><i class="fa-solid fa-filter"></i> Filter</button>
    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline" style="height:34px;">Reset</a>
  </div>
</form>

<div style="background:var(--card-bg);border:1px solid var(--border);border-radius:10px;overflow:hidden;">
  <div style="overflow-x:auto;">
    <table class="data-table">
      <thead>
        <tr>
          <th>Waktu</th>
          <th>Aksi</th>
          <th>Deskripsi</th>
          <th>User</th>
          <th>IP</th>
          <th>Detail</th>
        </tr>
      </thead>
      <tbody id="activityBody">
        @forelse($logs as $log)
          <tr @if($log->isHighlight()) style="background:{{ $log->badgeClass() === 'status-rejected' ? 'rgba(239,68,68,0.06)' : 'rgba(16,185,129,0.05)' }};" @endif>
            <td style="white-space:nowrap;font-size:12px;color:var(--tx2);">
              {{ $log->created_at->format('d/m/Y H:i:s') }}
              <div style="font-size:11px;color:var(--tx4);">{{ $log->created_at->diffForHumans() }}</div>
            </td>
            <td>
              <span class="status-badge {{ $log->badgeClass() }}" title="{{ $log->action }}" style="white-space:nowrap;">
                <i class="fa-solid {{ $log->icon() }}" style="margin-right:4px;font-size:10px;"></i>{{ $log->label() }}
              </span>
            </td>
            <td style="max-width:320px;">{{ $log->description ?? '-' }}</td>
            <td>
              @if($log->user)
                <div style="font-weight:600;">{{ $log->userName() }}</div>
                <div style="font-size:11px;color:var(--tx3);">{{ $log->user->email }}</div>
              @else
                <span style="color:var(--tx4);">{{ $log->userName() }}</span>
              @endif
            </td>
            <td style="font-family:monospace;font-size:12px;">{{ $log->displayIp() }}</td>
            <td>
              @if($log->properties)
                <details style="font-size:12px;">
                  <summary style="cursor:pointer;color:var(--accent);">Lihat</summary>
                  <pre style="background:var(--panel-2);padding:8px;border-radius:6px;margin-top:6px;max-width:280px;overflow:auto;font-size:11px;color:var(--tx2);">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </details>
              @else
                <span style="color:var(--tx4);">-</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="empty-state" style="padding:48px 20px;">
              <div style="font-size:32px;margin-bottom:10px;color:var(--tx4);"><i class="fa-solid fa-clipboard-list"></i></div>
              <div style="font-weight:600;color:var(--tx2);margin-bottom:4px;">
                {{ request('search') || request('action') || request('user_id') || request('date_from') || request('date_to') ? 'Tidak ada hasil' : 'Belum ada log aktivitas' }}
              </div>
              <div style="font-size:12px;color:var(--tx3);">
                {{ request('search') || request('action') || request('user_id') || request('date_from') || request('date_to') ? 'Coba ubah kata kunci atau hapus filter.' : 'Aksi penting akan tercatat otomatis di sini.' }}
              </div>
              @if(request('search') || request('action') || request('user_id') || request('date_from') || request('date_to'))
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline" style="margin-top:14px;height:32px;">Reset Filter</a>
              @endif
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:12px 16px;border-top:1px solid var(--hairline);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
    <span style="font-size:12px;color:var(--tx3);">
      {{ $logs->firstItem() ? 'Menampilkan ' . $logs->firstItem() . '–' . $logs->lastItem() . ' dari ' . $logs->total() : '0 entri' }}
    </span>
    <div>{{ $logs->links() }}</div>
  </div>
</div>

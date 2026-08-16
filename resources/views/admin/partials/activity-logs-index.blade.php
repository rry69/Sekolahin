<div class="page-title" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
  <span><i class="fa-solid fa-clock-rotate-left" style="color:#4f6ef7;margin-right:8px;"></i>Log Aktivitas</span>
  <span style="font-size:13px;font-weight:400;color:#888;">{{ $logs->total() }} entri</span>
</div>
<p style="font-size:13px;color:#888;margin-bottom:16px;">Catat semua aksi penting: upload dokumen, pembayaran, dan perubahan status beserta waktu & IP.</p>

<form id="filterForm" method="GET" action="{{ route('admin.activity-logs.index') }}" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;align-items:flex-end;">
  <div style="flex:1;min-width:160px;">
    <label style="font-size:11px;color:#888;display:block;margin-bottom:4px;">Cari</label>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Deskripsi / aksi / IP..." style="width:100%;padding:6px 10px;border:1px solid #e0e0e0;border-radius:6px;font-size:13px;">
  </div>
  <div style="min-width:180px;">
    <label style="font-size:11px;color:#888;display:block;margin-bottom:4px;">Aksi</label>
    <select name="action" style="width:100%;padding:6px 10px;border:1px solid #e0e0e0;border-radius:6px;font-size:13px;">
      <option value="">Semua aksi</option>
      @foreach($actions as $act)
        <option value="{{ $act }}" @selected(request('action') === $act)>{{ $act }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label style="font-size:11px;color:#888;display:block;margin-bottom:4px;">Dari tanggal</label>
    <x-date-picker name="date_from" :value="request('date_from')" label="Dari" />
  </div>
  <div>
    <label style="font-size:11px;color:#888;display:block;margin-bottom:4px;">Sampai</label>
    <x-date-picker name="date_to" :value="request('date_to')" label="Sampai" />
  </div>
  <button type="submit" class="btn btn-primary" style="height:34px;"><i class="fa-solid fa-filter"></i> Filter</button>
  <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline" style="height:34px;">Reset</a>
</form>

<div style="background:#fff;border:1px solid #e8e8e8;border-radius:10px;overflow:hidden;">
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
      <tbody>
        @forelse($logs as $log)
          <tr>
            <td style="white-space:nowrap;font-size:12px;color:#666;">
              {{ $log->created_at->format('d/m/Y H:i:s') }}
              <div style="font-size:11px;color:#aaa;">{{ $log->created_at->diffForHumans() }}</div>
            </td>
            <td><code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:11px;">{{ $log->action }}</code></td>
            <td style="max-width:320px;">{{ $log->description ?? '-' }}</td>
            <td>
              @if($log->user)
                <div style="font-weight:600;">{{ $log->user->name }}</div>
                <div style="font-size:11px;color:#888;">{{ $log->user->email }}</div>
              @else
                <span style="color:#aaa;">system / webhook</span>
              @endif
            </td>
            <td style="font-family:monospace;font-size:12px;">{{ $log->ip_address ?? '-' }}</td>
            <td>
              @if($log->properties)
                <details style="font-size:12px;">
                  <summary style="cursor:pointer;color:#4f6ef7;">Lihat</summary>
                  <pre style="background:#f8f9fb;padding:8px;border-radius:6px;margin-top:6px;max-width:280px;overflow:auto;font-size:11px;">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </details>
              @else
                <span style="color:#ccc;">-</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="empty-state">Belum ada log aktivitas.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:12px 16px;border-top:1px solid #f0f0f0;">
    {{ $logs->links() }}
  </div>
</div>

<div class="page-title" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
  <span><i class="fa-solid fa-route" style="color:var(--accent);margin-right:8px;"></i>Pengaturan Jalur Pendaftaran</span>
  <span style="font-size:13px;font-weight:400;color:var(--tx3);">Status aktif/nonaktif per jenjang</span>
</div>
<p style="font-size:13px;color:var(--tx3);margin-bottom:16px;">
  Kelola status aktif/nonaktif jalur (Beasiswa, Prestasi, Reguler) per jenjang. Jalur yang dinonaktifkan tidak muncul di form pendaftaran siswa dan ditolak di backend. Data historis pendaftar lama tetap tersimpan.
</p>

@if ($levels->isEmpty() || $tracks->isEmpty())
  <div class="empty-state">Belum ada jenjang atau jalur terdaftar.</div>
@else
  <div style="display:flex;flex-direction:column;gap:20px;">
    @foreach ($levels as $level)
      <div style="background:var(--card-bg);border:1px solid var(--border);border-radius:10px;overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid var(--hairline);background:var(--panel-2);">
          <div>
            <div style="font-weight:600;font-size:14px;color:var(--tx1);">{{ $level->name }}</div>
            @if($level->description)
              <div style="font-size:12px;color:var(--tx3);margin-top:2px;">{{ $level->description }}</div>
            @endif
          </div>
          <span class="status-badge {{ $level->is_active ? 'status-verified' : 'status-pending' }}" style="{{ $level->is_active ? '' : 'background:var(--panel-2);color:var(--tx3);' }}">{{ $level->is_active ? 'Jenjang Aktif' : 'Jenjang Nonaktif' }}</span>
        </div>
        <div>
          @foreach ($tracks as $track)
            @php
              $isActive = $statusMap[$level->id][$track->id] ?? true;
              $count = $counts->get($level->id)?->get($track->id)?->total ?? 0;
            @endphp
            <div class="track-row" style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid var(--hairline);" data-track-id="{{ $track->id }}" data-level-id="{{ $level->id }}">
              <div style="flex:1;">
                <div style="display:flex;align-items:center;gap:8px;">
                  <span style="font-weight:500;font-size:14px;color:var(--tx1);">{{ $track->name }}</span>
                  <span class="status-badge track-badge {{ $isActive ? 'status-accepted' : 'status-rejected' }}" style="{{ $isActive ? '' : 'background:var(--badge-rejected-bg);color:var(--badge-rejected-fg);' }}">{{ $isActive ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                @if($track->description)
                  <div style="font-size:12px;color:var(--tx3);margin-top:2px;">{{ $track->description }}</div>
                @endif
                <div style="font-size:11px;color:var(--tx4);margin-top:2px;">Pendaftar jenjang ini: <span style="font-weight:600;color:var(--tx2);">{{ $count }}</span> (historis tetap tersimpan)</div>
              </div>
              <label style="position:relative;display:inline-flex;align-items:center;cursor:pointer;margin-left:16px;flex-shrink:0;">
                <input type="checkbox" class="sr-only track-toggle" data-track="{{ $track->id }}" data-level="{{ $level->id }}"
                  data-track-name="{{ $track->name }}" data-level-name="{{ $level->name }}" data-status="{{ $isActive ? '1' : '0' }}" {{ $isActive ? 'checked' : '' }}>
                <div class="track-pill {{ $isActive ? 'on' : '' }}">
                  <div class="track-knob"></div>
                </div>
              </label>
            </div>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>
@endif

<div class="prd-table-wrap">
  <table class="data-table">
    <thead>
      <tr>
        <th>Nama Periode</th>
        <th>Jenjang</th>
        <th>Tahun Ajaran</th>
        <th>Mulai</th>
        <th>Selesai</th>
        <th>Kuota / Sisa</th>
        <th>Pendaftar</th>
        <th>Status</th>
        <th style="min-width:110px;">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($periods as $period)
        @php
          $computed = $period->computedStatus();
          $badgeClass = \App\Models\RegistrationPeriod::statusBadgeClass($computed);
          $label = \App\Models\RegistrationPeriod::statusLabel($computed);
          $count = $period->registrations_count ?? 0;
          $hasQuota = $period->max_applicants !== null;
          $sisa = $hasQuota ? $period->remainingQuota() : null;
          $kuotaCls = '';
          if ($hasQuota) {
            if ($sisa === 0) $kuotaCls = 'kuota-full';
            elseif ($sisa !== null && $period->max_applicants > 0 && $sisa / $period->max_applicants < 0.3) $kuotaCls = 'kuota-warn';
            else $kuotaCls = 'kuota-ok';
          }
        @endphp
        <tr>
          <td>
            <div style="font-weight:600;color:var(--tx1);">{{ $period->name }}</div>
            @if ($period->wave)
              <div style="font-size:11px;color:var(--tx3);">Gelombang {{ $period->wave }}</div>
            @endif
            @if ($period->description)
              <div style="font-size:11px;color:var(--tx4);max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $period->description }}">{{ $period->description }}</div>
            @endif
          </td>
          <td><span style="font-size:13px;color:var(--tx1);font-weight:500;">{{ $period->schoolLevel->name ?? '-' }}</span></td>
          <td style="font-size:13px;color:var(--tx2);">{{ $period->academic_year ?? '-' }}</td>
          <td style="font-size:13px;white-space:nowrap;">{{ $period->start_date?->format('d M Y') ?? '-' }}</td>
          <td style="font-size:13px;white-space:nowrap;">{{ $period->end_date?->format('d M Y') ?? '-' }}</td>
          <td>
            @if ($hasQuota)
              <span style="font-size:13px;font-weight:600;" class="{{ $kuotaCls }}">{{ $period->max_applicants }} / sisa {{ $sisa }}</span>
            @else
              <span style="font-size:12px;color:var(--tx3);">Tak terbatas</span>
            @endif
          </td>
          <td style="font-size:13px;font-weight:600;color:var(--tx1);">{{ $count }}</td>
          <td>
            <span class="status-badge {{ $badgeClass }}" style="border-radius:9999px;padding:3px 10px;font-size:11px;font-weight:600;white-space:nowrap;">{{ $label }}</span>
          </td>
          <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
              <a href="{{ route('admin.periods.edit', $period) }}" class="btn btn-outline" style="padding:5px 10px;font-size:12px;"><i class="fa-solid fa-pen" style="font-size:10px;"></i> Edit</a>
              <button type="button" class="btn btn-outline" style="padding:5px 10px;font-size:12px;color:var(--danger);border-color:rgba(220,38,38,0.25);" onclick="openPeriodDelete({{ $period->id }}, {{ json_encode($period->name) }}, {{ $count }})">
                <i class="fa-solid fa-trash-can" style="font-size:10px;"></i> Hapus
              </button>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="9" class="empty-state">
            <div style="padding:28px 16px;text-align:center;">
              <div style="font-size:13px;color:var(--tx2);">Belum ada periode pendaftaran</div>
              <div style="font-size:12px;color:var(--tx4);margin-top:4px;">Tambahkan periode pertama untuk membuka pendaftaran.</div>
            </div>
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

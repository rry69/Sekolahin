@if ($periods->isEmpty())
  <div class="prd-empty"><i class="fa-regular fa-calendar-xmark"></i>Belum ada periode pendaftaran<div style="font-size:12px;margin-top:4px;color:var(--muted);">Tambahkan periode pertama untuk membuka pendaftaran.</div></div>
@else
  <div class="prd-list">
    @foreach ($periods as $period)
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
      <div class="prd-row">
        <span class="prd-ic"><i class="fa-solid fa-calendar-days"></i></span>
        <div class="prd-body">
          <div class="prd-name">
            {{ $period->name }}
            @if ($period->wave) <span style="font-size:11px;color:var(--muted);font-weight:500;">· Gelombang {{ $period->wave }}</span> @endif
            @if ($period->academic_year) <span style="font-size:11px;color:var(--muted);font-weight:500;">· {{ $period->academic_year }}</span> @endif
            <span class="prd-pill {{ $badgeClass }}" style="font-size:11px;padding:3px 10px;">{{ $label }}</span>
          </div>
          <div class="prd-sub">
            <span><i class="fa-solid fa-school" style="font-size:10px;margin-right:3px;"></i>{{ $period->schoolLevel->name ?? '-' }}</span>
            <span class="dot">·</span>
            <span><i class="fa-regular fa-calendar" style="font-size:10px;margin-right:3px;"></i>{{ $period->start_date?->format('d M Y') ?? '-' }} — {{ $period->end_date?->format('d M Y') ?? '-' }}</span>
            <span class="dot">·</span>
            @if ($hasQuota)
              <span class="{{ $kuotaCls }}">{{ $period->max_applicants }} kuota / sisa {{ $sisa }}</span>
            @else
              <span style="color:var(--muted);">Tak terbatas</span>
            @endif
            <span class="dot">·</span>
            <span><i class="fa-solid fa-users" style="font-size:10px;margin-right:3px;"></i>{{ $count }} pendaftar</span>
          </div>
          @if ($period->description)
            <div style="font-size:11.5px;color:var(--muted);margin-top:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:520px;" title="{{ $period->description }}">{{ $period->description }}</div>
          @endif
        </div>
        <div class="prd-actions">
          <a href="{{ route('admin.periods.edit', $period) }}" class="prd-btn ghost sm"><i class="fa-solid fa-pen" style="font-size:10px;"></i> Edit</a>
          <button type="button" class="prd-btn ghost sm" style="color:var(--red);" onclick="openPeriodDelete({{ $period->id }}, {{ json_encode($period->name) }}, {{ $count }})"><i class="fa-solid fa-trash-can" style="font-size:10px;"></i> Hapus</button>
        </div>
      </div>
    @endforeach
  </div>
@endif

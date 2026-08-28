@if ($majors->isEmpty())
  <div class="mjr-empty">
    <i class="fa-solid fa-folder-open"></i>
    Tidak ada jurusan yang cocok dengan filter.
  </div>
@else
  <div class="mjr-list">
    @foreach ($majors as $major)
      @php
        $jenjangName = $major->schoolLevel->name ?? $major->school->schoolLevels->first()?->name ?? '-';
        $isActive = $major->is_active;
      @endphp
      <div class="mjr-row">
        <span class="mjr-ic {{ $isActive ? 'active' : 'inactive' }}"><i class="fa-solid fa-graduation-cap"></i></span>
        <div class="mjr-body">
          <div class="mjr-name">
            <a href="{{ route('admin.majors.show', $major) }}">{{ $major->name }}</a>
            <span class="mjr-cap">· {{ $major->code }} · {{ $jenjangName }}@if($major->order !== null) · #{{ $major->order }}@endif</span>
            <span class="mjr-pill {{ $isActive ? 'green' : 'red' }}" style="padding:2px 9px;font-size:10.5px">{{ $major->statusLabel() }}</span>
          </div>
          <div class="mjr-sub">
            <i class="fa-solid fa-school" style="margin-right:3px;font-size:10px;"></i>{{ $major->school->name ?? '-' }}
          </div>
          <div class="mjr-stats">
            <span>Pendaftar <b>{{ $major->total_applicants }}</b></span>
            <span style="color:var(--divider)">·</span>
            <span>Pending <b style="color:#b45309">{{ $major->pending_count }}</b></span>
            <span style="color:var(--divider)">·</span>
            <span>Diterima <b style="color:var(--green)">{{ $major->accepted_count }}</b></span>
            <span style="color:var(--divider)">·</span>
            <span>Ditolak <b style="color:var(--red)">{{ $major->rejected_count }}</b></span>
          </div>
          @php
            $totalQuota = $major->trackQuotas->sum('quota') ?: $major->quota;
            $availableQuota = $major->available_quota;
          @endphp
          <div class="mjr-quotas-min">
            @foreach($tracks as $t)
              @php $q = $major->{"quota_{$t->id}"} ?? null; $s = $major->{"sisa_{$t->id}"} ?? null; @endphp
              @if($q !== null)
                <span>{{ $t->name }} {{ $q }} <span style="color:var(--muted)">sisa {{ $s }}</span></span>
                <span style="color:var(--divider)">·</span>
              @endif
            @endforeach
            <span>Total {{ $totalQuota }} <span style="color:var(--muted)">sisa {{ $availableQuota }}</span></span>
          </div>
        </div>
        <div class="mjr-actions">
          <a href="{{ route('admin.majors.show', $major) }}" class="mjr-btn ghost sm" title="Detail"><i class="fa-solid fa-eye" style="font-size:10px;"></i> Detail</a>
          <a href="{{ route('admin.majors.edit', $major) }}" class="mjr-btn amber sm" title="Edit"><i class="fa-solid fa-pen" style="font-size:10px;"></i> Edit</a>
          <form action="{{ route('admin.majors.toggle-status', $major) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="mjr-btn {{ $isActive ? 'ghost' : 'green' }} sm" title="{{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}">
              <i class="fa-solid fa-{{ $isActive ? 'toggle-on' : 'toggle-off' }}" style="font-size:11px;"></i> {{ $isActive ? 'Nonaktif' : 'Aktif' }}
            </button>
          </form>
          <button type="button" class="mjr-btn red sm" title="Hapus" onclick="openMajorDelete({{ $major->id }}, {{ json_encode($major->name) }})">
            <i class="fa-solid fa-trash-can" style="font-size:10px;"></i> Hapus
          </button>
        </div>
      </div>
    @endforeach
  </div>
@endif

<div class="mjr-footer">
  <span>Menampilkan {{ $majors->firstItem() ?? 0 }}–{{ $majors->lastItem() ?? 0 }} dari {{ $majors->total() }}</span>
  <div class="pager">
    {{ $majors->links('vendor.pagination.bringova') }}
  </div>
</div>

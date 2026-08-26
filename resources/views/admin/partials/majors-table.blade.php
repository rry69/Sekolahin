<div class="mjr-table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th>Status</th>
          <th>Jenjang</th>
          <th>Sekolah</th>
          <th>Kode</th>
          <th>Jurusan</th>
          <th>Pendaftar</th>
          <th>Pending</th>
          <th>Diterima</th>
          <th>Ditolak</th>
          @foreach($tracks as $t)<th>{{ $t->name }}</th>@endforeach
          <th>Total Kuota</th>
          <th>Total Sisa</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($majors as $major)
          <tr>
            <td>
              <span class="status-badge status-{{ $major->is_active ? 'active' : 'inactive' }}">{{ $major->statusLabel() }}</span>
            </td>
            <td>{{ $major->schoolLevel->name ?? $major->school->schoolLevels->first()?->name ?? '-' }}</td>
            <td>{{ $major->school->name ?? '-' }}</td>
            <td>{{ $major->code }}</td>
            <td>
              <a href="{{ route('admin.majors.show', $major) }}" style="font-weight:600;color:var(--accent);text-decoration:none;">{{ $major->name }}</a>
              @if ($major->order !== null)
                <span style="display:block;font-size:10px;color:var(--tx4);">Urutan {{ $major->order }}</span>
              @endif
            </td>
            <td>{{ $major->total_applicants }}</td>
            <td><span class="status-badge status-pending">{{ $major->pending_count }}</span></td>
            <td><span class="status-badge status-accepted">{{ $major->accepted_count }}</span></td>
            <td><span class="status-badge status-rejected">{{ $major->rejected_count }}</span></td>
            @foreach($tracks as $t)
              @php $q = $major->{"quota_{$t->id}"} ?? null; $s = $major->{"sisa_{$t->id}"} ?? null; @endphp
              <td>
                @if($q !== null)
                  <span style="font-size:12px;">{{ $q }} <span style="color:var(--tx4);">/ sisa {{ $s }}</span></span>
                @else <span style="color:var(--tx4);">-</span> @endif
              </td>
            @endforeach
            <td>{{ $major->trackQuotas->sum('quota') ?: $major->quota }}</td>
            <td>{{ $major->available_quota }}</td>
            <td>
              <div style="display:flex;gap:6px;flex-wrap:wrap;">
                <a href="{{ route('admin.majors.show', $major) }}" class="btn btn-outline" title="Detail"><i class="fa-solid fa-eye" style="font-size:10px;"></i></a>
                <a href="{{ route('admin.majors.edit', $major) }}" class="btn btn-outline" title="Edit"><i class="fa-solid fa-pen" style="font-size:10px;"></i></a>
                <form action="{{ route('admin.majors.toggle-status', $major) }}" method="POST" style="display:inline;">
                  @csrf
                  <button type="submit" class="btn {{ $major->is_active ? 'btn-outline' : 'btn-primary' }}" title="{{ $major->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                    <i class="fa-solid fa-{{ $major->is_active ? 'toggle-on' : 'toggle-off' }}" style="font-size:11px;"></i>
                  </button>
                </form>
                <button type="button" class="btn btn-danger" title="Hapus" onclick="openMajorDelete({{ $major->id }}, {{ json_encode($major->name) }})">
                  <i class="fa-solid fa-trash-can" style="font-size:10px;"></i>
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="{{ 9 + $tracks->count() + 3 }}" class="empty-state">
              <i class="fa-solid fa-folder-open" style="font-size:24px;color:var(--tx4);display:block;margin-bottom:8px;"></i>
              Tidak ada jurusan yang cocok dengan filter.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mjr-footer">
    <span style="font-size:12px;color:var(--tx3);">Menampilkan {{ $majors->firstItem() ?? 0 }}–{{ $majors->lastItem() ?? 0 }} dari {{ $majors->total() }}</span>
    <div class="pager">
      {{ $majors->links('vendor.pagination.egglore') }}
    </div>
  </div>

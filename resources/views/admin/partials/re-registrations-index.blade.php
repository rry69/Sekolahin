<div class="breadcrumb">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <span class="sep">/</span>
  <span>Daftar Ulang</span>
</div>
<h1 class="page-title">Daftar Ulang Pendaftar</h1>

@if (session('success'))
<div class="ajax-success alert alert-success">
  {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="ajax-success alert alert-error">
  {{ session('error') }}
</div>
@endif

<div class="doc-tabs">
  <a href="{{ route('admin.re-registrations.index') }}" class="doc-tab {{ !request('status') && !request('level') ? 'active' : '' }}">Semua</a>
  <a href="{{ route('admin.re-registrations.index', ['status' => 'pending']) }}" class="doc-tab {{ request('status') == 'pending' ? 'active' : '' }}">Pending</a>
  <a href="{{ route('admin.re-registrations.index', ['status' => 'completed']) }}" class="doc-tab {{ request('status') == 'completed' ? 'active' : '' }}">Selesai</a>
  <div class="doc-actions">
    <button class="doc-action-btn" onclick="toggleFilterForm()"><i class="fa-solid fa-filter" style="font-size:10px"></i> Filter</button>
  </div>
</div>

<form id="filterForm" method="GET" action="{{ route('admin.re-registrations.index') }}" style="display:none;margin-bottom:16px;">
  <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;background:var(--panel-2);padding:16px;border-radius:10px;">
    <div style="flex:1;min-width:220px;">
      <label style="display:block;font-size:11px;color:var(--tx3);margin-bottom:4px;">Cari (No. Registrasi / Nama)</label>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor registrasi atau nama siswa..."
             style="width:100%;padding:6px 12px;border:1px solid var(--input-border);border-radius:6px;font-size:13px;background:var(--input-bg);color:var(--tx-body);">
    </div>
    <div>
      <label style="display:block;font-size:11px;color:var(--tx3);margin-bottom:4px;">Jenjang</label>
      <select name="level" style="padding:6px 12px;border:1px solid var(--input-border);border-radius:6px;font-size:13px;background:var(--input-bg);color:var(--tx-body);">
        <option value="">Semua Jenjang</option>
        @foreach ($schoolLevels ?? [] as $level)
          <option value="{{ $level->id }}" {{ request('level') == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Cari</button>
  </div>
</form>

@if ($reRegistrations->isEmpty())
  <div class="empty-state">Tidak ada data daftar ulang</div>
@else
  <table class="data-table">
    <thead>
      <tr>
        <th>No. Registrasi</th>
        <th>Nama Siswa</th>
        <th>Tanggal Submit</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($reRegistrations as $reReg)
      <tr>
        <td style="font-weight:500;">{{ $reReg->registration->registration_number }}</td>
        <td>
          <div style="font-weight:500;">{{ $reReg->registration->applicant->full_name }}</div>
          <div style="font-size:11px;color:var(--tx4);">{{ $reReg->registration->applicant->user->email }}</div>
        </td>
        <td style="font-size:12px;color:var(--tx2);">
          {{ $reReg->submitted_at ? $reReg->submitted_at->format('d M Y H:i') : '-' }}
        </td>
        <td>
          @php
            $statusMap = [
              'pending' => 'status-pending',
              'completed' => 'status-accepted',
              'rejected' => 'status-rejected',
            ];
            $statusLabels = ['pending' => 'Pending', 'completed' => 'Selesai', 'rejected' => 'Ditolak'];
          @endphp
          <span class="status-badge {{ $statusMap[$reReg->status] ?? 'status-pending' }}">{{ $statusLabels[$reReg->status] ?? ucfirst($reReg->status) }}</span>
        </td>
        <td>
          <div style="display:flex;gap:6px;">
            <a href="{{ route('admin.re-registrations.show', $reReg) }}" class="btn btn-outline" style="padding:4px 10px;font-size:11px;text-decoration:none;">Detail</a>
            @if ($reReg->status === 'pending')
              <form action="{{ route('admin.re-registrations.verify', $reReg) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-primary" style="padding:4px 10px;font-size:11px;">Verifikasi</button>
              </form>
              <button type="button" onclick="showReRegRejectModal({{ $reReg->id }})" class="btn btn-danger" style="padding:4px 10px;font-size:11px;">Tolak</button>
            @endif
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div style="margin-top:16px;">
    {{ $reRegistrations->appends(request()->query())->links() }}
  </div>
@endif

<div id="reRegRejectModal" class="modal-overlay" style="display:none;">
  <div class="modal-card">
    <div class="modal-head">
      <div style="flex:1;">
        <h3 class="modal-title">Tolak Daftar Ulang</h3>
      </div>
    </div>
    <form id="reRegRejectForm" method="POST">
      @csrf
      <div style="margin-bottom:16px;">
        <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;">Catatan / Alasan Penolakan</label>
        <textarea name="notes" rows="4" style="width:100%;padding:8px 12px;border:1px solid var(--input-border);border-radius:6px;font-size:13px;font-family:inherit;background:var(--input-bg);color:var(--tx-body);" required></textarea>
      </div>
      <div style="display:flex;gap:8px;justify-content:flex-end;">
        <button type="button" onclick="hideReRegRejectModal()" class="btn btn-outline">Batal</button>
        <button type="submit" class="btn btn-danger">Tolak</button>
      </div>
    </form>
  </div>
</div>

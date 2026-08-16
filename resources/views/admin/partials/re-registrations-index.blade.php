<div class="breadcrumb">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <span class="sep">/</span>
  <span>Daftar Ulang</span>
</div>
<h1 class="page-title">Daftar Ulang Pendaftar</h1>

@if (session('success'))
<div class="ajax-success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
  {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="ajax-success" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
  {{ session('error') }}
</div>
@endif

<div class="doc-tabs">
  <a href="{{ route('admin.re-registrations.index') }}" class="doc-tab {{ !request('status') ? 'active' : '' }}">Semua</a>
  <a href="{{ route('admin.re-registrations.index', ['status' => 'pending']) }}" class="doc-tab {{ request('status') == 'pending' ? 'active' : '' }}">Pending</a>
  <a href="{{ route('admin.re-registrations.index', ['status' => 'completed']) }}" class="doc-tab {{ request('status') == 'completed' ? 'active' : '' }}">Selesai</a>
</div>

@if ($reRegistrations->isEmpty())
  <div class="empty-state">Tidak ada data daftar ulang</div>
@else
  <table class="data-table">
    <thead>
      <tr>
        <th>No. Registrasi</th>
        <th>Nama Siswa</th>
        <th>Uk. Seragam</th>
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
          <div style="font-size:11px;color:#aaa;">{{ $reReg->registration->applicant->user->email }}</div>
        </td>
        <td>{{ trim(($reReg->uniform_shirt_size ?? '-') . ' / ' . ($reReg->uniform_pants_size ?? '-'), ' /') ?: '-' }}</td>
        <td style="font-size:12px;color:#666;">
          {{ $reReg->submitted_at ? $reReg->submitted_at->format('d M Y H:i') : '-' }}
        </td>
        <td>
          @php
            $statusMap = [
              'pending' => 'status-pending',
              'completed' => 'status-accepted',
            ];
          @endphp
          <span class="status-badge {{ $statusMap[$reReg->status] ?? 'status-pending' }}">{{ ucfirst($reReg->status) }}</span>
        </td>
        <td>
          <div style="display:flex;gap:6px;">
            <a href="{{ route('admin.re-registrations.show', $reReg) }}" class="btn btn-outline" style="padding:4px 10px;font-size:11px;text-decoration:none;">Detail</a>
            @if ($reReg->status === 'pending')
              <form action="{{ route('admin.re-registrations.verify', $reReg) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-primary" style="padding:4px 10px;font-size:11px;">Verifikasi</button>
              </form>
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

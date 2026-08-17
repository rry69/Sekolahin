<div class="breadcrumb">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <span class="sep">/</span>
  <span>Pendaftaran</span>
</div>
<h1 class="page-title">Daftar Pendaftaran</h1>

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
  <a href="{{ route('admin.registrations.index') }}" class="doc-tab {{ !request('status') && !request('payment_status') ? 'active' : '' }}">Semua</a>
  <a href="{{ route('admin.registrations.index', ['status' => 'pending']) }}" class="doc-tab {{ request('status') == 'pending' ? 'active' : '' }}">Pending</a>
  <a href="{{ route('admin.registrations.index', ['status' => 'verified']) }}" class="doc-tab {{ request('status') == 'verified' ? 'active' : '' }}">Terverifikasi</a>
  <a href="{{ route('admin.registrations.index', ['status' => 'accepted']) }}" class="doc-tab {{ request('status') == 'accepted' ? 'active' : '' }}">Diterima</a>
  <a href="{{ route('admin.registrations.index', ['status' => 'rejected']) }}" class="doc-tab {{ request('status') == 'rejected' ? 'active' : '' }}">Ditolak</a>
  <div class="doc-actions">
    <button class="doc-action-btn" onclick="toggleFilterForm()"><i class="fa-solid fa-filter" style="font-size:10px"></i> Filter</button>
  </div>
</div>

<form id="filterForm" method="GET" action="{{ route('admin.registrations.index') }}" style="display:none;margin-bottom:16px;">
  <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;background:var(--panel-2);padding:16px;border-radius:10px;">
    <div>
      <label style="display:block;font-size:11px;color:var(--tx3);margin-bottom:4px;">Status</label>
      <select name="status" style="padding:6px 12px;border:1px solid var(--input-border);border-radius:6px;font-size:13px;background:var(--input-bg);color:var(--tx-body);">
        <option value="">Semua Status</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Diterima</option>
      </select>
    </div>
    <div>
      <label style="display:block;font-size:11px;color:var(--tx3);margin-bottom:4px;">Pembayaran</label>
      <select name="payment_status" style="padding:6px 12px;border:1px solid var(--input-border);border-radius:6px;font-size:13px;background:var(--input-bg);color:var(--tx-body);">
        <option value="">Semua Status</option>
        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
        <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Gagal</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Filter</button>
  </div>
</form>

@if ($registrations->isEmpty())
  <div class="empty-state">Tidak ada pendaftaran</div>
@else
  <table class="data-table">
    <thead>
      <tr>
        <th>No. Pendaftaran</th>
        <th>Nama</th>
        <th>Verif. NISN</th>
        <th>Waktu Daftar</th>
        <th>Jalur</th>
        <th>Jurusan Pilihan</th>
        <th>Status</th>
        <th>Pembayaran</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($registrations as $reg)
      <tr>
        <td style="font-weight:500;">{{ $reg->registration_number }}</td>
        <td>
          <div style="font-weight:500;">{{ $reg->applicant->full_name ?? '-' }}</div>
          <div style="font-size:11px;color:var(--tx4);">{{ $reg->applicant->user->email ?? '-' }}</div>
        </td>
        <td>
            @php
                $vstatus = $reg->applicant->nisn_verification_status ?? null;
                $vbadge = ['verified' => 'status-accepted', 'unavailable' => 'status-pending', 'failed' => 'status-rejected'];
            @endphp
            <span class="status-badge {{ $vbadge[$vstatus] ?? 'status-pending' }}">
                {{ \App\Services\NisnVerificationService::statusLabel($vstatus ?? '') }}
            </span>
        </td>
        <td style="font-size:12px;color:var(--tx2);">{{ $reg->created_at->format('d M Y H:i') }}</td>
        <td>{{ $reg->registrationTrack->name ?? '-' }}</td>
        <td>{{ $reg->major->name ?? '-' }}</td>
        <td><span class="status-badge status-{{ $reg->status }}">{{ ucfirst(str_replace('_', ' ', $reg->status)) }}</span></td>
        <td>
          @php
            $pm = ['unpaid'=>'pending','pending'=>'pending','paid'=>'accepted','failed'=>'rejected'];
          @endphp
          <span class="status-badge status-{{ $pm[$reg->payment_status] ?? 'pending' }}">{{ ucfirst($reg->payment_status) }}</span>
        </td>
        <td>
          <div style="display:flex;gap:6px;flex-wrap:wrap">
            <a href="{{ route('admin.registrations.show', $reg) }}" class="btn btn-outline" style="padding:4px 10px;font-size:11px;text-decoration:none;">Detail</a>
            <button type="button" onclick="openResetModal({{ $reg->id }}, '{{ addslashes($reg->registration_number) }}', '{{ addslashes($reg->applicant->full_name ?? '-') }}')" class="btn" style="padding:4px 10px;font-size:11px;background:var(--warning);color:var(--accent-fg);border:none;">Reset</button>
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div style="margin-top:16px;">
    {{ $registrations->appends(request()->query())->links() }}
  </div>

@endif

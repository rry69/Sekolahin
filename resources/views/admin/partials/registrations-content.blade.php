<div class="breadcrumb">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <span class="sep">/</span>
  <span>Pendaftaran</span>
</div>
<h1 class="page-title">Kelola Pendaftaran</h1>

@if (session('success'))
<div class="ajax-success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
  {{ session('success') }}
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
  <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;background:#f8f9fb;padding:16px;border-radius:10px;">
    <div>
      <label style="display:block;font-size:11px;color:#999;margin-bottom:4px;">Status Pendaftaran</label>
      <select name="status" style="padding:6px 12px;border:1px solid #e0e0e0;border-radius:6px;font-size:13px;">
        <option value="">Semua Status</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Diterima</option>
        <option value="re_registration_complete" {{ request('status') == 're_registration_complete' ? 'selected' : '' }}>Daftar Ulang Selesai</option>
      </select>
    </div>
    <div>
      <label style="display:block;font-size:11px;color:#999;margin-bottom:4px;">Status Pembayaran</label>
      <select name="payment_status" style="padding:6px 12px;border:1px solid #e0e0e0;border-radius:6px;font-size:13px;">
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
        <th>Jenjang</th>
        <th>Jalur</th>
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
          <div style="font-size:11px;color:#aaa;">{{ $reg->applicant->user->email ?? '-' }}</div>
        </td>
        <td>{{ $reg->registrationPeriod->schoolLevel->name ?? '-' }}</td>
        <td>{{ $reg->registrationTrack->name ?? '-' }}</td>
        <td><span class="status-badge status-{{ $reg->status }}">{{ ucfirst(str_replace('_', ' ', $reg->status)) }}</span></td>
        <td>
          @php
            $pm = ['unpaid'=>'pending','pending'=>'pending','paid'=>'accepted','failed'=>'rejected'];
          @endphp
          <span class="status-badge status-{{ $pm[$reg->payment_status] ?? 'pending' }}">{{ ucfirst($reg->payment_status) }}</span>
        </td>
        <td>
          <button onclick="openStatusModal({{ $reg->id }}, '{{ $reg->status }}', '{{ addslashes($reg->notes) }}')" class="btn btn-outline" style="padding:4px 10px;font-size:11px;">Status</button>
          <button onclick="openPaymentModal({{ $reg->id }}, '{{ $reg->payment_status }}', {{ $reg->payment_amount ?? 0 }})" class="btn btn-outline" style="padding:4px 10px;font-size:11px;">Bayar</button>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div style="margin-top:16px;">
    {{ $registrations->appends(request()->query())->links() }}
  </div>
@endif

<!-- Status Modal -->
<div id="statusModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:50;align-items:flex-start;justify-content:center;padding-top:80px;">
  <div style="background:#fff;border-radius:10px;padding:24px;width:400px;max-width:90%;box-shadow:0 4px 20px rgba(0,0,0,0.15);">
    <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">Update Status Pendaftaran</h3>
    <form id="statusForm" method="POST">
      @csrf
      @method('PATCH')
      <div style="margin-bottom:12px;">
        <label style="display:block;font-size:11px;color:#999;margin-bottom:4px;">Status</label>
        <select name="status" id="statusSelect" required style="width:100%;padding:8px 12px;border:1px solid #e0e0e0;border-radius:6px;font-size:13px;">
          <option value="pending">Pending</option>
          <option value="verified">Terverifikasi</option>
          <option value="rejected">Ditolak</option>
          <option value="accepted">Diterima</option>
          <option value="re_registration_complete">Daftar Ulang Selesai</option>
        </select>
      </div>
      <div style="margin-bottom:16px;">
        <label style="display:block;font-size:11px;color:#999;margin-bottom:4px;">Catatan</label>
        <textarea name="notes" id="notesInput" rows="3" style="width:100%;padding:8px 12px;border:1px solid #e0e0e0;border-radius:6px;font-size:13px;"></textarea>
      </div>
      <div style="display:flex;justify-content:flex-end;gap:8px;">
        <button type="button" onclick="closeStatusModal()" class="btn btn-outline">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:50;align-items:flex-start;justify-content:center;padding-top:80px;">
  <div style="background:#fff;border-radius:10px;padding:24px;width:400px;max-width:90%;box-shadow:0 4px 20px rgba(0,0,0,0.15);">
    <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">Update Status Pembayaran</h3>
    <form id="paymentForm" method="POST">
      @csrf
      @method('PATCH')
      <div style="margin-bottom:12px;">
        <label style="display:block;font-size:11px;color:#999;margin-bottom:4px;">Status Pembayaran</label>
        <select name="payment_status" id="paymentStatusSelect" required style="width:100%;padding:8px 12px;border:1px solid #e0e0e0;border-radius:6px;font-size:13px;">
          <option value="unpaid">Belum Dibayar</option>
          <option value="pending">Menunggu Konfirmasi</option>
          <option value="paid">Lunas</option>
          <option value="failed">Gagal</option>
        </select>
      </div>
      <div style="margin-bottom:16px;">
        <label style="display:block;font-size:11px;color:#999;margin-bottom:4px;">Jumlah (Rp)</label>
        <input type="number" name="payment_amount" id="paymentAmountInput" step="0.01" style="width:100%;padding:8px 12px;border:1px solid #e0e0e0;border-radius:6px;font-size:13px;">
      </div>
      <div style="display:flex;justify-content:flex-end;gap:8px;">
        <button type="button" onclick="closePaymentModal()" class="btn btn-outline">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>

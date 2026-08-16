<div class="breadcrumb">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <span class="sep">/</span>
  <span>Pembayaran</span>
</div>
<h1 class="page-title">Daftar Pembayaran</h1>

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
  <a href="{{ route('admin.payments.index') }}" class="doc-tab {{ !request('status') ? 'active' : '' }}">Semua</a>
  <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="doc-tab {{ request('status') == 'pending' ? 'active' : '' }}">Pending</a>
  <a href="{{ route('admin.payments.index', ['status' => 'verified']) }}" class="doc-tab {{ request('status') == 'verified' ? 'active' : '' }}">Terverifikasi</a>
  <a href="{{ route('admin.payments.index', ['status' => 'rejected']) }}" class="doc-tab {{ request('status') == 'rejected' ? 'active' : '' }}">Ditolak</a>
</div>

@if ($payments->isEmpty())
  <div class="empty-state">Tidak ada data pembayaran</div>
@else
  <table class="data-table">
    <thead>
      <tr>
        <th>No. Registrasi</th>
        <th>Pendaftar</th>
        <th>Tipe</th>
        <th>Jumlah</th>
        <th>Metode</th>
        <th>Status</th>
        <th>Bukti</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($payments as $payment)
      <tr>
        <td style="font-weight:500;">{{ $payment->registration->registration_number }}</td>
        <td>
          <div style="font-weight:500;">{{ $payment->registration->applicant->full_name }}</div>
          <div style="font-size:11px;color:#aaa;">{{ $payment->registration->applicant->user->email }}</div>
        </td>
        <td>
          @if ($payment->payment_type === 'registration_fee')
            Biaya Pendaftaran
          @else
            Biaya Daftar Ulang
          @endif
        </td>
        <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
        <td style="text-transform:capitalize;">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
        <td>
          @php
            $statusMap = [
              'pending' => 'status-pending',
              'verified' => 'status-accepted',
              'rejected' => 'status-rejected',
            ];
            $statusLabels = ['pending' => 'Pending', 'verified' => 'Lunas', 'rejected' => 'Ditolak'];
          @endphp
          <span class="status-badge {{ $statusMap[$payment->status] ?? 'status-pending' }}">{{ $statusLabels[$payment->status] ?? ucfirst($payment->status) }}</span>
        </td>
        <td>
          @if ($payment->proof_file)
            <button type="button" onclick="showFileModal('{{ asset('storage/' . $payment->proof_file) }}', 'Bukti Pembayaran · {{ $payment->registration->applicant->full_name }}')" class="btn btn-outline" style="padding:4px 10px;font-size:11px;">
              Lihat Bukti
            </button>
          @else
            <span style="color:#aaa;">-</span>
          @endif
        </td>
        <td>
          @if ($payment->status === 'pending')
            <div style="display:flex;gap:6px;">
              <form action="{{ route('admin.payments.verify', $payment) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-primary" style="padding:4px 10px;font-size:11px;">Verifikasi</button>
              </form>
              <button onclick="showRejectModal({{ $payment->id }})" class="btn btn-danger" style="padding:4px 10px;font-size:11px;">Tolak</button>
            </div>
          @else
            <form action="{{ route('admin.payments.reset', $payment) }}" method="POST" style="display:inline;"
                  onsubmit="return confirm('Kembalikan status pembayaran ini ke pending?')">
              @csrf
              <button type="submit" class="btn btn-outline" style="padding:4px 10px;font-size:11px;">Reset</button>
            </form>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div style="margin-top:16px;">
    {{ $payments->appends(request()->query())->links() }}
  </div>
@endif

<div id="rejectModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.4);z-index:1000;align-items:center;justify-content:center;">
  <div style="background:#fff;padding:24px;border-radius:10px;width:400px;max-width:90%;">
    <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">Tolak Pembayaran</h3>
    <form id="rejectForm" method="POST">
      @csrf
      <div style="margin-bottom:16px;">
        <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;">Alasan Penolakan</label>
        <textarea name="rejection_reason" rows="4" style="width:100%;padding:8px 12px;border:1px solid #e0e0e0;border-radius:6px;font-size:13px;font-family:inherit;" required></textarea>
      </div>
      <div style="display:flex;gap:8px;justify-content:flex-end;">
        <button type="button" onclick="hideRejectModal()" class="btn btn-outline">Batal</button>
        <button type="submit" class="btn btn-danger">Tolak</button>
      </div>
    </form>
  </div>
</div>

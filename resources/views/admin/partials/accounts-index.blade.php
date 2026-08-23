<div class="breadcrumb">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <span class="sep">/</span>
  <span>Akun Siswa</span>
</div>
<h1 class="page-title">Daftar Akun Siswa</h1>

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
  <a href="{{ route('admin.accounts.index') }}" class="doc-tab {{ !request('registration_status') && !request('major_id') ? 'active' : '' }}">Semua</a>
  <a href="{{ route('admin.accounts.index', ['registration_status' => 'pending']) }}" class="doc-tab {{ request('registration_status') == 'pending' ? 'active' : '' }}">Pending</a>
  <a href="{{ route('admin.accounts.index', ['registration_status' => 'verified']) }}" class="doc-tab {{ request('registration_status') == 'verified' ? 'active' : '' }}">Terverifikasi</a>
  <a href="{{ route('admin.accounts.index', ['registration_status' => 'accepted']) }}" class="doc-tab {{ request('registration_status') == 'accepted' ? 'active' : '' }}">Diterima</a>
  <a href="{{ route('admin.accounts.index', ['registration_status' => 'rejected']) }}" class="doc-tab {{ request('registration_status') == 'rejected' ? 'active' : '' }}">Ditolak</a>
  <div class="doc-actions">
    <button class="doc-action-btn" onclick="toggleFilterForm()"><i class="fa-solid fa-filter" style="font-size:10px"></i> Filter</button>
  </div>
</div>

<form id="filterForm" method="GET" action="{{ route('admin.accounts.index') }}" style="display:none;margin-bottom:16px;">
  <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;background:var(--panel-2);padding:16px;border-radius:10px;">
    <div>
      <label style="display:block;font-size:11px;color:var(--tx3);margin-bottom:4px;">Cari</label>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / Email / NIK / NISN" style="padding:6px 12px;border:1px solid var(--input-border);border-radius:6px;font-size:13px;width:200px;background:var(--input-bg);color:var(--tx-body);">
    </div>
    <div>
      <label style="display:block;font-size:11px;color:var(--tx3);margin-bottom:4px;">Status Pendaftaran</label>
      <select name="registration_status" style="padding:6px 12px;border:1px solid var(--input-border);border-radius:6px;font-size:13px;background:var(--input-bg);color:var(--tx-body);">
        <option value="">Semua Status</option>
        <option value="pending" {{ request('registration_status') == 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="verified" {{ request('registration_status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
        <option value="accepted" {{ request('registration_status') == 'accepted' ? 'selected' : '' }}>Diterima</option>
        <option value="rejected" {{ request('registration_status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
      </select>
    </div>
    <div>
      <label style="display:block;font-size:11px;color:var(--tx3);margin-bottom:4px;">Jurusan</label>
      <select name="major_id" style="padding:6px 12px;border:1px solid var(--input-border);border-radius:6px;font-size:13px;background:var(--input-bg);color:var(--tx-body);">
        <option value="">Semua Jurusan</option>
        @foreach ($majors as $major)
          <option value="{{ $major->id }}" {{ request('major_id') == $major->id ? 'selected' : '' }}>{{ $major->name }}</option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline">Reset</a>
  </div>
</form>

@if ($accounts->isEmpty())
  <div class="empty-state">Tidak ada akun siswa</div>
@else
  <table class="data-table">
    <thead>
      <tr>
        <th>Nama</th>
        <th>Email</th>
        <th>NIK / NISN</th>
        <th>Jml Pendaftaran</th>
        <th>Terdaftar</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($accounts as $account)
      <tr>
        <td style="font-weight:500;">{{ $account->applicant->full_name ?? $account->name }}</td>
        <td>{{ $account->email }}</td>
        <td>
          <div>{{ $account->applicant->nik ?? '-' }}</div>
          <div style="font-size:11px;color:var(--tx4);">NISN: {{ $account->applicant->nisn ?? '-' }}</div>
        </td>
        <td>
          <span class="status-badge status-accepted">{{ $account->applicant->registrations_count ?? 0 }}</span>
        </td>
        <td style="font-size:12px;color:var(--tx2);">{{ $account->created_at->format('d M Y') }}</td>
        <td>
          @php $hasAccepted = $account->applicant?->registrations?->contains(fn($r) => $r->isAccepted()) ?? false; @endphp
          @if (! $hasAccepted)
          <form action="{{ route('admin.accounts.destroy', $account) }}" method="POST"
                onsubmit="return confirm('Hapus akun siswa {{ $account->applicant->full_name ?? $account->name }}? Seluruh data pendaftaran dan pembayarannya akan ikut terhapus permanen.')" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" style="padding:4px 10px;font-size:11px;">Hapus Akun</button>
          </form>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div style="margin-top:16px;">
    {{ $accounts->appends(request()->query())->links() }}
  </div>
@endif

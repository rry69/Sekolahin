<div class="breadcrumb">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <span class="sep">/</span>
  <span>Rekap Siswa Diterima</span>
</div>
<h1 class="page-title">Rekap Siswa Diterima</h1>

@if (session('success'))
<div class="ajax-success alert alert-success">
  {{ session('success') }}
</div>
@endif

<div class="summary-cards">
  <div class="summary-card">
    <div class="label"><i class="fa-solid fa-check"></i> Total Siswa Diterima</div>
    <div class="value">{{ $registrations->total() }}</div>
  </div>
</div>

<div class="doc-tabs">
  <a href="{{ route('admin.rekap.index') }}" class="doc-tab {{ !request('major_id') && !request('period_id') ? 'active' : '' }}">Semua</a>
  @foreach ($majors as $major)
    <a href="{{ route('admin.rekap.index', ['major_id' => $major->id] + request()->only(['period_id'])) }}" class="doc-tab {{ request('major_id') == $major->id ? 'active' : '' }}">
      {{ $major->name }} ({{ $statsPerMajor[$major->id] ?? 0 }})
    </a>
  @endforeach
  <div class="doc-actions">
    <button class="doc-action-btn" onclick="toggleFilterForm()"><i class="fa-solid fa-filter" style="font-size:10px"></i> Filter</button>
    <a href="{{ route('admin.rekap.export.xlsx', request()->only(['major_id','period_id','search'])) }}" class="doc-action-btn" title="Export Excel"><i class="fa-solid fa-file-excel" style="font-size:10px"></i> Export Excel</a>
    <a href="{{ route('admin.rekap.export.pdf', request()->only(['major_id','period_id','search'])) }}" class="doc-action-btn" title="Export PDF"><i class="fa-solid fa-file-pdf" style="font-size:10px"></i> Export PDF</a>
  </div>
</div>

<form id="filterForm" method="GET" action="{{ route('admin.rekap.index') }}" style="display:none;margin-bottom:16px;">
  <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;background:var(--panel-2);padding:16px;border-radius:10px;">
    <div>
      <label style="display:block;font-size:11px;color:var(--tx3);margin-bottom:4px;">Periode</label>
      <select name="period_id" style="padding:6px 12px;border:1px solid var(--input-border);border-radius:6px;font-size:13px;background:var(--input-bg);color:var(--tx-body);">
        <option value="">Semua Periode</option>
        @foreach ($periods as $period)
          <option value="{{ $period->id }}" {{ request('period_id') == $period->id ? 'selected' : '' }}>{{ $period->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label style="display:block;font-size:11px;color:var(--tx3);margin-bottom:4px;">Cari</label>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NIS / NISN / No. Reg" style="padding:6px 12px;border:1px solid var(--input-border);border-radius:6px;font-size:13px;background:var(--input-bg);color:var(--tx-body);">
    </div>
    <button type="submit" class="btn btn-primary">Filter</button>
  </div>
</form>

@if ($registrations->isEmpty())
  <div class="empty-state">Belum ada siswa yang diterima</div>
@else
  <table class="data-table">
    <thead>
      <tr>
        <th>No. Registrasi</th>
        <th>NIS</th>
        <th>Nama</th>
        <th>Jurusan Diterima</th>
        <th>Periode</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($registrations as $reg)
      <tr>
        <td style="font-weight:500;">{{ $reg->registration_number }}</td>
        <td>{{ $reg->applicant->student_number ?? '-' }}</td>
        <td>
          <div style="font-weight:500;">{{ $reg->applicant->full_name ?? '-' }}</div>
          <div style="font-size:11px;color:var(--tx4);">{{ $reg->applicant->user->email ?? '-' }}</div>
        </td>
        <td>{{ $reg->finalMajor->name ?? '-' }}</td>
        <td style="font-size:12px;color:var(--tx2);">{{ $reg->registrationPeriod->name ?? '-' }}</td>
        <td><span class="status-badge status-accepted">{{ ucfirst(str_replace('_', ' ', $reg->status)) }}</span></td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div style="margin-top:16px;">
    {{ $registrations->appends(request()->query())->links() }}
  </div>
@endif

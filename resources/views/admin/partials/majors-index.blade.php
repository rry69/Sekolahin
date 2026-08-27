<style>
  .mjr-card { background: var(--panel); border: 1px solid var(--border); border-radius: 12px; }
  .mjr-toolbar { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; padding: 16px 18px; border-bottom: 1px solid var(--hairline); }
  .mjr-toolbar .grow { flex: 1; min-width: 200px; }
  .mjr-field { display: flex; flex-direction: column; gap: 5px; }
  .mjr-field label { font-size: 11px; font-weight: 600; color: var(--tx3); text-transform: uppercase; letter-spacing: .4px; }
  .mjr-input { width: 100%; padding: 8px 12px; border: 1px solid var(--input-border); border-radius: 8px; font-size: 13px; background: var(--input-bg); color: var(--tx-body); box-sizing: border-box; }
  .mjr-input:focus { outline: none; border-color: var(--accent); }
  .mjr-table-wrap { overflow-x: auto; }
  .status-active { background: var(--badge-accepted-bg); color: var(--badge-accepted-fg); }
  .status-inactive { background: var(--badge-rejected-bg); color: var(--badge-rejected-fg); }
  .mjr-skeleton td { height: 40px; }
  .skel { display: block; height: 10px; border-radius: 9999px; background: var(--panel-2); }
  .mjr-summary { padding: 12px 18px; border-bottom: 1px solid var(--hairline); font-size: 12px; color: var(--tx2); display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; }
  .mjr-footer { padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; border-top: 1px solid var(--hairline); }
  @media (max-width: 640px) { .mjr-toolbar { padding: 12px; } }
</style>

<div class="mjr-card" id="majorsCard">
  <div class="mjr-toolbar">
    <div class="mjr-field grow">
      <label for="mjrSearch"><i class="fa-solid fa-magnifying-glass" style="font-size:10px;"></i> Cari Jurusan</label>
      <input type="text" id="mjrSearch" class="mjr-input" placeholder="Cari nama jurusan atau kode..." value="{{ request('q') }}" autocomplete="off">
    </div>
    <div class="mjr-field">
      <label for="mjrLevel">Jenjang</label>
      <select id="mjrLevel" class="mjr-input">
        <option value="">Semua Jenjang</option>
        @foreach ($levels as $l)
          <option value="{{ $l->id }}" {{ request('level') == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="mjr-field">
      <label for="mjrSchool">Sekolah</label>
      <select id="mjrSchool" class="mjr-input">
        <option value="">Semua Sekolah</option>
        @foreach ($schools as $s)
          <option value="{{ $s->id }}" {{ request('school_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
        @endforeach
      </select>
    </div>
    <a href="{{ route('admin.majors.create') }}" class="btn btn-primary" style="white-space:nowrap;">
      <i class="fa-solid fa-plus" style="font-size:10px;"></i> Tambah Jurusan
    </a>
  </div>

  <div class="mjr-summary">
    <span id="mjrTotal"><i class="fa-solid fa-layer-group" style="font-size:11px;"></i> Total <strong>{{ $majors->total() }}</strong> jurusan</span>
    @if (request()->has('q') || request()->has('school_id') || request()->has('level'))
      <a href="{{ route('admin.majors.index') }}" class="btn btn-outline" style="padding:3px 10px;font-size:11px;"><i class="fa-solid fa-xmark" style="font-size:9px;"></i> Reset filter</a>
    @endif
  </div>

  <div id="mjrBody">
    @include('admin.partials.majors-table')
  </div>
</div>

{{-- Modal konfirmasi hapus --}}
<div id="majorDeleteModal" class="modal-overlay" style="display:none;">
  <div class="modal-card">
    <div class="modal-head">
      <div class="modal-icon modal-icon-amber">🗑️</div>
      <div>
        <h3 class="modal-title">Hapus jurusan?</h3>
        <p class="modal-text">Yakin ingin menghapus jurusan <strong id="majorDeleteName"></strong>? Aksi ini tidak dapat dibatalkan.</p>
        <p class="modal-sub">Jurusan yang masih memiliki pendaftar tidak dapat dihapus — nonaktifkan saja.</p>
      </div>
    </div>
    <div class="modal-actions">
      <button type="button" onclick="closeMajorDelete()" class="modal-btn-cancel">Batal</button>
      <form id="majorDeleteForm" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" id="majorDeleteConfirm">Ya, Hapus</button>
      </form>
    </div>
  </div>
</div>

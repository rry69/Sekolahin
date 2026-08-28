<style>
  /* ===================== KELOLA SEKOLAH — Bringova (no cards, scoped) ===================== */
  .sch {
    --coral: #FF6B6B;
    --coral-soft: #FFE5E3;
    --coral-2: #FF8E6E;
    --amber: #F59E0B;
    --amber-soft: #FEF3C7;
    --green: #10B981;
    --green-soft: #D1FAE5;
    --blue: #3B82F6;
    --blue-soft: #DBEAFE;
    --purple: #8B5CF6;
    --purple-soft: #EDE9FE;
    --red: #EF4444;
    --red-soft: #FEE2E2;
    --gray: #6b7280;
    --gray-soft: #F3F4F6;
    --ink: #1a1a2e;
    --muted: #8a8f9d;
    --divider: rgba(26, 26, 46, 0.10);

    position: relative;
    border-radius: 24px;
    padding: 28px 28px 44px;
    background: #f6f7fb;
  }
  .sch .s-inner { width: 100%; max-width: 1080px; margin: 0 auto; }

  /* ---------- header ---------- */
  .sch .s-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
  .sch .s-crumb a { color: var(--coral); text-decoration: none; }
  .sch .s-crumb a:hover { text-decoration: underline; }
  .sch .s-crumb .sep { color: #d3d6de; }
  .sch .s-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 2px; }
  .sch .s-meta { font-size: 13px; color: var(--muted); }

  /* ---------- alerts (flash) ---------- */
  .sch .s-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px; font-weight: 500; }
  .sch .s-alert i { margin-top: 2px; }
  .sch .s-alert.success { background: var(--green-soft); color: var(--green); }
  .sch .s-alert.error   { background: var(--red-soft);   color: var(--red); }
  .sch .s-alert.info    { background: var(--blue-soft);  color: var(--blue); }

  /* ---------- head (title + add button) ---------- */
  .sch .s-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 22px; }

  /* ---------- buttons ---------- */
  .sch .s-btn { display: inline-flex; align-items: center; gap: 7px; border: none; cursor: pointer; border-radius: 11px; padding: 10px 17px; font-size: 13px; font-weight: 700; text-decoration: none; transition: transform .15s ease, filter .15s ease, background-color .15s ease; }
  .sch .s-btn:hover { transform: translateY(-1px); }
  .sch .s-btn.coral { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 8px 18px -8px rgba(255,107,107,0.6); }
  .sch .s-btn.coral:hover { filter: brightness(1.04); }
  .sch .s-btn.ghost { background: rgba(255,255,255,0.6); color: var(--ink); box-shadow: 0 2px 10px -8px rgba(26,26,46,0.3); }
  .sch .s-btn.ghost:hover { background: #fff; color: var(--coral); }
  .sch .s-btn.sm { padding: 6px 11px; font-size: 11.5px; border-radius: 9px; }

  /* ---------- section (divider, no card) ---------- */
  .sch .s-sec { border-top: 1px solid var(--divider); padding: 24px 0 6px; }
  .sch .s-sec:first-of-type { border-top: none; padding-top: 4px; }
  .sch .s-sec-head { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; }
  .sch .s-sec-ic { flex: 0 0 auto; width: 40px; height: 40px; border-radius: 13px; display: flex; align-items: center; justify-content: center; font-size: 16px; background: var(--coral-soft); color: var(--coral); }
  .sch .s-sec-name { font-size: 16px; font-weight: 700; color: var(--ink); }
  .sch .s-sec-desc { font-size: 12px; color: var(--muted); margin-top: 1px; }

  /* ---------- pills ---------- */
  .sch .s-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 700; white-space: nowrap; }
  .sch .s-pill.green  { background: var(--green-soft);  color: var(--green); }
  .sch .s-pill.amber  { background: var(--amber-soft);  color: #b45309; }
  .sch .s-pill.blue   { background: var(--blue-soft);   color: var(--blue); }
  .sch .s-pill.red    { background: var(--red-soft);    color: var(--red); }
  .sch .s-pill.gray   { background: var(--gray-soft);   color: var(--gray); }

  /* ---------- list rows (no card, divider) ---------- */
  .sch .s-list { display: flex; flex-direction: column; }
  .sch .s-row { display: flex; align-items: center; gap: 15px; padding: 15px 4px; border-bottom: 1px solid var(--divider); }
  .sch .s-row:last-child { border-bottom: none; }
  .sch .s-row-ic {
    flex: 0 0 auto; width: 46px; height: 46px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center; font-size: 17px;
    background: var(--gray-soft); color: var(--gray);
  }
  .sch .s-body { flex: 1; min-width: 0; }
  .sch .s-name { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; font-size: 14px; font-weight: 700; color: var(--ink); }
  .sch .s-sub { font-size: 12px; color: var(--muted); margin-top: 2px; }
  .sch .s-tags { display: flex; gap: 7px; flex-wrap: wrap; margin-top: 6px; }
  .sch .s-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; flex-wrap: wrap; justify-content: flex-end; }
  .sch .s-empty { text-align: center; color: var(--muted); font-size: 13px; padding: 18px 0; }

  /* ---------- info row (pengaturan jenjang) ---------- */
  .sch .s-info { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-top: 26px; padding: 14px 16px; border: 1px dashed rgba(26,26,46,0.14); border-radius: 14px; background: rgba(255,255,255,0.30); }
  .sch .s-info-text { display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--gray); }
  .sch .s-info-text i { color: var(--blue); font-size: 15px; }
  .sch .s-info-text a { color: var(--blue); font-weight: 600; text-decoration: none; }
  .sch .s-info-text a:hover { text-decoration: underline; }

  /* ---------- delete confirm modal (Bringova) ---------- */
  .sch .s-modal-backdrop { position: fixed; inset: 0; z-index: 90; background: rgba(26,26,46,0.36); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; padding: 16px; }
  .sch .s-modal-backdrop.is-open { display: flex; }
  .sch .s-modal { width: 100%; max-width: 400px; background: #fff; border-radius: 18px; padding: 22px; box-shadow: 0 24px 60px -18px rgba(26,26,46,0.4); animation: sModalPop .2s cubic-bezier(.22,1.2,.36,1); }
  @keyframes sModalPop { from { opacity: 0; transform: scale(0.97) translateY(4px); } to { opacity: 1; transform: scale(1) translateY(0); } }
  .sch .s-modal-body { display: flex; align-items: flex-start; gap: 13px; margin-bottom: 18px; }
  .sch .s-modal-ic { flex: 0 0 auto; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 17px; background: var(--red-soft); color: var(--red); }
  .sch .s-modal-title { font-size: 15px; font-weight: 700; color: var(--ink); }
  .sch .s-modal-msg { font-size: 13px; color: var(--muted); margin-top: 3px; line-height: 1.5; }
  .sch .s-modal-actions { display: flex; justify-content: flex-end; gap: 8px; }
  .sch .s-modal-actions .s-btn-ghost { background: transparent; color: var(--muted); }
  .sch .s-modal-actions .s-btn-ghost:hover { color: var(--ink); }

  /* ---------- responsive ---------- */
  @media (max-width: 720px) {
    .sch { padding: 20px 16px 32px; }
    .sch .s-row { flex-wrap: wrap; }
    .sch .s-actions { justify-content: flex-start; width: 100%; margin-left: 61px; }
    .sch .s-info { flex-direction: column; align-items: flex-start; }
  }
</style>

<div class="sch">
  <div class="s-inner">
  <div class="s-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span>Kelola Sekolah</span>
  </div>

  <div class="s-head">
    <div>
      <h1 class="s-title">Data Sekolah</h1>
      <p class="s-meta">Kelola daftar sekolah berdasarkan jenjang pendidikan</p>
    </div>
    <a href="{{ route('admin.schools.create') }}" class="s-btn coral"><i class="fa-solid fa-plus"></i> Tambah Sekolah</a>
  </div>

  @if (session('success'))
    <div class="s-alert success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
  @endif
  @if (session('error'))
    <div class="s-alert error"><i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span></div>
  @endif

  @forelse ($levels as $level)
    @php $levelSchools = $grouped->get($level->id, collect()); @endphp
    <div class="s-sec">
      <div class="s-sec-head">
        <span class="s-sec-ic"><i class="fa-solid fa-school"></i></span>
        <div>
          <div class="s-sec-name">{{ $level->name }}</div>
          @if($level->description)
            <div class="s-sec-desc">{{ $level->description }}</div>
          @endif
        </div>
        <span class="s-pill {{ $level->is_active ? 'green' : 'gray' }}">
          <i class="fa-solid {{ $level->is_active ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
          {{ $level->is_active ? 'Jenjang Aktif' : 'Jenjang Nonaktif' }}
        </span>
      </div>

      @if ($levelSchools->isNotEmpty())
        <div class="s-list">
          @foreach ($levelSchools as $entry)
            @php $school = $entry['school']; @endphp
            <div class="s-row">
              <span class="s-row-ic"><i class="fa-solid fa-building-columns"></i></span>
              <div class="s-body">
                <div class="s-name">{{ $school->name }}</div>
                <div class="s-sub">
                  @if($school->address)
                    <i class="fa-solid fa-location-dot" style="margin-right:3px"></i>{{ $school->address }}
                  @else
                    Alamat belum diisi
                  @endif
                  @if($school->principal_name)
                    &nbsp;·&nbsp; <i class="fa-solid fa-user-tie" style="margin-right:3px"></i>{{ $school->principal_name }}
                  @endif
                </div>
                <div class="s-tags">
                  <span class="s-pill blue"><i class="fa-solid fa-book"></i> {{ $school->majors_count }} jurusan</span>
                  @if($school->school_status)
                    <span class="s-pill gray">{{ ucfirst($school->school_status) }}</span>
                  @endif
                </div>
              </div>
              <div class="s-actions">
                <a href="{{ route('admin.schools.edit', $school) }}" class="s-btn sm" style="background:var(--amber-soft);color:#b45309"><i class="fa-solid fa-pen"></i> Edit</a>
                <button type="button" class="s-btn sm" style="background:var(--red-soft);color:var(--red)" onclick="openSchoolDelete({{ $school->id }}, '{{ addslashes($school->name) }}')"><i class="fa-solid fa-trash-can"></i> Hapus</button>
                <form id="schoolDeleteForm-{{ $school->id }}" method="POST" action="{{ route('admin.schools.destroy', $school) }}" style="display:none">
                  @csrf
                  @method('DELETE')
                </form>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="s-empty"><i class="fa-regular fa-school" style="display:block;font-size:20px;margin-bottom:6px;color:#d3d6de"></i>Belum ada sekolah untuk jenjang ini.</div>
      @endif
    </div>
  @empty
    <div class="s-empty"><i class="fa-regular fa-school" style="display:block;font-size:20px;margin-bottom:6px;color:#d3d6de"></i>Tidak ada data jenjang</div>
  @endforelse

  @php $levelCount = \App\Models\SchoolLevel::count(); @endphp
  @if ($levelCount > 0)
    <div class="s-info">
      <div class="s-info-text">
        <i class="fa-solid fa-circle-info"></i>
        <span>Status aktif/nonaktif tiap jenjang kini dikelola di <a href="{{ route('admin.settings.edit', ['tab' => 'jenjang']) }}">Pengaturan › tab Jenjang</a>.</span>
      </div>
      <a href="{{ route('admin.settings.edit', ['tab' => 'jenjang']) }}" class="s-btn ghost sm"><i class="fa-solid fa-gear"></i> Buka Pengaturan Jenjang</a>
    </div>
  @endif
  </div>

{{-- ================== DELETE SCHOOL CONFIRM MODAL (Bringova) ================== --}}
<div id="schoolDeleteModal" class="s-modal-backdrop" aria-hidden="true">
  <div class="s-modal" role="dialog" aria-modal="true">
    <div class="s-modal-body">
      <div id="schoolDeleteIcon" class="s-modal-ic"><i class="fa-solid fa-trash-can"></i></div>
      <div style="flex:1;min-width:0">
        <h3 id="schoolDeleteTitle" class="s-modal-title">Hapus sekolah ini?</h3>
        <p id="schoolDeleteMessage" class="s-modal-msg"></p>
      </div>
    </div>
    <div class="s-modal-actions">
      <button type="button" onclick="closeSchoolDelete()" class="s-btn ghost sm s-btn-ghost">Batal</button>
      <button type="button" id="schoolDeleteAction" class="s-btn sm" style="background:var(--red);color:#fff">Ya, Hapus</button>
    </div>
  </div>
</div>
</div>

<script>
(function () {
  var pendingForm = null;

  window.openSchoolDelete = function (id, name) {
    pendingForm = document.getElementById('schoolDeleteForm-' + id);
    document.getElementById('schoolDeleteTitle').textContent = 'Hapus sekolah ini?';
    document.getElementById('schoolDeleteMessage').textContent = 'Hapus sekolah "' + name + '"? Sekolah beserta data jurusannya akan dihapus permanen.';
    showModal();
  };

  window.closeSchoolDelete = function () {
    hideModal();
    pendingForm = null;
  };

  function showModal() {
    var m = document.getElementById('schoolDeleteModal');
    m.classList.add('is-open');
    m.setAttribute('aria-hidden', 'false');
  }
  function hideModal() {
    var m = document.getElementById('schoolDeleteModal');
    m.classList.remove('is-open');
    m.setAttribute('aria-hidden', 'true');
  }

  document.getElementById('schoolDeleteModal').addEventListener('click', function (e) {
    if (e.target === this) closeSchoolDelete();
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && document.getElementById('schoolDeleteModal').classList.contains('is-open')) closeSchoolDelete();
  });
  document.getElementById('schoolDeleteAction').addEventListener('click', function () {
    if (pendingForm) pendingForm.submit();
  });
})();
</script>

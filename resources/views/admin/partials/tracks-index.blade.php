<style>
  /* ===================== TRACKS — Pengaturan Jalur (Bringova, no cards, scoped) ===================== */
  .trk {
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
    padding: 28px 28px 40px;
    background: #f6f7fb;
  }

  /* ---------- header ---------- */
  .trk .t-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
  .trk .t-crumb a { color: var(--coral); text-decoration: none; }
  .trk .t-crumb a:hover { text-decoration: underline; }
  .trk .t-crumb .sep { color: #d3d6de; }
  .trk .t-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 4px; }
  .trk .t-meta { font-size: 13px; color: var(--muted); line-height: 1.5; margin-bottom: 22px; }

  /* ---------- alert (flash) ---------- */
  .trk .t-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px; font-weight: 500; }
  .trk .t-alert i { margin-top: 2px; }
  .trk .t-alert.success { background: var(--green-soft); color: var(--green); }
  .trk .t-alert.error   { background: var(--red-soft);   color: var(--red); }

  /* ---------- level section (divider, no card) ---------- */
  .trk .t-sec { border-top: 1px solid var(--divider); padding: 22px 0 4px; }
  .trk .t-sec:first-of-type { border-top: none; padding-top: 4px; }
  .trk .t-sec-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 14px; }
  .trk .t-sec-head-l { display: flex; align-items: center; gap: 11px; }
  .trk .t-sec-ic { flex: 0 0 auto; width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 15px; background: var(--coral-soft); color: var(--coral); }
  .trk .t-sec-name { font-size: 15px; font-weight: 700; color: var(--ink); }
  .trk .t-sec-desc { font-size: 12px; color: var(--muted); margin-top: 2px; }

  /* ---------- pills ---------- */
  .trk .t-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 700; white-space: nowrap; }
  .trk .t-pill.green { background: transparent; border: 1px solid currentColor; color: var(--green); }
  .trk .t-pill.amber { background: transparent; border: 1px solid currentColor; color: #b45309; }
  .trk .t-pill.red   { background: transparent; border: 1px solid currentColor;   color: var(--red); }
  .trk .t-pill.coral { background: transparent; border: 1px solid currentColor; color: var(--coral); }
  .trk .t-pill.blue  { background: transparent; border: 1px solid currentColor;  color: var(--blue); }

  /* ---------- track row ---------- */
  .trk .t-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 16px 6px; border-bottom: 1px solid var(--divider); }
  .trk .t-row:last-child { border-bottom: none; }
  .trk .t-row-info { display: flex; align-items: flex-start; gap: 14px; flex: 1; min-width: 0; }
  .trk .t-row-ic { flex: 0 0 auto; width: 44px; height: 44px; border-radius: 13px; display: flex; align-items: center; justify-content: center; font-size: 16px; background: var(--gray-soft); color: var(--gray); }
  .trk .t-row.active .t-row-ic { background: var(--coral-soft); color: var(--coral); }
  .trk .t-row-name { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
  .trk .t-row-name b { font-size: 14px; font-weight: 700; color: var(--ink); }
  .trk .t-row-desc { font-size: 12px; color: var(--muted); margin-top: 3px; }
  .trk .t-row-count { font-size: 11.5px; color: var(--muted); margin-top: 4px; }
  .trk .t-row-count b { color: var(--ink); font-weight: 600; }

  /* ---------- toggle (wrap existing global .track-pill) ---------- */
  .trk .t-toggle { position: relative; display: inline-flex; align-items: center; cursor: pointer; margin-left: 16px; flex-shrink: 0; }

  /* ---------- empty state ---------- */
  .trk .t-empty { text-align: center; color: var(--muted); font-size: 13px; padding: 34px 0; }
  .trk .t-empty i { display: block; font-size: 26px; margin-bottom: 8px; color: #d3d6de; }

  /* ---------- modal (Bringova) ---------- */
  .trk .t-modal-backdrop { position: fixed; inset: 0; z-index: 90; background: rgba(26,26,46,0.36); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; padding: 16px; }
  .trk .t-modal-backdrop.is-open { display: flex; }
  .trk .t-modal { width: 100%; max-width: 400px; background: #fff; border-radius: 18px; padding: 22px; box-shadow: 0 24px 60px -18px rgba(26,26,46,0.4); animation: tModalPop .2s cubic-bezier(.22,1.2,.36,1); }
  @keyframes tModalPop { from { opacity: 0; transform: scale(0.97) translateY(4px); } to { opacity: 1; transform: scale(1) translateY(0); } }
  .trk .t-modal-body { display: flex; align-items: flex-start; gap: 13px; margin-bottom: 18px; }
  .trk .t-modal-ic { flex: 0 0 auto; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; background: var(--amber-soft); color: #b45309; }
  .trk .t-modal-title { font-size: 15px; font-weight: 700; color: var(--ink); }
  .trk .t-modal-msg { font-size: 13px; color: var(--muted); margin-top: 3px; line-height: 1.5; }
  .trk .t-modal-actions { display: flex; justify-content: flex-end; gap: 8px; }
  .trk .t-btn { display: inline-flex; align-items: center; gap: 6px; border: none; cursor: pointer; border-radius: 11px; padding: 9px 15px; font-size: 12.5px; font-weight: 700; transition: transform .15s ease, filter .15s ease, background-color .15s ease, color .15s ease; }
  .trk .t-btn:hover { transform: translateY(-1px); }
  .trk .t-btn.amber { background: var(--amber); color: #fff; }
  .trk .t-btn.amber:hover { background: #d97706; }
  .trk .t-btn.ghost { background: transparent; color: var(--muted); }
  .trk .t-btn.ghost:hover { color: var(--ink); }

  /* ---------- responsive ---------- */
  @media (max-width: 620px) {
    .trk { padding: 20px 16px 32px; }
    .trk .t-row { align-items: flex-start; }
    .trk .t-toggle { margin-left: 0; }
  }
</style>

<div class="trk">
  <div class="t-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span>Pengaturan Jalur Pendaftaran</span>
  </div>

  <h1 class="t-title">Pengaturan Jalur Pendaftaran</h1>
  <p class="t-meta">Kelola status aktif/nonaktif jalur (Beasiswa, Prestasi, Reguler) per jenjang. Jalur yang dinonaktifkan tidak muncul di form pendaftaran siswa dan ditolak di backend. Data historis pendaftar lama tetap tersimpan.</p>

  @if (session('success'))
    <div class="t-alert success"><x-hi name="checkmark-circle-02" /><span>{{ session('success') }}</span></div>
  @endif
  @if (session('error'))
    <div class="t-alert error"><x-hi name="alert-02" /><span>{{ session('error') }}</span></div>
  @endif

  @if ($levels->isEmpty() || $tracks->isEmpty())
    <div class="t-empty"><x-hi name="folder-open" />Belum ada jenjang atau jalur terdaftar.</div>
  @else
    @foreach ($levels as $level)
      <div class="t-sec">
        <div class="t-sec-head">
          <div class="t-sec-head-l">
            <span class="t-sec-ic"><x-hi name="school" /></span>
            <div>
              <div class="t-sec-name">{{ $level->name }}</div>
              @if($level->description)
                <div class="t-sec-desc">{{ $level->description }}</div>
              @endif
            </div>
          </div>
          <span class="t-pill {{ $level->is_active ? 'green' : 'amber' }}">{{ $level->is_active ? 'Jenjang Aktif' : 'Jenjang Nonaktif' }}</span>
        </div>

        @foreach ($tracks as $track)
          @php
            $isActive = $statusMap[$level->id][$track->id] ?? true;
            $count = $counts->get($level->id)?->get($track->id)?->total ?? 0;
          @endphp
          <div class="t-row {{ $isActive ? 'active' : '' }} track-row" data-track-id="{{ $track->id }}" data-level-id="{{ $level->id }}">
            <div class="t-row-info">
              <span class="t-row-ic"><x-hi name="route-01" /></span>
              <div style="min-width:0">
                <div class="t-row-name">
                  <b>{{ $track->name }}</b>
                  <span class="status-badge track-badge {{ $isActive ? 'status-accepted' : 'status-rejected' }}" style="{{ $isActive ? '' : 'background: transparent; border: 1px solid currentColor;color:var(--badge-rejected-fg);' }}">{{ $isActive ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                @if($track->description)
                  <div class="t-row-desc">{{ $track->description }}</div>
                @endif
                <div class="t-row-count">Pendaftar jenjang ini: <b>{{ $count }}</b> (historis tetap tersimpan)</div>
              </div>
            </div>
            <label class="t-toggle">
              <input type="checkbox" class="sr-only track-toggle" data-track="{{ $track->id }}" data-level="{{ $level->id }}"
                data-track-name="{{ $track->name }}" data-level-name="{{ $level->name }}" data-status="{{ $isActive ? '1' : '0' }}" {{ $isActive ? 'checked' : '' }}>
              <div class="track-pill {{ $isActive ? 'on' : '' }}">
                <div class="track-knob"></div>
              </div>
            </label>
          </div>
        @endforeach
      </div>
    @endforeach
  @endif

  {{-- ================== MODAL KONFIRMASI NONAKTIFKAN JALUR (Bringova) ================== --}}
  <div id="trackConfirmModal" class="t-modal-backdrop" aria-hidden="true">
    <div class="t-modal" role="dialog" aria-modal="true">
      <div class="t-modal-body">
        <div class="t-modal-ic"><x-hi name="toggle-off" /></div>
        <div style="flex:1;min-width:0">
          <h3 class="t-modal-title">Nonaktifkan jalur ini?</h3>
          <p id="trackConfirmMsg" class="t-modal-msg"></p>
        </div>
      </div>
      <div class="t-modal-actions">
        <button type="button" class="t-btn ghost" onclick="closeTrackConfirm()">Batal</button>
        <button type="button" class="t-btn amber" id="trackConfirmAction">Ya, Nonaktifkan</button>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  var pending = null;

  function showModal() {
    var m = document.getElementById('trackConfirmModal');
    if (!m) return;
    m.classList.add('is-open');
    m.setAttribute('aria-hidden', 'false');
  }
  function hideModal() {
    var m = document.getElementById('trackConfirmModal');
    if (!m) return;
    m.classList.remove('is-open');
    m.setAttribute('aria-hidden', 'true');
  }

  // Dipanggil dari listener toggle di layout saat jalur dinonaktifkan.
  window.confirmTrackDeactivate = function (payload) {
    pending = payload;
    var msg = document.getElementById('trackConfirmMsg');
    if (msg) msg.textContent = 'Nonaktifkan jalur "' + (payload.trackName || '') + '" untuk jenjang "' + (payload.levelName || '') + '"? Jalur ini tidak akan muncul di form pendaftaran siswa dan ditolak di backend. Data historis pendaftar lama tetap tersimpan.';
    showModal();
  };

  window.closeTrackConfirm = function () {
    hideModal();
    if (pending && pending.el) pending.el.checked = pending.wasActive;
    pending = null;
  };

  document.getElementById('trackConfirmAction').addEventListener('click', function () {
    if (!pending) return;
    var p = pending;
    var el = p.el;
    hideModal();
    pending = null;
    // Lanjutkan eksekusi toggle dengan is_active=false (mode nonaktifkan).
    if (window.doTrackToggle) {
      window.doTrackToggle(el, p.row, p.trackId, p.levelId, p.trackName, p.levelName, false, p.wasActive);
    }
  });

  var bd = document.getElementById('trackConfirmModal');
  if (bd) bd.addEventListener('click', function (e) { if (e.target === this) closeTrackConfirm(); });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      var m = document.getElementById('trackConfirmModal');
      if (m && m.classList.contains('is-open')) closeTrackConfirm();
    }
  });
})();
</script>
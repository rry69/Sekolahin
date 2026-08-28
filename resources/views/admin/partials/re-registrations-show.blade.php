<style>
  /* ===================== RE-REGISTRATIONS SHOW — Bringova (no cards, scoped) ===================== */
  .rres {
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

  /* ---------- header ---------- */
  .rres .r-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
  .rres .r-crumb a { color: var(--coral); text-decoration: none; }
  .rres .r-crumb a:hover { text-decoration: underline; }
  .rres .r-crumb .sep { color: #d3d6de; }
  .rres .r-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 2px; }
  .rres .r-meta { font-size: 13px; color: var(--muted); margin-bottom: 20px; }
  .rres .r-meta b { color: var(--ink); font-weight: 600; }
  .rres .r-head-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }

  /* ---------- alerts (flash) ---------- */
  .rres .r-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px; font-weight: 500; }
  .rres .r-alert i { margin-top: 2px; }
  .rres .r-alert.success { background: var(--green-soft); color: var(--green); }
  .rres .r-alert.error   { background: var(--red-soft);   color: var(--red); }

  /* ---------- section (divider, no card) ---------- */
  .rres .r-sec { border-top: 1px solid var(--divider); padding: 26px 0 6px; margin-top: 4px; }
  .rres .r-sec:first-of-type { border-top: none; padding-top: 4px; }
  .rres .r-sec-title { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--ink); text-transform: uppercase; letter-spacing: .4px; margin-bottom: 18px; }
  .rres .r-sec-title i { color: var(--coral); font-size: 13px; }

  /* ---------- info grid (label-value) ---------- */
  .rres .r-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px 22px; }
  .rres .r-item .r-lbl { font-size: 11.5px; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: .3px; margin-bottom: 3px; }
  .rres .r-item .r-val { font-size: 14px; color: var(--ink); font-weight: 600; }
  .rres .r-item.full { grid-column: 1 / -1; }

  /* ---------- pills ---------- */
  .rres .r-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 700; white-space: nowrap; }
  .rres .r-pill.green  { background: var(--green-soft);  color: var(--green); }
  .rres .r-pill.amber  { background: var(--amber-soft);  color: #b45309; }
  .rres .r-pill.red    { background: var(--red-soft);    color: var(--red); }

  /* ---------- code ---------- */
  .rres .r-code { font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; letter-spacing: 2px; font-size: 22px; font-weight: 800; color: var(--coral); }
  .rres .r-code-sub { font-size: 12px; color: var(--muted); margin-top: 4px; }

  /* ---------- buttons ---------- */
  .rres .r-act { display: inline-flex; align-items: center; gap: 6px; border: none; cursor: pointer; border-radius: 11px; padding: 9px 15px; font-size: 12.5px; font-weight: 700; transition: transform .15s ease, filter .15s ease, background-color .15s ease, color .15s ease; text-decoration: none; }
  .rres .r-act:hover { transform: translateY(-1px); }
  .rres .r-act.ghost { background: rgba(255,255,255,0.6); color: var(--ink); box-shadow: 0 2px 10px -8px rgba(26,26,46,0.3); }
  .rres .r-act.ghost:hover { background: #fff; color: var(--coral); }
  .rres .r-act.verify { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 6px 16px -8px rgba(255,107,107,0.6); }
  .rres .r-act.verify:hover { filter: brightness(1.04); }
  .rres .r-act.reject { background: var(--red); color: #fff; }
  .rres .r-act.reject:hover { background: #dc2626; }

  .rres .r-back { margin-top: 28px; display: flex; justify-content: space-between; align-items: center; gap: 8px; flex-wrap: wrap; }
  .rres .r-back-actions { display: flex; gap: 8px; }

  /* ---------- modal (Bringova) ---------- */
  .rres .r-modal-backdrop { position: fixed; inset: 0; z-index: 90; background: rgba(26,26,46,0.36); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; padding: 16px; }
  .rres .r-modal-backdrop.is-open { display: flex; }
  .rres .r-modal { width: 100%; max-width: 420px; background: #fff; border-radius: 18px; padding: 22px; box-shadow: 0 24px 60px -18px rgba(26,26,46,0.4); animation: rresModalPop .2s cubic-bezier(.22,1.2,.36,1); }
  @keyframes rresModalPop { from { opacity: 0; transform: scale(0.97) translateY(4px); } to { opacity: 1; transform: scale(1) translateY(0); } }
  .rres .r-modal-title { display: flex; align-items: center; gap: 9px; font-size: 15px; font-weight: 700; color: var(--ink); margin-bottom: 14px; }
  .rres .r-modal-title.red i { color: var(--red); }
  .rres .r-modal-title.green i { color: var(--green); }
  .rres .r-modal label { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
  .rres .r-modal textarea { width: 100%; padding: 9px 12px; border: 1px solid rgba(26,26,46,0.14); border-radius: 10px; font-size: 13px; font-family: inherit; color: var(--ink); background: rgba(255,255,255,0.55); transition: border-color .18s ease, box-shadow .18s ease; resize: vertical; }
  .rres .r-modal textarea:focus { outline: none; border-color: var(--red); box-shadow: 0 0 0 3px rgba(239,68,68,0.12); background: #fff; }
  .rres .r-modal-msg { font-size: 13px; color: var(--muted); line-height: 1.6; }
  .rres .r-modal-msg b { color: var(--ink); }
  .rres .r-modal-foot { display: flex; gap: 8px; justify-content: flex-end; margin-top: 16px; }

  /* ---------- responsive ---------- */
  @media (max-width: 620px) {
    .rres { padding: 20px 16px 32px; }
    .rres .r-grid { grid-template-columns: 1fr; }
    .rres .r-head-actions { justify-content: flex-start; }
    .rres .r-back { flex-direction: column; align-items: flex-start; }
  }
</style>

<div class="rres">
  <div class="r-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <a href="{{ route('admin.re-registrations.index') }}">Daftar Ulang</a>
    <span class="sep">/</span>
    <span>Detail</span>
  </div>

  @php
    $statusMap = ['pending' => 'amber', 'completed' => 'green', 'rejected' => 'red'];
    $statusLabels = ['pending' => 'Pending', 'completed' => 'Selesai', 'rejected' => 'Ditolak'];
  @endphp

  <div class="flex flex-wrap items-start justify-between gap-4" style="margin-bottom:18px">
    <div>
      <h1 class="r-title">Detail Daftar Ulang</h1>
      <p class="r-meta">No. Registrasi: <b>{{ $reRegistration->registration->registration_number }}</b></p>
    </div>
    <div class="r-head-actions">
      <span class="r-pill {{ $statusMap[$reRegistration->status] ?? 'amber' }}">{{ $statusLabels[$reRegistration->status] ?? ucfirst($reRegistration->status) }}</span>
    </div>
  </div>

  @if (session('success'))
    <div class="r-alert success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
  @endif
  @if (session('error'))
    <div class="r-alert error"><i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span></div>
  @endif

  <div class="r-sec">
    <div class="r-sec-title"><i class="fa-solid fa-user"></i> Informasi Pendaftar</div>
    <div class="r-grid">
      <div class="r-item">
        <div class="r-lbl">Nama Lengkap</div>
        <div class="r-val">{{ $reRegistration->registration->applicant->full_name }}</div>
      </div>
      <div class="r-item">
        <div class="r-lbl">Email</div>
        <div class="r-val">{{ $reRegistration->registration->applicant->user->email }}</div>
      </div>
      <div class="r-item">
        <div class="r-lbl">Jenjang</div>
        <div class="r-val">{{ $reRegistration->registration->registrationPeriod->schoolLevel->name }}</div>
      </div>
      <div class="r-item">
        <div class="r-lbl">Jalur Pendaftaran</div>
        <div class="r-val">{{ $reRegistration->registration->registrationTrack->name }}</div>
      </div>
    </div>
  </div>

  @if($reRegistration->verification_code)
  <div class="r-sec">
    <div class="r-sec-title"><i class="fa-solid fa-qrcode"></i> Kode Verifikasi</div>
    <div class="r-code">{{ $reRegistration->verification_code }}</div>
    <div class="r-code-sub">Kode pada kartu daftar ulang</div>
  </div>
  @endif

  <div class="r-sec">
    <div class="r-sec-title"><i class="fa-solid fa-clipboard-check"></i> Status Verifikasi</div>
    <div class="r-grid">
      <div class="r-item">
        <div class="r-lbl">Tanggal Submit</div>
        <div class="r-val">{{ $reRegistration->submitted_at ? $reRegistration->submitted_at->format('d M Y H:i') : '-' }}</div>
      </div>
      @if ($reRegistration->verified_at)
        <div class="r-item">
          <div class="r-lbl">Tanggal Verifikasi</div>
          <div class="r-val">{{ $reRegistration->verified_at->format('d M Y H:i') }}</div>
        </div>
        <div class="r-item">
          <div class="r-lbl">Diverifikasi Oleh</div>
          <div class="r-val">{{ $reRegistration->verifier->name ?? '-' }}</div>
        </div>
      @endif
      @if ($reRegistration->notes)
        <div class="r-item full">
          <div class="r-lbl">Catatan</div>
          <div class="r-val">{{ $reRegistration->notes }}</div>
        </div>
      @endif
    </div>
  </div>

  <div class="r-back">
    <a href="{{ route('admin.re-registrations.index') }}" class="r-act ghost"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    @if ($reRegistration->status === 'pending')
      <div class="r-back-actions">
        <button type="button" onclick="showReRegRejectModal({{ $reRegistration->id }})" class="r-act reject"><i class="fa-solid fa-xmark"></i> Tolak Daftar Ulang</button>
        <button type="button" onclick="openReRegVerify({{ $reRegistration->id }}, '{{ $reRegistration->registration->registration_number }}')" class="r-act verify"><i class="fa-solid fa-check"></i> Verifikasi Daftar Ulang</button>
      </div>
    @endif
  </div>

  {{-- Modal Tolak Daftar Ulang (Bringova) --}}
  <div id="reRegRejectModal" class="r-modal-backdrop" style="display:none">
    <div class="r-modal" role="dialog" aria-modal="true">
      <div class="r-modal-title red"><i class="fa-solid fa-circle-exclamation"></i> Tolak Daftar Ulang</div>
      <form id="reRegRejectForm" method="POST">
        @csrf
        <label>Catatan / Alasan Penolakan</label>
        <textarea name="notes" rows="4" placeholder="Alasan penolakan (wajib)" required></textarea>
        <div class="r-modal-foot">
          <button type="button" onclick="hideReRegRejectModal()" class="r-act ghost">Batal</button>
          <button type="submit" class="r-act reject"><i class="fa-solid fa-xmark"></i> Tolak</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Modal Konfirmasi Verifikasi (Bringova) --}}
  <div id="reRegVerifyModal" class="r-modal-backdrop" style="display:none">
    <div class="r-modal" role="dialog" aria-modal="true">
      <div class="r-modal-title green"><i class="fa-solid fa-circle-check"></i> Verifikasi Daftar Ulang</div>
      <p class="r-modal-msg">Verifikasi daftar ulang <b id="reRegVerifyNumber"></b>? Pendaftaran akan ditandai <b>Daftar Ulang Selesai</b>.</p>
      <form id="reRegVerifyForm" method="POST" style="margin-top:0">
        @csrf
      </form>
      <div class="r-modal-foot">
        <button type="button" onclick="closeReRegVerify()" class="r-act ghost">Batal</button>
        <button type="button" onclick="submitReRegVerify()" class="r-act verify"><i class="fa-solid fa-check"></i> Ya, Verifikasi</button>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  var pendingReRegId = null;

  window.openReRegVerify = function (id, regNumber) {
    pendingReRegId = id;
    var num = document.getElementById('reRegVerifyNumber');
    if (num) num.textContent = regNumber;
    var form = document.getElementById('reRegVerifyForm');
    if (form) form.action = '/admin/re-registrations/' + id + '/verify';
    var m = document.getElementById('reRegVerifyModal');
    if (m) { m.style.display = 'flex'; m.classList.add('is-open'); }
  };

  window.closeReRegVerify = function () {
    var m = document.getElementById('reRegVerifyModal');
    if (m) { m.style.display = 'none'; m.classList.remove('is-open'); }
    pendingReRegId = null;
  };

  window.submitReRegVerify = function () {
    var form = document.getElementById('reRegVerifyForm');
    if (form) form.submit();
  };

  var vm = document.getElementById('reRegVerifyModal');
  if (vm) vm.addEventListener('click', function (e) { if (e.target === this) closeReRegVerify(); });

  var rm = document.getElementById('reRegRejectModal');
  if (rm) rm.addEventListener('click', function (e) { if (e.target === this) hideReRegRejectModal(); });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      var vm2 = document.getElementById('reRegVerifyModal');
      if (vm2 && vm2.style.display === 'flex') closeReRegVerify();
    }
  });
})();
</script>

<style>
  /* ===================== PAYMENTS INDEX — Bringova (no cards, scoped) ===================== */
  .pay {
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
  .pay .p-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
  .pay .p-crumb a { color: var(--coral); text-decoration: none; }
  .pay .p-crumb a:hover { text-decoration: underline; }
  .pay .p-crumb .sep { color: #d3d6de; }
  .pay .p-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 22px; }

  /* ---------- alerts (flash) ---------- */
  .pay .p-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px; font-weight: 500; }
  .pay .p-alert i { margin-top: 2px; }
  .pay .p-alert.success { background: var(--green-soft); color: var(--green); }
  .pay .p-alert.error   { background: var(--red-soft);   color: var(--red); }

  /* ---------- tabs (underline, no box) ---------- */
  .pay .p-tabs { display: flex; gap: 18px; border-bottom: 1px solid var(--divider); margin-bottom: 22px; flex-wrap: wrap; }
  .pay .p-tabs a.doc-tab,
  .pay .p-tabs a.p-tab {
    all: unset;
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 2px 11px; font-size: 13px; font-weight: 600; color: var(--muted);
    text-decoration: none; border-bottom: 2.5px solid transparent; margin-bottom: -1px;
    cursor: pointer; white-space: nowrap;
    transition: color .18s ease;
  }
  .pay .p-tabs a.p-tab:hover, .pay .p-tabs a.doc-tab:hover { color: var(--ink); }
  .pay .p-tabs a.p-tab.active, .pay .p-tabs a.doc-tab.active { color: var(--coral); border-bottom-color: var(--coral); }

  /* ---------- toolbar: search + filter ---------- */
  .pay .p-toolbar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 16px; }
  .pay .p-search { position: relative; flex: 1; min-width: 200px; }
  .pay .p-search i { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 13px; pointer-events: none; }
  .pay .p-search input { width: 100%; padding: 11px 14px 11px 38px; border: 1px solid rgba(26,26,46,0.14); border-radius: 12px; font-size: 13.5px; color: var(--ink); background: rgba(255,255,255,0.55); transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
  .pay .p-search input::placeholder { color: var(--muted); }
  .pay .p-search input:focus { outline: none; border-color: var(--coral); box-shadow: 0 0 0 4px rgba(255,107,107,0.14); background: #fff; }
  .pay .p-fbtn, .pay .p-gobtn { display: inline-flex; align-items: center; gap: 7px; border: none; cursor: pointer; border-radius: 12px; padding: 11px 18px; font-size: 13px; font-weight: 700; transition: transform .15s ease, filter .15s ease; }
  .pay .p-fbtn { background: rgba(255,255,255,0.7); color: var(--ink); box-shadow: 0 4px 14px -10px rgba(26,26,46,0.3); }
  .pay .p-fbtn:hover { background: #fff; color: var(--coral); }
  .pay .p-gobtn { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 8px 18px -8px rgba(255,107,107,0.6); }
  .pay .p-gobtn:hover { filter: brightness(1.03); transform: translateY(-1px); }

  /* ---------- filter panel ---------- */
  .pay .p-filters { display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end; padding: 18px; margin-bottom: 20px; border: 1px dashed rgba(26,26,46,0.14); border-radius: 14px; background: rgba(255,255,255,0.30); }
  .pay .p-field { display: flex; flex-direction: column; gap: 5px; }
  .pay .p-field label { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .3px; }

  /* ---------- picker trigger (pengganti <select>) ---------- */
  .pay .r-pick { display: inline-flex; align-items: center; gap: 8px; flex-wrap: nowrap; padding: 9px 4px; border: none; border-bottom: 1px solid rgba(26,26,46,0.18); border-radius: 0; font-size: 13px; color: var(--ink); background: transparent; min-width: 170px; cursor: pointer; text-align: left; min-height: 38px; max-width: 100%; transition: border-color .18s ease, color .18s ease; }
  .pay .r-pick:hover { border-bottom-color: var(--coral); }
  .pay .r-pick:focus { outline: none; border-bottom-color: var(--coral); }
  .pay .r-pick .pick-label { flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .pay .r-pick .pick-label.is-placeholder { color: var(--muted); }
  .pay .r-pick .pick-caret { display: none; }
  .pay .r-pick .pick-clear { flex: 0 0 auto; display: none; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 6px; background: var(--gray-soft); color: var(--gray); cursor: pointer; font-size: 9px; user-select: none; }
  .pay .r-pick .pick-clear:hover { background: var(--red-soft); color: var(--red); }
  .pay .r-pick.has-value .pick-clear { display: inline-flex; }
  .pay .r-pick.has-value .pick-label.is-placeholder { display: none; }

  /* ---------- modal picker (Bringova) ---------- */
  .pay .picker-backdrop { position: fixed; inset: 0; z-index: 80; background: rgba(26,26,46,0.32); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); display: none; align-items: flex-start; justify-content: center; padding: 80px 16px 16px; animation: pPickerFade .18s ease-out; }
  .pay .picker-backdrop.is-open { display: flex; }
  @keyframes pPickerFade { from { opacity: 0; } to { opacity: 1; } }
  .pay .picker-panel { width: 100%; max-width: 380px; max-height: min(520px, calc(100vh - 120px)); display: flex; flex-direction: column; background: #fff; border-radius: 18px; box-shadow: 0 20px 50px -16px rgba(26,26,46,0.35), 0 0 0 1px rgba(26,26,46,0.06); overflow: hidden; animation: pPickerPop .22s cubic-bezier(.22,1.2,.36,1); }
  @keyframes pPickerPop { from { opacity: 0; transform: translateY(-6px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
  .pay .picker-head { display: flex; align-items: center; gap: 10px; padding: 14px 16px; border-bottom: 1px solid var(--divider); }
  .pay .picker-head .picker-title { font-size: 14px; font-weight: 700; color: var(--ink); flex: 1; }
  .pay .picker-head .picker-close { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 8px; border: none; background: transparent; color: var(--muted); cursor: pointer; font-size: 12px; transition: background-color .15s ease, color .15s ease; }
  .pay .picker-head .picker-close:hover { background: var(--gray-soft); color: var(--ink); }
  .pay .picker-search { position: relative; padding: 10px 14px; border-bottom: 1px solid var(--divider); }
  .pay .picker-search i { position: absolute; left: 24px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 12px; pointer-events: none; }
  .pay .picker-search input { width: 100%; padding: 9px 12px 9px 32px; border: 1px solid rgba(26,26,46,0.14); border-radius: 10px; font-size: 13px; color: var(--ink); background: rgba(255,255,255,0.7); transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
  .pay .picker-search input:focus { outline: none; border-color: var(--coral); background: #fff; box-shadow: 0 0 0 3px rgba(255,107,107,0.12); }
  .pay .picker-list { flex: 1; overflow-y: auto; padding: 6px 8px; }
  .pay .picker-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; font-size: 13px; color: var(--ink); cursor: pointer; user-select: none; transition: background-color .15s ease, color .15s ease; }
  .pay .picker-item:hover, .pay .picker-item.is-active { background: var(--coral-soft); color: var(--coral); }
  .pay .picker-item.is-selected { background: var(--coral); color: #fff; font-weight: 600; }
  .pay .picker-item.is-selected:hover { background: var(--coral); }
  .pay .picker-item .pi-label { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .pay .picker-item .pi-check { font-size: 11px; opacity: 0; }
  .pay .picker-item.is-selected .pi-check { opacity: 1; }
  .pay .picker-empty { padding: 26px 12px; text-align: center; color: var(--muted); font-size: 12.5px; }
  .pay .picker-empty i { display: block; font-size: 20px; margin-bottom: 6px; color: #d3d6de; }
  .pay .picker-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 10px 14px; border-top: 1px solid var(--divider); background: rgba(255,255,255,0.5); }
  .pay .picker-foot .picker-clear-all { display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px; border-radius: 9px; border: none; background: transparent; color: var(--muted); font-size: 12px; font-weight: 600; cursor: pointer; transition: color .15s ease, background-color .15s ease; }
  .pay .picker-foot .picker-clear-all:hover { color: var(--red); background: var(--red-soft); }
  .pay .picker-foot .picker-done { display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 9px; border: none; background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; font-size: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 6px 14px -6px rgba(255,107,107,0.55); transition: filter .15s ease, transform .15s ease; }
  .pay .picker-foot .picker-done:hover { filter: brightness(1.04); transform: translateY(-1px); }

  /* ---------- list rows ---------- */
  .pay .p-list { display: flex; flex-direction: column; }
  .pay .p-row { display: flex; align-items: center; gap: 15px; padding: 16px 4px; border-bottom: 1px solid var(--divider); }
  .pay .p-row:last-child { border-bottom: none; }
  .pay .p-ic { flex: 0 0 auto; width: 46px; height: 46px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 17px; }
  .pay .p-ic.amber { background: var(--amber-soft); color: #b45309; }
  .pay .p-ic.green { background: var(--green-soft); color: var(--green); }
  .pay .p-ic.red   { background: var(--red-soft);   color: var(--red); }
  .pay .p-ic.blue  { background: var(--blue-soft);  color: var(--blue); }
  .pay .p-body { flex: 1; min-width: 0; }
  .pay .p-name { font-size: 14px; font-weight: 700; color: var(--ink); }
  .pay .p-sub { font-size: 12px; color: var(--muted); }
  .pay .p-tags { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-top: 4px; }
  .pay .p-pill { display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; white-space: nowrap; }
  .pay .p-pill.amber { background: var(--amber-soft); color: #b45309; }
  .pay .p-pill.green { background: var(--green-soft); color: var(--green); }
  .pay .p-pill.red   { background: var(--red-soft);   color: var(--red); }
  .pay .p-pill.gray  { background: var(--gray-soft);  color: var(--gray); }
  .pay .p-pill.coral { background: var(--coral-soft); color: var(--coral); }
  .pay .p-pill.blue  { background: var(--blue-soft);  color: var(--blue); }
  .pay .p-amount { font-size: 16px; font-weight: 800; color: var(--ink); flex-shrink: 0; }
  .pay .p-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; justify-content: flex-end; flex-shrink: 0; }
  .pay .p-btn { display: inline-flex; align-items: center; gap: 6px; border: none; cursor: pointer; border-radius: 10px; padding: 8px 13px; font-size: 12px; font-weight: 700; transition: transform .15s ease, filter .15s ease, background-color .15s ease, color .15s ease; }
  .pay .p-btn:hover { transform: translateY(-1px); }
  .pay .p-btn.sm { padding: 6px 11px; font-size: 11px; border-radius: 9px; }
  .pay .p-btn.coral { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 6px 16px -8px rgba(255,107,107,0.6); }
  .pay .p-btn.coral:hover { filter: brightness(1.04); }
  .pay .p-btn.green { background: var(--green); color: #fff; }
  .pay .p-btn.green:hover { background: #059669; }
  .pay .p-btn.red { background: var(--red); color: #fff; }
  .pay .p-btn.red:hover { background: #dc2626; }
  .pay .p-btn.ghost { background: rgba(255,255,255,0.6); color: var(--ink); box-shadow: 0 2px 10px -8px rgba(26,26,46,0.3); }
  .pay .p-btn.ghost:hover { background: #fff; color: var(--coral); }
  .pay .p-btn.coral-soft { background: var(--coral-soft); color: var(--coral); }
  .pay .p-btn.coral-soft:hover { filter: brightness(0.97); }
  .pay .p-empty { text-align: center; color: var(--muted); font-size: 13px; padding: 30px 0; }
  .pay .p-empty i { display: block; font-size: 26px; margin-bottom: 8px; color: #d3d6de; }

  /* ---------- pagination ---------- */
  .pay .p-pager { margin-top: 22px; display: flex; justify-content: center; }
  .pay .p-pager > nav { display: flex; justify-content: center; }

  /* ---------- modal (Bringova) ---------- */
  .pay .p-modal-backdrop { position: fixed; inset: 0; z-index: 90; background: rgba(26,26,46,0.36); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; padding: 16px; }
  .pay .p-modal-backdrop.is-open { display: flex; }
  .pay .p-modal { width: 100%; max-width: 400px; background: #fff; border-radius: 18px; padding: 22px; box-shadow: 0 24px 60px -18px rgba(26,26,46,0.4); animation: pModalPop .2s cubic-bezier(.22,1.2,.36,1); }
  @keyframes pModalPop { from { opacity: 0; transform: scale(0.97) translateY(4px); } to { opacity: 1; transform: scale(1) translateY(0); } }
  .pay .p-modal-body { display: flex; align-items: flex-start; gap: 13px; margin-bottom: 18px; }
  .pay .p-modal-ic { flex: 0 0 auto; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
  .pay .p-modal-ic.green { background: var(--green-soft); color: var(--green); }
  .pay .p-modal-ic.amber { background: var(--amber-soft); color: #b45309; }
  .pay .p-modal-ic.red   { background: var(--red-soft);   color: var(--red); }
  .pay .p-modal-title { font-size: 15px; font-weight: 700; color: var(--ink); }
  .pay .p-modal-msg { font-size: 13px; color: var(--muted); margin-top: 3px; line-height: 1.5; }
  .pay .p-modal-actions { display: flex; justify-content: flex-end; gap: 8px; }
  .pay .p-modal-actions .p-btn-ghost { background: transparent; color: var(--muted); }
  .pay .p-modal-actions .p-btn-ghost:hover { color: var(--ink); }

  /* ---------- reject form ---------- */
  .pay .p-reject-form { margin-bottom: 16px; }
  .pay .p-reject-form label { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: .3px; }
  .pay .p-reject-form textarea { width: 100%; padding: 10px 12px; border: 1px solid rgba(26,26,46,0.14); border-radius: 10px; font-size: 13px; font-family: inherit; background: #fff; color: var(--ink); resize: vertical; }
  .pay .p-reject-form textarea:focus { outline: none; border-color: var(--red); box-shadow: 0 0 0 3px rgba(239,68,68,0.12); }

  /* ---------- responsive ---------- */
  @media (max-width: 720px) {
    .pay { padding: 20px 16px 32px; }
    .pay .p-row { flex-wrap: wrap; }
    .pay .p-amount { width: 100%; }
    .pay .p-actions { justify-content: flex-start; width: 100%; }
  }
</style>

<div class="pay">
  <div class="p-crumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span>Pembayaran</span>
  </div>
  <h1 class="p-title">Daftar Pembayaran</h1>

  @if (session('success'))
    <div class="p-alert success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
  @endif
  @if (session('error'))
    <div class="p-alert error"><i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span></div>
  @endif

  <div class="p-tabs">
    <a href="{{ route('admin.payments.index') }}" class="p-tab doc-tab {{ !request('status') && !request('search') && !request('payment_type') && !request('payment_method') ? 'active' : '' }}">Semua</a>
    <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="p-tab doc-tab {{ request('status') == 'pending' ? 'active' : '' }}">Pending</a>
    <a href="{{ route('admin.payments.index', ['status' => 'verified']) }}" class="p-tab doc-tab {{ request('status') == 'verified' ? 'active' : '' }}">Terverifikasi</a>
    <a href="{{ route('admin.payments.index', ['status' => 'rejected']) }}" class="p-tab doc-tab {{ request('status') == 'rejected' ? 'active' : '' }}">Ditolak</a>
  </div>

  <form id="filterForm" method="GET" action="{{ route('admin.payments.index') }}">
    <div class="p-toolbar">
      <div class="p-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="search" name="search" placeholder="Cari no. registrasi, nama, atau email…" value="{{ request('search') }}" autocomplete="off">
      </div>
      <button type="button" class="p-fbtn" onclick="toggleFilterPanel()"><i class="fa-solid fa-sliders"></i> Filter</button>
      <button type="submit" class="p-gobtn"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
    </div>

    <div id="filterPanel" class="p-filters" style="display:{{ request('payment_type') || request('payment_method') ? 'flex' : 'none' }}">
      <div class="p-field">
        <label>Tipe Pembayaran</label>
        <button type="button" class="r-pick" data-picker="payment_type" aria-haspopup="listbox" aria-expanded="false">
          <span class="pick-label is-placeholder">Pilih tipe…</span>
          <span class="pick-clear" data-clear="payment_type" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
          <i class="fa-solid fa-chevron-down pick-caret"></i>
        </button>
        <input type="hidden" name="payment_type" data-picker-input="payment_type" value="{{ request('payment_type') }}">
      </div>
      <div class="p-field">
        <label>Metode Pembayaran</label>
        <button type="button" class="r-pick" data-picker="payment_method" aria-haspopup="listbox" aria-expanded="false">
          <span class="pick-label is-placeholder">Pilih metode…</span>
          <span class="pick-clear" data-clear="payment_method" role="button" tabindex="0" aria-label="Bersihkan"><i class="fa-solid fa-xmark"></i></span>
          <i class="fa-solid fa-chevron-down pick-caret"></i>
        </button>
        <input type="hidden" name="payment_method" data-picker-input="payment_method" value="{{ request('payment_method') }}">
      </div>
      <button type="submit" class="p-gobtn"><i class="fa-solid fa-filter"></i> Terapkan</button>
    </div>
  </form>

  @php
    $payIcon = [
      'pending' => ['ic' => 'amber', 'icon' => 'fa-solid fa-hourglass-half'],
      'verified' => ['ic' => 'green', 'icon' => 'fa-solid fa-circle-check'],
      'rejected' => ['ic' => 'red', 'icon' => 'fa-solid fa-circle-xmark'],
    ];
    $pillMap = ['pending' => 'amber', 'verified' => 'green', 'rejected' => 'red'];
    $statusLabels = ['pending' => 'Pending', 'verified' => 'Lunas', 'rejected' => 'Ditolak'];
    $typeLabels = ['registration_fee' => 'Biaya Pendaftaran', 're_registration_fee' => 'Biaya Daftar Ulang'];
    $methodLabels = ['bank_transfer' => 'Transfer Bank', 'cash' => 'Tunai', 'online' => 'Online'];
  @endphp

  @if ($payments->isEmpty())
    <div class="p-empty"><i class="fa-regular fa-credit-card"></i>Tidak ada data pembayaran</div>
  @else
    <div class="p-list">
      @foreach ($payments as $payment)
      @php
        $st = $payment->status;
        $type = $payment->payment_type;
        $method = $payment->payment_method;
      @endphp
      <div class="p-row">
        <span class="p-ic {{ $payIcon[$st]['ic'] ?? 'blue' }}"><i class="{{ $payIcon[$st]['icon'] ?? 'fa-solid fa-money-bill-wave' }}"></i></span>
        <div class="p-body">
          <div class="p-name">{{ $payment->registration->applicant->full_name ?? '-' }}</div>
          <div class="p-sub">{{ $payment->registration->registration_number }} · {{ $payment->registration->applicant->user->email ?? '-' }}</div>
          <div class="p-tags">
            <span class="p-pill coral">{{ $typeLabels[$type] ?? ucfirst(str_replace('_', ' ', $type)) }}</span>
            <span class="p-pill blue">{{ $methodLabels[$method] ?? str_replace('_', ' ', $method) }}</span>
            <span class="p-pill {{ $pillMap[$st] ?? 'gray' }}">{{ $statusLabels[$st] ?? ucfirst($st) }}</span>
          </div>
        </div>
        <div class="p-amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
        <div class="p-actions">
          @if ($payment->proof_file)
            <button type="button" onclick="showFileModal('{{ route('payments.proof', $payment) }}', 'Bukti Pembayaran · {{ $payment->registration->applicant->full_name }}')" class="p-btn ghost sm"><i class="fa-solid fa-receipt"></i> Bukti</button>
          @endif
          @if ($payment->status === 'pending')
            <button type="button" onclick="openPayVerify({{ $payment->id }}, '{{ addslashes($payment->registration->registration_number) }}')" class="p-btn green sm"><i class="fa-solid fa-check"></i> Verifikasi</button>
            <button type="button" onclick="showRejectModal({{ $payment->id }})" class="p-btn red sm"><i class="fa-solid fa-xmark"></i> Tolak</button>
          @else
            <button type="button" onclick="openPayReset({{ $payment->id }}, '{{ addslashes($payment->registration->registration_number) }}')" class="p-btn ghost sm"><i class="fa-solid fa-rotate-left"></i> Reset</button>
          @endif
        </div>
      </div>
      @endforeach
    </div>

    <div class="p-pager">
      {{ $payments->appends(request()->query())->links('vendor.pagination.bringova') }}
    </div>
  @endif

{{-- ============================================================
     Modal Picker (Bringova) — reuse global picker system
     ============================================================ --}}
<div id="pickerBackdrop" class="picker-backdrop" aria-hidden="true">
  <div class="picker-panel" role="dialog" aria-modal="true" aria-labelledby="pickerTitle">
    <div class="picker-head">
      <div class="picker-title" id="pickerTitle">Pilih item</div>
      <button type="button" class="picker-close" onclick="closePicker()" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="picker-search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input id="pickerSearch" type="search" placeholder="Cari…" autocomplete="off">
    </div>
    <div class="picker-list" id="pickerList" role="listbox"></div>
    <div class="picker-foot">
      <button type="button" class="picker-clear-all" onclick="clearCurrentPicker()"><i class="fa-solid fa-eraser"></i> Bersihkan</button>
      <button type="button" class="picker-done" onclick="closePicker()">Selesai</button>
    </div>
  </div>
</div>

@php
  $pickerJson = [
    'payment_type' => $paymentTypes,
    'payment_method' => $paymentMethods,
  ];
  $pickerLabels = [
    'payment_type' => 'Pilih Tipe Pembayaran',
    'payment_method' => 'Pilih Metode Pembayaran',
  ];
@endphp

<div id="reg-data" hidden data-picker='@json($pickerJson)' data-picker-labels='@json($pickerLabels)'></div>

{{-- ================== MODAL VERIFIKASI PEMBAYARAN (Bringova, Enter) ================== --}}
<div id="payVerifyModal" class="p-modal-backdrop" aria-hidden="true">
  <div class="p-modal" role="dialog" aria-modal="true">
    <div class="p-modal-body">
      <div class="p-modal-ic green"><i class="fa-solid fa-circle-check"></i></div>
      <div style="flex:1;min-width:0">
        <h3 class="p-modal-title">Verifikasi pembayaran?</h3>
        <p class="p-modal-msg" id="payVerifyMsg">Yakin ingin memverifikasi pembayaran ini sebagai lunas?</p>
      </div>
    </div>
    <div class="p-modal-actions">
      <button type="button" onclick="closePayVerify()" class="p-btn ghost p-btn-ghost">Batal</button>
      <button type="button" onclick="submitPayVerify()" id="payVerifyAction" class="p-btn green"><i class="fa-solid fa-check"></i> Ya, Verifikasi</button>
    </div>
  </div>
</div>

{{-- ================== MODAL RESET PEMBAYARAN (Bringova, Enter) ================== --}}
<div id="payResetModal" class="p-modal-backdrop" aria-hidden="true">
  <div class="p-modal" role="dialog" aria-modal="true">
    <div class="p-modal-body">
      <div class="p-modal-ic amber"><i class="fa-solid fa-rotate-left"></i></div>
      <div style="flex:1;min-width:0">
        <h3 class="p-modal-title">Kembalikan ke pending?</h3>
        <p class="p-modal-msg" id="payResetMsg">Pembayaran ini akan dikembalikan ke status pending. Yakin lanjut?</p>
      </div>
    </div>
    <div class="p-modal-actions">
      <button type="button" onclick="closePayReset()" class="p-btn ghost p-btn-ghost">Batal</button>
      <button type="button" onclick="submitPayReset()" id="payResetAction" class="p-btn amber"><i class="fa-solid fa-rotate-left"></i> Ya, Reset</button>
    </div>
  </div>
</div>

{{-- ================== MODAL TOLAK PEMBAYARAN (Bringova) ================== --}}
<div id="rejectModal" class="p-modal-backdrop" aria-hidden="true">
  <div class="p-modal" role="dialog" aria-modal="true">
    <div class="p-modal-body">
      <div class="p-modal-ic red"><i class="fa-solid fa-circle-xmark"></i></div>
      <div style="flex:1;min-width:0">
        <h3 class="p-modal-title">Tolak Pembayaran</h3>
        <p class="p-modal-msg">Pembayaran akan ditolak. Beri alasan penolakan (wajib).</p>
      </div>
    </div>
    <form id="rejectForm" method="POST" style="display:block">
      @csrf
      <div class="p-reject-form">
        <label>Alasan Penolakan</label>
        <textarea name="rejection_reason" rows="4" required maxlength="500" placeholder="Tulis alasan penolakan…"></textarea>
      </div>
      <div class="p-modal-actions">
        <button type="button" onclick="hideRejectModal()" class="p-btn ghost p-btn-ghost">Batal</button>
        <button type="submit" class="p-btn red"><i class="fa-solid fa-xmark"></i> Tolak</button>
      </div>
    </form>
  </div>
</div>

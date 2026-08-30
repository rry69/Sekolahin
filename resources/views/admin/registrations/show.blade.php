@extends('layouts.dashboard')
@section('title', 'Detail Pendaftaran')
@section('content')
<style>
  /* ===================== DETAIL PENDAFTARAN — Bringova (no cards, scoped) ===================== */
  .det {
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
  .det .d-crumb { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
  .det .d-crumb a { color: var(--coral); text-decoration: none; }
  .det .d-crumb a:hover { text-decoration: underline; }
  .det .d-crumb .sep { color: #d3d6de; }
  .det .d-title { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; margin-bottom: 2px; }
  .det .d-meta { font-size: 13px; color: var(--muted); }
  .det .d-meta b { color: var(--ink); font-weight: 600; }
  .det .d-head-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }

  /* ---------- alerts (flash) ---------- */
  .det .d-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px; font-weight: 500; }
  .det .d-alert i { margin-top: 2px; }
  .det .d-alert.success { background: var(--green-soft); color: var(--green); }
  .det .d-alert.error   { background: var(--red-soft);   color: var(--red); }
  .det .d-alert.info    { background: var(--blue-soft);  color: var(--blue); }

  /* ---------- section (divider, no card) ---------- */
  .det .d-sec { border-top: 1px solid var(--divider); padding: 26px 0 4px; margin-top: 4px; }
  .det .d-sec:first-of-type { border-top: none; padding-top: 4px; }
  .det .d-sec-title { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--ink); text-transform: uppercase; letter-spacing: .4px; margin-bottom: 18px; }
  .det .d-sec-title i { color: var(--coral); font-size: 13px; }
  .det .d-sec-title .tag { margin-left: 6px; }

  /* ---------- info grid (label-value) ---------- */
  .det .d-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px 22px; }
  .det .d-grid.cols-2 { grid-template-columns: repeat(2, 1fr); }
  .det .d-item .d-lbl { font-size: 11.5px; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: .3px; margin-bottom: 3px; }
  .det .d-item .d-val { font-size: 14px; color: var(--ink); font-weight: 600; }
  .det .d-item .d-sub { font-size: 12px; color: var(--muted); margin-top: 2px; }

  /* ---------- pills ---------- */
  .det .d-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 700; white-space: nowrap; }
  .det .d-pill.green  { background: transparent; border: 1px solid currentColor;  color: var(--green); }
  .det .d-pill.amber  { background: transparent; border: 1px solid currentColor;  color: #b45309; }
  .det .d-pill.blue   { background: transparent; border: 1px solid currentColor;   color: var(--blue); }
  .det .d-pill.red    { background: transparent; border: 1px solid currentColor;    color: var(--red); }
  .det .d-pill.gray   { background: transparent; border: 1px solid currentColor;   color: var(--gray); }
  .det .d-pill.coral  { background: transparent; border: 1px solid currentColor;  color: var(--coral); }

  /* ---------- buttons ---------- */
  .det .d-btn { display: inline-flex; align-items: center; gap: 6px; border: none; cursor: pointer; border-radius: 11px; padding: 9px 15px; font-size: 12.5px; font-weight: 700; transition: transform .15s ease, filter .15s ease, background-color .15s ease, color .15s ease; }
  .det .d-btn:hover { transform: translateY(-1px); }
  .det .d-btn.coral { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 6px 16px -8px rgba(255,107,107,0.6); }
  .det .d-btn.coral:hover { filter: brightness(1.04); }
  .det .d-btn.amber { background: var(--amber); color: #fff; }
  .det .d-btn.amber:hover { background: #d97706; }
  .det .d-btn.red { background: var(--red); color: #fff; }
  .det .d-btn.red:hover { background: #dc2626; }
  .det .d-btn.green { background: var(--green); color: #fff; }
  .det .d-btn.green:hover { background: #059669; }
  .det .d-btn.blue { background: var(--blue); color: #fff; }
  .det .d-btn.blue:hover { background: #2563eb; }
  .det .d-btn.ghost { background: rgba(255,255,255,0.6); color: var(--ink); box-shadow: 0 2px 10px -8px rgba(26,26,46,0.3); }
  .det .d-btn.ghost:hover { background: #fff; color: var(--coral); }
  .det .d-btn.sm { padding: 6px 11px; font-size: 11.5px; border-radius: 9px; }

  /* ---------- document rows ---------- */
  .det .d-doc { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 14px 4px; border-bottom: 1px solid var(--divider); }
  .det .d-doc:last-child { border-bottom: none; }
  .det .d-doc-info { display: flex; align-items: center; gap: 12px; min-width: 0; }
  .det .d-doc-ic { flex: 0 0 auto; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 15px; background: var(--coral-soft); color: var(--coral); }
  .det .d-doc-name { font-size: 13.5px; font-weight: 600; color: var(--ink); }
  .det .d-doc-file { font-size: 12px; color: var(--muted); }
  .det .d-doc-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }
  .det .d-doc-note { margin-top: 6px; font-size: 12px; color: var(--red); background: var(--red-soft); border: 1px solid rgba(239,68,68,0.2); border-radius: 8px; padding: 6px 10px; }

  /* ---------- reject panel ---------- */
  .det .d-reject { margin-top: 10px; margin-left: 52px; background: var(--red-soft); border: 1px solid rgba(239,68,68,0.2); border-radius: 12px; padding: 12px; }
  .det .d-reject p { font-size: 12px; font-weight: 600; color: var(--red); margin-bottom: 8px; }
  .det .d-reject form { display: flex; gap: 8px; align-items: center; }
  .det .d-reject input { flex: 1; border: 1px solid rgba(26,26,46,0.14); border-radius: 9px; font-size: 13px; padding: 9px 12px; color: var(--ink); background: #fff; }
  .det .d-reject input:focus { outline: none; border-color: var(--red); box-shadow: 0 0 0 3px rgba(239,68,68,0.12); }

  /* ---------- verify (lock + form) ---------- */
  .det .d-lock { display: flex; align-items: flex-start; gap: 10px; padding: 12px 15px; border-radius: 12px; font-size: 13px; margin-bottom: 16px; background: var(--amber-soft); color: #b45309; }
  .det .d-lock i { margin-top: 2px; }
  .det .d-lock b { font-weight: 700; }
  .det .d-verify-form { display: flex; flex-direction: column; gap: 18px; }
  .det .d-verify-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
  .det .d-field { display: flex; flex-direction: column; gap: 6px; }
  .det .d-field label { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .3px; }
  .det .d-input { border: 1px solid rgba(26,26,46,0.14); border-radius: 10px; font-size: 13px; padding: 10px 12px; color: var(--ink); background: rgba(255,255,255,0.55); transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
  .det .d-input:focus { outline: none; border-color: var(--coral); box-shadow: 0 0 0 3px rgba(255,107,107,0.12); background: #fff; }
  .det .d-input.w-44 { width: 100%; }
  .det .d-input.flex-1 { flex: 1; min-width: 180px; }
  .det .d-hint { font-size: 11.5px; color: var(--muted); margin-top: 6px; }
  .det .d-hint code { background: var(--gray-soft); padding: 1px 5px; border-radius: 5px; font-size: 11px; }
  .det .d-verify-foot { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; border-top: 1px solid var(--divider); padding-top: 16px; }

  /* ---------- picker trigger (pengganti <select>) ---------- */
  .det .r-pick {
    display: inline-flex; align-items: center; gap: 8px; flex-wrap: nowrap;
    padding: 9px 4px; border: none; border-bottom: 1px solid rgba(26,26,46,0.18); border-radius: 0;
    font-size: 13px; color: var(--ink); background: transparent; min-width: 200px;
    cursor: pointer; text-align: left; min-height: 38px; max-width: 100%;
    transition: border-color .18s ease, color .18s ease;
  }
  .det .r-pick:hover { border-bottom-color: var(--coral); }
  .det .r-pick:focus { outline: none; border-bottom-color: var(--coral); }
  .det .r-pick .pick-label { flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .det .r-pick .pick-label.is-placeholder { color: var(--muted); }
  .det .r-pick .pick-caret { display: none; }
  .det .r-pick .pick-clear {
    flex: 0 0 auto;
    display: none; align-items: center; justify-content: center;
    width: 18px; height: 18px; border-radius: 6px; background: var(--gray-soft);
    color: var(--gray); cursor: pointer; font-size: 9px; user-select: none;
  }
  .det .r-pick .pick-clear:hover { background: var(--red-soft); color: var(--red); }
  .det .r-pick.has-value .pick-clear { display: inline-flex; }
  .det .r-pick.has-value .pick-label.is-placeholder { display: none; }

  /* ---------- modal picker (Bringova) ---------- */
  .det .picker-backdrop {
    position: fixed; inset: 0; z-index: 80;
    background: rgba(26,26,46,0.32);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    display: none; align-items: flex-start; justify-content: center;
    padding: 80px 16px 16px;
    animation: dPickerFade .18s ease-out;
  }
  .det .picker-backdrop.is-open { display: flex; }
  @keyframes dPickerFade { from { opacity: 0; } to { opacity: 1; } }

  .det .picker-panel {
    width: 100%; max-width: 380px; max-height: min(520px, calc(100vh - 120px));
    display: flex; flex-direction: column;
    background: #fff; border-radius: 18px;
    box-shadow: 0 20px 50px -16px rgba(26,26,46,0.35), 0 0 0 1px rgba(26,26,46,0.06);
    overflow: hidden;
    animation: dPickerPop .22s cubic-bezier(.22,1.2,.36,1);
  }
  @keyframes dPickerPop { from { opacity: 0; transform: translateY(-6px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }

  .det .picker-head { display: flex; align-items: center; gap: 10px; padding: 14px 16px; border-bottom: 1px solid var(--divider); }
  .det .picker-head .picker-title { font-size: 14px; font-weight: 700; color: var(--ink); flex: 1; }
  .det .picker-head .picker-close { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 8px; border: none; background: transparent; color: var(--muted); cursor: pointer; font-size: 12px; transition: background-color .15s ease, color .15s ease; }
  .det .picker-head .picker-close:hover { background: var(--gray-soft); color: var(--ink); }

  .det .picker-search { position: relative; padding: 10px 14px; border-bottom: 1px solid var(--divider); }
  .det .picker-search i { position: absolute; left: 24px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 12px; pointer-events: none; }
  .det .picker-search input { width: 100%; padding: 9px 12px 9px 32px; border: 1px solid rgba(26,26,46,0.14); border-radius: 10px; font-size: 13px; color: var(--ink); background: rgba(255,255,255,0.7); transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
  .det .picker-search input:focus { outline: none; border-color: var(--coral); background: #fff; box-shadow: 0 0 0 3px rgba(255,107,107,0.12); }

  .det .picker-list { flex: 1; overflow-y: auto; padding: 6px 8px; }
  .det .picker-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; font-size: 13px; color: var(--ink); cursor: pointer; user-select: none; transition: background-color .15s ease, color .15s ease; }
  .det .picker-item:hover, .det .picker-item.is-active { background: var(--coral-soft); color: var(--coral); }
  .det .picker-item.is-selected { background: var(--coral); color: #fff; font-weight: 600; }
  .det .picker-item.is-selected:hover { background: var(--coral); }
  .det .picker-item .pi-label { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .det .picker-item .pi-check { font-size: 11px; opacity: 0; }
  .det .picker-item.is-selected .pi-check { opacity: 1; }
  .det .picker-empty { padding: 26px 12px; text-align: center; color: var(--muted); font-size: 12.5px; }
  .det .picker-empty i { display: block; font-size: 20px; margin-bottom: 6px; color: #d3d6de; }

  .det .picker-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 10px 14px; border-top: 1px solid var(--divider); background: rgba(255,255,255,0.5); }
  .det .picker-foot .picker-clear-all { display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px; border-radius: 9px; border: none; background: transparent; color: var(--muted); font-size: 12px; font-weight: 600; cursor: pointer; transition: color .15s ease, background-color .15s ease; }
  .det .picker-foot .picker-clear-all:hover { color: var(--red); background: var(--red-soft); }
  .det .picker-foot .picker-done { display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 9px; border: none; background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; font-size: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 6px 14px -6px rgba(255,107,107,0.55); transition: filter .15s ease, transform .15s ease; }
  .det .picker-foot .picker-done:hover { filter: brightness(1.04); transform: translateY(-1px); }

  /* ---------- payment / history ---------- */
  .det .d-pay-summary { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; padding: 14px 16px; border: 1px solid var(--divider); border-radius: 14px; background: rgba(255,255,255,0.40); }
  .det .d-pay-summary-left { display: flex; align-items: center; gap: 14px; min-width: 0; }
  .det .d-pay-big { font-size: 19px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; }
  .det .d-pay-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
  .det .d-pay-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 4px; border-bottom: 1px solid var(--divider); }
  .det .d-pay-row:last-child { border-bottom: none; }
  .det .d-pay-main { flex: 1; min-width: 0; }
  .det .d-pay-amount { font-size: 14px; font-weight: 700; color: var(--ink); }
  .det .d-pay-sub { font-size: 12px; color: var(--muted); }
  .det .d-pay-note { font-size: 12px; color: var(--gray); background: var(--gray-soft); padding: 6px 10px; border-radius: 8px; margin-top: 4px; }
  .det .d-pay-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
  .det .d-empty { text-align: center; color: var(--muted); font-size: 13px; padding: 18px 0; }

  .det .d-back { margin-top: 28px; }

  /* ---------- custom confirm modal (Bringova) ---------- */
  .det .d-modal-backdrop { position: fixed; inset: 0; z-index: 90; background: rgba(26,26,46,0.36); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; padding: 16px; }
  .det .d-modal-backdrop.is-open { display: flex; }
  .det .d-modal { width: 100%; max-width: 400px; background: #fff; border-radius: 18px; padding: 22px; box-shadow: 0 24px 60px -18px rgba(26,26,46,0.4); animation: dModalPop .2s cubic-bezier(.22,1.2,.36,1); }
  @keyframes dModalPop { from { opacity: 0; transform: scale(0.97) translateY(4px); } to { opacity: 1; transform: scale(1) translateY(0); } }
  .det .d-modal-body { display: flex; align-items: flex-start; gap: 13px; margin-bottom: 18px; }
  .det .d-modal-ic { flex: 0 0 auto; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
  .det .d-modal-ic.green { background: var(--green-soft); color: var(--green); }
  .det .d-modal-ic.amber { background: var(--amber-soft); color: #b45309; }
  .det .d-modal-ic.red { background: var(--red-soft); color: var(--red); }
  .det .d-modal-title { font-size: 15px; font-weight: 700; color: var(--ink); }
  .det .d-modal-msg { font-size: 13px; color: var(--muted); margin-top: 3px; line-height: 1.5; }
  .det .d-modal-actions { display: flex; justify-content: flex-end; gap: 8px; }
  .det .d-modal-actions .d-btn-ghost { background: transparent; color: var(--muted); }
  .det .d-modal-actions .d-btn-ghost:hover { color: var(--ink); }

  /* ---------- toast (Bringova) ---------- */
  .det .d-toast { position: fixed; top: 20px; right: 20px; z-index: 100; display: none; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 600; box-shadow: 0 12px 30px -12px rgba(26,26,46,0.35); max-width: 320px; }
  .det .d-toast.show { display: flex; }
  .det .d-toast.success { background: var(--green); color: #fff; }
  .det .d-toast.error { background: var(--red); color: #fff; }

  /* ---------- responsive ---------- */
  @media (max-width: 900px) {
    .det .d-grid, .det .d-grid.cols-2, .det .d-verify-grid { grid-template-columns: repeat(2, 1fr); }
  }
  @media (max-width: 620px) {
    .det { padding: 20px 16px 32px; }
    .det .d-grid, .det .d-grid.cols-2, .det .d-verify-grid { grid-template-columns: 1fr; }
    .det .d-head-actions { justify-content: flex-start; }
    .det .d-doc, .det .d-pay-row { flex-direction: column; align-items: flex-start; }
    .det .d-doc-actions, .det .d-pay-right { justify-content: flex-start; }
    .det .d-pay-summary-left { flex-wrap: wrap; }
    .det .d-reject { margin-left: 0; }
  }
</style>

<div class="det">
  <div class="d-crumb">
    <a href="{{ route('admin.registrations.index') }}">Pendaftaran</a>
    <span class="sep">/</span>
    <span>Detail Pendaftaran</span>
  </div>

  <div class="flex flex-wrap items-start justify-between gap-4" style="margin-bottom:20px">
    <div>
      <h1 class="d-title">Detail Pendaftaran</h1>
      <p class="d-meta">No. Registrasi: <b>{{ $registration->registration_number }}</b></p>
    </div>
    <div class="d-head-actions">
      <x-status-badge :status="$registration->status" type="registration" class="d-pill" />
      @if ($registration->deadline_at && $registration->status === 'pending')
        @php $hoursRemaining = $registration->getDeadlineHoursRemaining(); @endphp
        <span class="d-pill amber"><x-hi name="clock-01" /> Batas waktu: {{ $registration->deadline_at->format('d M Y H:i') }}@if ($hoursRemaining !== null) ({{ $hoursRemaining }} jam tersisa)@endif</span>
      @endif
      @if ($registration->canceled_at)
        <span class="d-pill gray">Dibatalkan: {{ $registration->canceled_at->format('d M Y H:i') }}</span>
      @endif
      @if ($registration->withdrawn_at)
        <span class="d-pill gray">Mengundurkan diri: {{ $registration->withdrawn_at->format('d M Y H:i') }}</span>
      @endif
      <form action="{{ route('admin.registrations.reset-password', $registration) }}" method="POST" id="resetPasswordForm" class="inline-block">
        @csrf
        <button type="button" onclick="openActionConfirm('reset-password', '{{ addslashes($registration->applicant?->user?->email ?? '') }}')" class="d-btn amber"><x-hi name="key-01" /> Reset Password</button>
      </form>
      @if (! $registration->isAccepted())
      <form action="{{ route('admin.registrations.delete-account', $registration) }}" method="POST" id="deleteAccountForm" class="inline-block">
        @csrf
        <button type="button" onclick="openActionConfirm('delete-account', '{{ addslashes($registration->applicant?->full_name ?? '') }}')" class="d-btn red"><x-hi name="delete-02" /> Hapus Akun</button>
      </form>
      @endif
    </div>
  </div>

  @if (session('success'))
    <div class="d-alert success"><x-hi name="checkmark-circle-02" /><span>{{ session('success') }}</span></div>
  @endif
  @if (session('error'))
    <div class="d-alert error"><x-hi name="alert-02" /><span>{{ session('error') }}</span></div>
  @endif

  {{-- ================== INFORMASI PENDAFTAR ================== --}}
  <div class="d-sec">
    <div class="d-sec-title"><x-hi name="user" /> Informasi Pendaftar</div>
    <div class="d-grid">
      <div class="d-item">
        <div class="d-lbl">Nama Lengkap</div>
        <div class="d-val">{{ $registration->applicant->full_name ?? '-' }}</div>
      </div>
      <div class="d-item">
        <div class="d-lbl">NISN</div>
        <div class="d-val">{{ $registration->applicant->nisn ?? '-' }}</div>
      </div>
      <div class="d-item">
        <div class="d-lbl">Verifikasi NISN</div>
        <div class="d-val">
          @php $vstatus = $registration->applicant->nisn_verification_status ?? null; @endphp
          @if ($vstatus === 'verified')
            <span class="d-pill green"><x-hi name="checkmark" /> Terverifikasi</span>
            @if ($registration->applicant->nisn_verified_at)
              <div class="d-sub">{{ $registration->applicant->nisn_verified_at->format('d M Y H:i') }}</div>
            @endif
          @elseif ($vstatus === 'unavailable')
            <span class="d-pill amber">Menunggu (server NISN tidak dapat diakses)</span>
          @elseif ($vstatus === 'failed')
            <span class="d-pill red">Gagal</span>
          @else
            <span class="d-pill gray">Belum diverifikasi</span>
          @endif
        </div>
        @if ($registration->applicant->nisn_verified_name)
          <div class="d-sub">Nama di Kemendikdasmen: {{ $registration->applicant->nisn_verified_name }}</div>
        @endif
      </div>
      <div class="d-item">
        <div class="d-lbl">NIK</div>
        <div class="d-val">{{ $registration->applicant->nik ?? '-' }}</div>
      </div>
      <div class="d-item">
        <div class="d-lbl">Email</div>
        <div class="d-val">{{ $registration->applicant->user->email ?? '-' }}</div>
      </div>
      <div class="d-item">
        <div class="d-lbl">Password Akun</div>
        <div class="d-val">
          @if ($registration->applicant->user && !empty(session('reset_password_' . $registration->applicant->user->id)))
            <span style="color:var(--green)">{{ session('reset_password_' . $registration->applicant->user->id) }}</span>
            <span class="d-sub">(baru saja direset)</span>
          @else
            <span class="d-pill gray">Tersembunyi</span>
            <span class="d-sub">— klik "Reset Password" untuk membuat password baru</span>
          @endif
        </div>
      </div>
      <div class="d-item">
        <div class="d-lbl">Jenis Kelamin</div>
        <div class="d-val">{{ $registration->applicant->gender ?? '-' }}</div>
      </div>
      <div class="d-item">
        <div class="d-lbl">Tempat/Tanggal Lahir</div>
        <div class="d-val">{{ $registration->applicant->birth_place ?? '-' }}, {{ $registration->applicant->birth_date ? $registration->applicant->birth_date->format('d M Y') : '-' }}</div>
      </div>
    </div>
  </div>

  {{-- ================== PILIHAN SEKOLAH & JURUSAN ================== --}}
  <div class="d-sec">
    <div class="d-sec-title"><x-hi name="school" /> Pilihan Sekolah &amp; Jurusan</div>
    <div class="d-grid cols-2">
      <div class="d-item">
        <div class="d-lbl">Sekolah</div>
        <div class="d-val">{{ $registration->school->name ?? '-' }}</div>
      </div>
      <div class="d-item">
        <div class="d-lbl">Jalur</div>
        <div class="d-val">{{ $registration->registrationTrack->name ?? '-' }}</div>
      </div>
      <div class="d-item">
        <div class="d-lbl">Jurusan Pilihan</div>
        <div class="d-val">{{ $registration->major->name ?? '-' }}</div>
      </div>
      <div class="d-item">
        <div class="d-lbl">Jurusan Diterima</div>
        <div class="d-val">{{ $registration->finalMajor->name ?? '-' }}</div>
      </div>
    </div>
  </div>

  {{-- ================== VERIFIKASI DOKUMEN ================== --}}
  <div class="d-sec">
    <div class="d-sec-title"><x-hi name="folder-open" /> Verifikasi Dokumen</div>
    @forelse ($registration->documents as $doc)
      <div class="d-doc" id="doc-row-{{ $doc->id }}">
        <div class="d-doc-info">
          <span class="d-doc-ic"><x-hi name="file-01" /></span>
          <div style="min-width:0">
            <div class="d-doc-name">{{ $doc->document_type }}</div>
            <div class="d-doc-file">{{ $doc->file_name }}</div>
            @if($doc->verification_notes)
              <div class="d-doc-note"><x-hi name="alert-02" /> Alasan: {{ $doc->verification_notes }}</div>
            @endif
          </div>
        </div>
        <div class="d-doc-actions" id="doc-actions-{{ $doc->id }}">
          <button type="button" onclick="showFileModal('{{ route('registration.documents.download', [$registration, $doc]) }}', '{{ addslashes($doc->file_name) }}')" class="d-btn ghost sm"><x-hi name="view" /> Lihat</button>
          @if($doc->verified_at)
            <span id="doc-badge-{{ $doc->id }}" class="d-pill green"><x-hi name="checkmark" /> Terverifikasi</span>
            <span id="doc-verify-btns-{{ $doc->id }}" class="hidden items-center gap-2">
              <button type="button" onclick="openDocVerifyModal({{ $doc->id }}, '{{ addslashes($doc->document_type) }}')" class="d-btn green sm">✓ Verifikasi</button>
              <button type="button" onclick="toggleDocReject({{ $doc->id }})" class="d-btn red sm">✕ Tolak</button>
            </span>
            <span id="doc-verified-btns-{{ $doc->id }}" class="inline-flex items-center gap-2">
              <button type="button" onclick="openDocUnverifyModal({{ $doc->id }}, '{{ addslashes($doc->document_type) }}')" class="d-btn amber sm">↩ Batal Verifikasi</button>
            </span>
          @elseif($doc->verification_notes)
            <span id="doc-badge-{{ $doc->id }}" class="d-pill red">Ditolak</span>
            <span id="doc-verify-btns-{{ $doc->id }}" class="hidden items-center gap-2"></span>
            <span id="doc-verified-btns-{{ $doc->id }}" class="hidden"></span>
          @else
            <span id="doc-badge-{{ $doc->id }}" class="d-pill amber">Menunggu</span>
            <span id="doc-verify-btns-{{ $doc->id }}" class="inline-flex items-center gap-2">
              <button type="button" onclick="openDocVerifyModal({{ $doc->id }}, '{{ addslashes($doc->document_type) }}')" class="d-btn green sm">✓ Verifikasi</button>
              <button type="button" onclick="toggleDocReject({{ $doc->id }})" class="d-btn red sm">✕ Tolak</button>
            </span>
            <span id="doc-verified-btns-{{ $doc->id }}" class="hidden items-center gap-2">
              <button type="button" onclick="openDocUnverifyModal({{ $doc->id }}, '{{ addslashes($doc->document_type) }}')" class="d-btn amber sm">↩ Batal Verifikasi</button>
            </span>
          @endif
        </div>
      </div>
      @if(!$doc->verified_at && !$doc->verification_notes)
        <div id="doc-reject-{{ $doc->id }}" class="d-reject hidden">
          <p><x-hi name="alert-02" /> Tolak dokumen — beri alasan (file akan dihapus):</p>
          <form action="{{ route('admin.documents.reject', $doc) }}" method="POST">
            @csrf
            @method('PATCH')
            <input type="text" name="verification_notes" placeholder="Alasan penolakan (wajib)" required maxlength="500">
            <button type="submit" class="d-btn red sm">Kirim</button>
            <button type="button" onclick="toggleDocReject({{ $doc->id }})" class="d-btn ghost sm">Batal</button>
          </form>
        </div>
      @endif
    @empty
      <div class="d-empty"><x-hi name="folder-open" style="display:block;font-size:20px;margin-bottom:6px;color:#d3d6de" />Belum ada dokumen</div>
    @endforelse
  </div>

  {{-- ================== VERIFIKASI PENDAFTARAN ================== --}}
  <div class="d-sec">
    <div class="d-sec-title"><x-hi name="file-security" /> Verifikasi Pendaftaran</div>
    @if ($registration->status === 'pending' || $registration->status === 'rejected')
      @php
        $docsVerified = $registration->hasAllDocumentsVerified();
        $requiredDocs = $registration->requiredDocumentTypes();
      @endphp
      <div id="docVerifyLock" class="d-lock {{ $docsVerified ? 'hidden' : '' }}">
        <x-hi name="alert-02" />
        <span>Verifikasi pendaftaran terkunci sampai <b>semua dokumen wajib</b> diverifikasi. Dokumen diverifikasi satu per satu di bagian Verifikasi Dokumen di atas.</span>
      </div>
      @php
        $isRegulerVerify = strtolower($registration->registrationTrack->name ?? '') === 'reguler';
        $verifyFee = $registration->payment_amount;
        if ($verifyFee === null && $isRegulerVerify) {
          $raw = \App\Models\Setting::get('fee_' . ($registration->registrationPeriod->school_level_id ?? '') . '_' . $registration->registration_track_id);
          $verifyFee = ($raw !== null && $raw !== '' && is_numeric($raw)) ? (float) $raw : 500000;
        }
      @endphp
      <form action="{{ route('admin.registrations.verify', $registration) }}" method="POST" class="d-verify-form">
        @csrf
        <div class="d-verify-grid">
          <div class="d-field">
            <label>Status Verifikasi</label>
            <button type="button" class="r-pick" data-picker="verify_status" aria-haspopup="listbox" aria-expanded="false">
              <span class="pick-label is-placeholder">Pilih status…</span>
              <span class="pick-clear" data-clear="verify_status" role="button" tabindex="0" aria-label="Bersihkan"><x-hi name="cancel-01" /></span>
              <x-hi name="arrow-down-01" />
            </button>
            <input type="hidden" name="status" data-picker-input="verify_status" value="verified">
          </div>
          @if(!$isRegulerVerify)
            <div class="d-field">
              <label>Biaya Pendaftaran (Rp)</label>
              <input type="number" name="payment_amount" value="{{ old('payment_amount', $verifyFee) }}" min="0" step="1000" placeholder="0 = gratis" class="d-input w-44">
            </div>
          @else
            <div class="d-field">
              <label>Biaya</label>
              <div class="d-input" style="display:flex;align-items:center;height:38px;border:none;padding:0 2px">
                <span style="font-size:14px;font-weight:700;color:var(--ink)">Rp {{ number_format($verifyFee, 0, ',', '.') }}</span>
                <span class="d-sub" style="margin-left:8px">(otomatis dari Setting)</span>
              </div>
            </div>
          @endif
        </div>
        <div class="d-field">
          <label>Catatan Verifikasi</label>
          <input type="text" name="verified_notes" placeholder="Catatan verifikasi" class="d-input flex-1">
        </div>
        <div class="d-verify-foot">
          @if(!$isRegulerVerify)
            <p class="d-hint" style="margin:0">Isi nominal per siswa (tiap siswa bisa beda). Isi <code>0</code> untuk gratis → langsung lunas tanpa siswa bayar.</p>
          @else
            <p class="d-hint" style="margin:0">Biaya Reguler otomatis dari menu Setting. Tidak perlu input manual.</p>
          @endif
          <button type="submit" class="d-btn coral"><x-hi name="save" /> Simpan</button>
        </div>
      </form>
    @else
      <p class="d-val" style="font-size:13.5px;color:var(--ink)">Diverifikasi oleh <b>{{ $registration->verifiedBy->name ?? '-' }}</b>
        @if($registration->verified_notes) — {{ $registration->verified_notes }} @endif
      </p>
    @endif
  </div>

  @if ($registration->status === 'verified')
  <div class="d-sec">
    <div class="d-sec-title"><x-hi name="checkmark-circle-01" /> Status Diterima (Otomatis)</div>
    <div class="d-lock" style="background:var(--blue-soft);color:var(--blue)">
      <x-hi name="information-circle" />
      <span>Siswa otomatis terdaftar sebagai siswa setelah <b>berkas diverifikasi</b> dan <b>pembayaran lunas</b>. NIS diterbitkan otomatis saat itu.</span>
    </div>
  </div>
  @endif

  {{-- ================== STATUS PEMBAYARAN ================== --}}
  <div class="d-sec">
    <div class="d-sec-title"><x-hi name="money-02" /> Status Pembayaran</div>
    @php
      $payPill = [
        'unpaid' => 'gray', 'pending' => 'amber', 'paid' => 'green', 'failed' => 'red',
      ];
    @endphp
    @php
      $pendingPayment = $registration->payments->where('status', 'pending')->sortByDesc('id')->first();
      $proofPayment = $registration->payments->filter(fn ($p) => !empty($p->proof_file))->sortByDesc('id')->first();
      $invoicePayment = $latestVerifiedPayment ?? $registration->payments->whereNotNull('invoice_pdf')->sortByDesc('id')->first();
    @endphp
    <div class="d-pay-summary">
      <div class="d-pay-summary-left">
        <span class="d-pill {{ $payPill[$registration->payment_status] ?? 'gray' }}" style="font-size:12.5px;padding:6px 14px">{{ ucfirst($registration->payment_status) }}</span>
        @if ($registration->payment_amount !== null)
          <span class="d-pay-big">Rp {{ number_format($registration->payment_amount, 0, ',', '.') }}</span>
        @else
          <span class="d-sub">Belum ditentukan — akan muncul setelah Terverifikasi</span>
        @endif
      </div>
      <div class="d-pay-actions">
        @if($invoicePayment)
          <a href="{{ route('payments.invoice.view', $invoicePayment) }}" target="_blank" class="d-btn green sm"><x-hi name="invoice-01" /> Lihat Invoice</a>
        @endif
        @if($proofPayment)
          <button type="button" onclick="showFileModal('{{ route('payments.proof', $proofPayment) }}', '{{ addslashes($proofPayment->proof_file ? basename($proofPayment->proof_file) : 'Bukti Pembayaran') }}')" class="d-btn blue sm"><x-hi name="receipt-text" /> Lihat Bukti</button>
        @endif
        @if($pendingPayment)
          <form action="{{ route('admin.payments.verify', $pendingPayment) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="d-btn green sm"><x-hi name="checkmark-badge-01" /> Verifikasi Pembayaran</button>
          </form>
        @endif
        @if(!$pendingPayment && !$invoicePayment && !$proofPayment)
          <span class="d-sub">Belum ada pembayaran untuk diverifikasi.</span>
        @endif
      </div>
    </div>
  </div>

  {{-- ================== RIWAYAT PEMBAYARAN ================== --}}
  @php
    $successPayments = $registration->payments->filter(fn ($p) => ! \App\Models\Payment::isAbandonedOnline($p))->sortByDesc('created_at');
    $hiddenInvoicesAdmin = $registration->payments->filter(fn ($p) => \App\Models\Payment::isAbandonedOnline($p))->count();
  @endphp
  <div class="d-sec">
    <div class="d-sec-title"><x-hi name="work-history" /> Riwayat Pembayaran</div>
    @if($successPayments->isEmpty())
      <div class="d-empty"><x-hi name="credit-card" style="display:block;font-size:20px;margin-bottom:6px;color:#d3d6de" />Belum ada riwayat pembayaran</div>
    @else
      @foreach ($successPayments as $payment)
        <div class="d-pay-row">
          <div class="d-pay-main">
            <div class="d-pay-amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
            <div class="d-pay-sub">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }} · {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? '-')) }}</div>
            @if($payment->payment_method === 'online' && $payment->xendit_payment_method)
              <div class="d-pay-sub">Channel: <b>{{ \App\Services\XenditService::friendlyXenditMethod($payment->xendit_payment_method) }}</b> ({{ $payment->xendit_payment_method }}) via Xendit</div>
            @endif
            @if($payment->invoice_pdf)
              <a href="{{ route('payments.invoice', $payment) }}" target="_blank" class="d-pay-sub" style="color:var(--blue);text-decoration:underline">Invoice (PDF) →</a>
            @endif
            @if($payment->notes)
              <div class="d-pay-note">{{ $payment->notes }}</div>
            @endif
            @if($payment->xendit_paid_at)
              <div class="d-pay-sub" style="margin-top:4px">Dibayar: {{ $payment->xendit_paid_at->format('d M Y H:i') }}</div>
            @endif
          </div>
          <div class="d-pay-right">
            @if ($payment->proof_file)
              <button type="button" onclick="showFileModal('{{ route('payments.proof', $payment) }}', '{{ addslashes(basename($payment->proof_file)) }}')" class="d-btn blue sm">Lihat Bukti</button>
            @endif
            @php $adminPaymentLabels = ['pending' => 'Pending', 'verified' => 'Lunas', 'rejected' => 'Ditolak']; @endphp
            @php $payPillFor = ['pending' => 'amber', 'verified' => 'green', 'rejected' => 'red']; @endphp
            <span class="d-pill {{ $payPillFor[$payment->status] ?? 'gray' }}">{{ $adminPaymentLabels[$payment->status] ?? ucfirst($payment->status) }}</span>
          </div>
        </div>
      @endforeach
      @if($hiddenInvoicesAdmin > 0)
        <p class="d-sub" style="margin-top:8px">{{ $hiddenInvoicesAdmin }} invoice online yang tidak dilanjutkan disembunyikan.</p>
      @endif
    @endif
  </div>

  <div class="d-back">
    <a href="{{ route('admin.registrations.index') }}" class="d-btn ghost"><x-hi name="arrow-left-01" /> Kembali</a>
  </div>

{{-- ============================================================
     Modal Picker (Bringova) — reuse global picker system (verify_status)
     ============================================================ --}}
<div id="pickerBackdrop" class="picker-backdrop" aria-hidden="true">
  <div class="picker-panel" role="dialog" aria-modal="true" aria-labelledby="pickerTitle">
    <div class="picker-head">
      <div class="picker-title" id="pickerTitle">Pilih item</div>
      <button type="button" class="picker-close" onclick="closePicker()" aria-label="Tutup"><x-hi name="cancel-01" /></button>
    </div>
    <div class="picker-search">
      <x-hi name="search-01" />
      <input id="pickerSearch" type="search" placeholder="Cari…" autocomplete="off">
    </div>
    <div class="picker-list" id="pickerList" role="listbox"></div>
    <div class="picker-foot">
      <button type="button" class="picker-clear-all" onclick="clearCurrentPicker()"><x-hi name="eraser-01" /> Bersihkan</button>
      <button type="button" class="picker-done" onclick="closePicker()">Selesai</button>
    </div>
  </div>
</div>

@php
  $pickVerifyStatus = [
    ['v' => 'verified', 'l' => 'Verifikasi (Terima Berkas)'],
    ['v' => 'rejected', 'l' => 'Tolak'],
  ];
  $pickerJson = ['verify_status' => $pickVerifyStatus];
  $pickerLabels = ['verify_status' => 'Pilih Status Verifikasi'];
@endphp

<div id="reg-data" hidden data-picker='@json($pickerJson)' data-picker-labels='@json($pickerLabels)'></div>

{{-- ================== MODAL KONFIRMASI DOKUMEN (Bringova) ================== --}}
<div id="docConfirmModal" class="d-modal-backdrop" aria-hidden="true">
  <div class="d-modal" role="dialog" aria-modal="true">
    <div class="d-modal-body">
      <div id="docConfirmIcon" class="d-modal-ic green">✓</div>
      <div style="flex:1;min-width:0">
        <h3 id="docConfirmTitle" class="d-modal-title"></h3>
        <p id="docConfirmMessage" class="d-modal-msg"></p>
      </div>
    </div>
    <div class="d-modal-actions">
      <button type="button" onclick="closeDocConfirmModal()" class="d-btn ghost d-btn-ghost">Batal</button>
      <button type="button" id="docConfirmAction" class="d-btn green">Ya</button>
    </div>
  </div>
</div>

{{-- ================== MODAL KONFIRMASI AKSI GENERIC (Reset Password / Hapus Akun, Bringova) ================== --}}
<div id="actionConfirmModal" class="d-modal-backdrop" aria-hidden="true">
  <div class="d-modal" role="dialog" aria-modal="true">
    <div class="d-modal-body">
      <div id="actionConfirmIcon" class="d-modal-ic amber"><x-hi name="key-01" /></div>
      <div style="flex:1;min-width:0">
        <h3 id="actionConfirmTitle" class="d-modal-title"></h3>
        <p id="actionConfirmMessage" class="d-modal-msg"></p>
      </div>
    </div>
    <div class="d-modal-actions">
      <button type="button" onclick="closeActionConfirm()" class="d-btn ghost d-btn-ghost">Batal</button>
      <button type="button" id="actionConfirmAction" class="d-btn amber">Ya</button>
    </div>
  </div>
</div>

<div id="docToast" class="d-toast"></div>
</div>

<script>
function toggleDocReject(id) {
  var el = document.getElementById('doc-reject-' + id);
  if (!el) return;
  el.classList.toggle('hidden');
}

(function () {
  var pendingDocId = null;
  var pendingAction = null; // 'verify' | 'unverify'
  var csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

  function getToken() {
    var m = document.querySelector('meta[name="csrf-token"]');
    return (m && m.content) || csrf;
  }

  function showToast(msg, isError) {
    var t = document.getElementById('docToast');
    t.textContent = msg;
    t.className = 'd-toast show ' + (isError ? 'error' : 'success');
    setTimeout(function () { t.className = 'd-toast'; }, 3000);
  }

  window.docShowToast = showToast;

  function openDocConfirmModal() {
    var m = document.getElementById('docConfirmModal');
    m.classList.add('is-open');
    m.setAttribute('aria-hidden', 'false');
  }

  window.openDocVerifyModal = function (id, docType) {
    pendingDocId = id;
    pendingAction = 'verify';
    document.getElementById('docConfirmTitle').textContent = 'Verifikasi dokumen?';
    document.getElementById('docConfirmMessage').textContent = 'Verifikasi dokumen "' + docType + '"? Dokumen akan ditandai Terverifikasi.';
    var icon = document.getElementById('docConfirmIcon');
    icon.textContent = '✓';
    icon.className = 'd-modal-ic green';
    var btn = document.getElementById('docConfirmAction');
    btn.textContent = 'Ya, Verifikasi';
    btn.className = 'd-btn green';
    openDocConfirmModal();
  };

  window.openDocUnverifyModal = function (id, docType) {
    pendingDocId = id;
    pendingAction = 'unverify';
    document.getElementById('docConfirmTitle').textContent = 'Batalkan verifikasi?';
    document.getElementById('docConfirmMessage').textContent = 'Batalkan verifikasi dokumen "' + docType + '"? Status akan kembali menjadi Menunggu.';
    var icon = document.getElementById('docConfirmIcon');
    icon.textContent = '↩';
    icon.className = 'd-modal-ic amber';
    var btn = document.getElementById('docConfirmAction');
    btn.textContent = 'Ya, Batalkan';
    btn.className = 'd-btn amber';
    openDocConfirmModal();
  };

  window.closeDocConfirmModal = function () {
    var m = document.getElementById('docConfirmModal');
    m.classList.remove('is-open');
    m.setAttribute('aria-hidden', 'true');
    pendingDocId = null;
    pendingAction = null;
  };

  document.getElementById('docConfirmModal').addEventListener('click', function (e) {
    if (e.target === this) closeDocConfirmModal();
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      if (document.getElementById('docConfirmModal').classList.contains('is-open')) closeDocConfirmModal();
    }
  });

  document.getElementById('docConfirmAction').addEventListener('click', function () {
    if (!pendingDocId || !pendingAction) return;
    var id = pendingDocId;
    var action = pendingAction;
    closeDocConfirmModal();
    var url = action === 'verify'
      ? '{{ url('admin/documents') }}/' + id + '/verify'
      : '{{ url('admin/documents') }}/' + id + '/unverify';
    var btn = document.getElementById('docConfirmAction');

    fetch(url, {
      method: 'PATCH',
      headers: {
        'X-CSRF-TOKEN': getToken(),
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    }).then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); })
    .then(function (res) {
      if (!res.ok || !res.body.success) {
        showToast(res.body.message || 'Gagal memproses dokumen', true);
        return;
      }
      showToast(res.body.message || (action === 'verify' ? 'Dokumen diverifikasi' : 'Verifikasi dibatalkan'), false);
      applyDocState(id, action === 'verify' ? 'verified' : 'pending', res.body);
    }).catch(function () {
      showToast('Terjadi kesalahan jaringan', true);
    });
  });

  function applyDocState(docId, state, payload) {
    var badge = document.getElementById('doc-badge-' + docId);
    var verifyBtns = document.getElementById('doc-verify-btns-' + docId);
    var verifiedBtns = document.getElementById('doc-verified-btns-' + docId);
    var rejectPanel = document.getElementById('doc-reject-' + docId);
    if (rejectPanel) rejectPanel.classList.add('hidden');

    if (state === 'verified') {
      if (badge) { badge.textContent = 'Terverifikasi'; badge.className = 'd-pill green'; }
      if (verifyBtns) { verifyBtns.classList.add('hidden'); verifyBtns.classList.remove('inline-flex'); }
      if (verifiedBtns) { verifiedBtns.classList.remove('hidden'); verifiedBtns.classList.add('inline-flex'); }
    } else {
      if (badge) { badge.textContent = 'Menunggu'; badge.className = 'd-pill amber'; }
      if (verifyBtns) { verifyBtns.classList.remove('hidden'); verifyBtns.classList.add('inline-flex'); }
      if (verifiedBtns) { verifiedBtns.classList.add('hidden'); verifiedBtns.classList.remove('inline-flex'); }
    }

    var lock = document.getElementById('docVerifyLock');
    if (lock && payload) {
      var hasAll = payload.has_all_required_verified;
      if (hasAll === true || hasAll === 1) {
        lock.classList.add('hidden');
      } else if (hasAll === false || hasAll === 0) {
        lock.classList.remove('hidden');
      }
    }
  }
})();

/* ===== Modal konfirmasi aksi generic (Reset Password / Hapus Akun) ===== */
(function () {
  var modal = document.getElementById('actionConfirmModal');
  var pendingAction = null; // 'reset-password' | 'delete-account'

  function openActionConfirm(action, targetName) {
    pendingAction = action;
    var icon = document.getElementById('actionConfirmIcon');
    var title = document.getElementById('actionConfirmTitle');
    var msg = document.getElementById('actionConfirmMessage');
    var btn = document.getElementById('actionConfirmAction');

    if (action === 'reset-password') {
      icon.className = 'd-modal-ic amber';
      icon.innerHTML = hiSvg('key-01');
      title.textContent = 'Reset password?';
      msg.textContent = 'Yakin ingin mereset password akun ' + (targetName || 'siswa') + '? Password baru akan dikirim ke email siswa.';
      btn.textContent = 'Ya, Reset';
      btn.className = 'd-btn amber';
    } else {
      icon.className = 'd-modal-ic red';
      icon.innerHTML = hiSvg('delete-02');
      title.textContent = 'Hapus akun?';
      msg.textContent = 'Hapus akun siswa ' + (targetName || 'ini') + '? Seluruh data pendaftaran dan pembayarannya akan ikut terhapus permanen.';
      btn.textContent = 'Ya, Hapus';
      btn.className = 'd-btn red';
    }

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
  }

  window.openActionConfirm = openActionConfirm;

  window.closeActionConfirm = function () {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    pendingAction = null;
  };

  modal.addEventListener('click', function (e) {
    if (e.target === this) closeActionConfirm();
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal.classList.contains('is-open')) closeActionConfirm();
  });

  document.getElementById('actionConfirmAction').addEventListener('click', function () {
    if (!pendingAction) return;
    var form = document.getElementById(pendingAction === 'reset-password' ? 'resetPasswordForm' : 'deleteAccountForm');
    closeActionConfirm();
    if (form) form.submit();
  });
})();
</script>
@endsection
<x-student-layout title="Detail Pendaftaran">
  @php
    $isStudentView = !isset($isAdmin) || !$isAdmin;
    $isWithdrawn = $registration->status === 'withdrawn';
  @endphp
  <style>
    .det { --coral:#FF6B6B; --coral-2:#FF8E6E; --coral-soft:#FFE5E3; --ink:#1a1a2e; --muted:#8a8f9d; --divider:rgba(26,26,46,.10); --green:#10B981; --green-soft:#D1FAE5; --red:#EF4444; --red-soft:#FEE2E2; --amber:#D97706; --amber-soft:#FEF3C7; --blue:#2563EB; --blue-soft:#DBEAFE; --indigo:#6366F1; --indigo-soft:#E0E7FF; position:relative; border-radius:24px; padding:28px 28px 72px; background:#f6f7fb; }
    .det .det-inner { width:100%; max-width:1000px; margin:0 auto; }
    .det-crumb { font-size:12.5px; color:var(--muted); margin-bottom:6px; display:flex; align-items:center; gap:7px; flex-wrap:wrap; }
    .det-crumb a { color:var(--coral); font-weight:600; } .det-crumb a:hover { text-decoration:underline; }
    .det-title { font-size:26px; font-weight:800; color:var(--ink); letter-spacing:-0.01em; line-height:1.2; }
    .det-meta { font-size:13px; color:var(--muted); margin-top:6px; }

    .det-regnum { display:flex; align-items:center; gap:14px; flex-wrap:wrap; margin-top:18px; padding:16px 18px; border-top:1px solid var(--divider); border-bottom:1px solid var(--divider); }
    .det-regnum-ic { width:44px; height:44px; border-radius:13px; background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; display:flex; align-items:center; justify-content:center; font-size:18px; box-shadow:0 10px 20px -10px rgba(255,107,107,.6); flex:0 0 auto; }
    .det-regnum-num { font-size:18px; font-weight:800; color:var(--ink); letter-spacing:.01em; }
    .det-regnum-sub { font-size:12px; color:var(--muted); margin-top:1px; }
    .det-regnum-badge { margin-left:auto; }

    /* alert */
    .det-alert { display:flex; gap:13px; align-items:flex-start; border-radius:14px; padding:14px 16px; margin-top:20px; border:1px solid transparent; }
    .det-alert i.det-alert-ic { width:22px; height:22px; border-radius:7px; display:flex; align-items:center; justify-content:center; font-size:11px; flex:0 0 auto; margin-top:1px; }
    .det-alert .det-alert-body { flex:1; min-width:0; }
    .det-alert .det-alert-t { font-weight:700; font-size:13.5px; }
    .det-alert .det-alert-p { font-size:13px; margin-top:2px; opacity:.92; }
    .det-alert.red { background:var(--red-soft); border-color:rgba(239,68,68,.25); }
    .det-alert.red i.det-alert-ic { background:var(--red); color:#fff; }
    .det-alert.red .det-alert-t, .det-alert.red .det-alert-p { color:#B91C1C; }
    .det-alert.amber { background:var(--amber-soft); border-color:rgba(217,119,6,.3); }
    .det-alert.amber i.det-alert-ic { background:var(--amber); color:#fff; }
    .det-alert.amber .det-alert-t, .det-alert.amber .det-alert-p { color:#B45309; }
    .det-alert.info { background:var(--blue-soft); border-color:rgba(37,99,235,.25); }
    .det-alert.info i.det-alert-ic { background:var(--blue); color:#fff; }
    .det-alert.info .det-alert-t, .det-alert.info .det-alert-p { color:#1D4ED8; }
    .det-alert.green { background:var(--green-soft); border-color:rgba(16,185,129,.3); }
    .det-alert.green i.det-alert-ic { background:var(--green); color:#fff; }
    .det-alert.green .det-alert-t, .det-alert.green .det-alert-p { color:#047857; }

    /* section */
    .det-sec { border-top:1px solid var(--divider); padding:26px 0 6px; }
    .det-sec:first-of-type { border-top:none; padding-top:24px; }
    .det-sec-head { display:flex; align-items:center; gap:12px; margin-bottom:14px; flex-wrap:wrap; }
    .det-sec-ic { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:17px; flex:0 0 auto; }
    .det-sec-ic.coral { background:var(--coral-soft); color:var(--coral); }
    .det-sec-ic.blue { background:var(--blue-soft); color:var(--blue); }
    .det-sec-ic.amber { background:var(--amber-soft); color:var(--amber); }
    .det-sec-ic.green { background:var(--green-soft); color:var(--green); }
    .det-sec-ic.red { background:var(--red-soft); color:var(--red); }
    .det-sec-ttl { font-size:14px; font-weight:800; color:var(--ink); }
    .det-sec-desc { font-size:12px; color:var(--muted); margin-top:1px; }

    /* fields grid */
    .det-fields { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px 28px; }
    .det-field .det-f-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }
    .det-field .det-f-val { margin-top:3px; font-size:14px; font-weight:600; color:var(--ink); line-height:1.45; }
    .det-field .det-f-val.empty { color:var(--muted); font-weight:500; }

    /* status blocks */
    .det-status-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; }
    .det-status { border:1px solid var(--divider); border-left:3px solid var(--coral); border-radius:16px; padding:18px 20px; background:transparent; }
    .det-status.pay { border-left-color:var(--blue); }
    .det-status-hd { display:flex; align-items:center; gap:10px; margin-bottom:14px; padding-bottom:12px; border-bottom:1px solid var(--divider); }
    .det-status-ic { width:38px; height:38px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:15px; flex:0 0 auto; background:var(--coral-soft); color:var(--coral); }
    .det-status.pay .det-status-ic { background:var(--blue-soft); color:var(--blue); }
    .det-status-ttl { font-size:13px; font-weight:800; color:var(--ink); }
    .det-status-line { display:flex; align-items:flex-start; gap:9px; margin-top:8px; font-size:13px; color:var(--ink); }
    .det-status-line i { color:var(--coral); font-size:12px; margin-top:2px; flex:0 0 auto; width:14px; text-align:center; }
    .det-status.pay .det-status-line i { color:var(--blue); }
    .det-status-line b { font-weight:700; }
    .det-status .det-btn { margin-top:14px; }

    /* pills */
    .det-pill { display:inline-flex; align-items:center; gap:6px; padding:4px 11px; border-radius:99px; font-size:11px; font-weight:700; white-space:nowrap; }
    .det-pill.green { background:var(--green-soft); color:#047857; }
    .det-pill.amber { background:var(--amber-soft); color:#B45309; }
    .det-pill.red { background:var(--red-soft); color:#B91C1C; }
    .det-pill.blue { background:var(--blue-soft); color:#1D4ED8; }
    .det-pill.gray { background:#F3F4F6; color:var(--gray, #6b7280); }
    .det-pill.coral { background:var(--coral-soft); color:var(--coral); }

    /* buttons */
    .det-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:13px 20px; border-radius:12px; font-size:14px; font-weight:700; transition:transform .15s, box-shadow .15s, background .15s; min-height:44px; cursor:pointer; border:none; text-decoration:none; }
    .det-btn.coral { background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; box-shadow:0 10px 22px -10px rgba(255,107,107,.65); }
    .det-btn.coral:hover { transform:translateY(-1px); box-shadow:0 14px 26px -10px rgba(255,107,107,.7); }
    .det-btn.ghost { background:transparent; color:var(--coral); border:1.5px solid var(--coral); }
    .det-btn.ghost:hover { background:var(--coral-soft); }
    .det-btn.red { background:var(--red-soft); color:var(--red); }
    .det-btn.red:hover { background:var(--red); color:#fff; }
    .det-btn.green { background:var(--green); color:#fff; }
    .det-btn.green:hover { transform:translateY(-1px); }
    .det-btn.sm { padding:9px 15px; font-size:12.5px; border-radius:10px; min-height:38px; }
    .det-btn:disabled { background:var(--muted); color:#fff; box-shadow:none; cursor:not-allowed; opacity:.55; transform:none; }

    /* fixed action bar */
    .det-bar { position:fixed; left:0; right:0; bottom:0; z-index:50; background:rgba(246,247,251,.94); backdrop-filter:blur(6px); border-top:1px solid var(--divider); padding:12px 16px; }
    .det-bar-inner { max-width:1000px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; gap:12px; }

    /* progress */
    .det-progress { margin-top:6px; }
    .det-progress-head { display:flex; align-items:center; justify-content:space-between; gap:10px; font-size:13px; color:var(--ink); font-weight:600; margin-bottom:8px; }
    .det-progress-head .det-progress-pct { font-weight:800; color:var(--coral); }
    .det-progress-track { height:8px; border-radius:99px; background:rgba(26,26,46,.08); overflow:hidden; }
    .det-progress-fill { height:100%; border-radius:99px; background:linear-gradient(90deg,var(--green),#34D399); transition:width .3s ease; }

    /* document upload card */
    .det-doc { padding:18px 4px; border-top:1px solid var(--divider); }
    .det-doc:first-of-type { border-top:none; padding-top:4px; }
    .det-doc-head { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
    .det-doc-label { font-size:14px; font-weight:800; color:var(--ink); }
    .det-doc-sub { font-size:12px; color:var(--muted); margin-top:2px; }
    .det-doc-head .det-pill { margin-left:auto; }

    .det-file-row { display:flex; align-items:flex-start; gap:11px; padding:12px 4px; border-bottom:1px solid var(--divider); }
    .det-file-row:last-of-type { border-bottom:none; }
    .det-file-ic { width:38px; height:38px; border-radius:11px; background:#F3F4F6; color:var(--gray); display:flex; align-items:center; justify-content:center; font-size:15px; flex:0 0 auto; }
    .det-file-info { flex:1; min-width:0; }
    .det-file-name { font-size:13px; font-weight:600; color:var(--blue); background:none; border:none; padding:0; cursor:pointer; text-align:left; word-break:break-word; }
    .det-file-name:hover { text-decoration:underline; }
    .det-file-status { display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-top:6px; }
    .det-note-red { display:block; margin-top:6px; font-size:12px; color:var(--red); background:var(--red-soft); padding:8px 10px; border-radius:9px; }
    .det-file-actions { display:flex; align-items:center; gap:8px; flex-shrink:0; flex-wrap:wrap; justify-content:flex-end; }

    .det-mini { display:inline-flex; align-items:center; gap:5px; padding:7px 12px; border-radius:9px; font-size:11.5px; font-weight:700; cursor:pointer; border:none; transition:background .15s, color .15s; }
    .det-mini.green { background:var(--green-soft); color:#047857; } .det-mini.green:hover { background:var(--green); color:#fff; }
    .det-mini.red { background:var(--red-soft); color:var(--red); } .det-mini.red:hover { background:var(--red); color:#fff; }
    .det-mini.ghost { background:#F3F4F6; color:var(--gray); } .det-mini.ghost:hover { background:var(--gray-soft); }

    /* drop zone */
    .det-drop { position:relative; margin-top:12px; border:1.5px dashed rgba(26,26,46,.22); border-radius:14px; padding:20px 16px; display:flex; align-items:center; justify-content:center; text-align:center; cursor:pointer; transition:border-color .15s, background .15s; background:rgba(255,255,255,.4); }
    .det-drop-inner { display:flex; flex-direction:column; align-items:center; }
    .det-drop:hover, .det-drop.over { border-color:var(--coral); background:var(--coral-soft); }
    .det-drop-ic { width:42px; height:42px; border-radius:12px; background:var(--coral-soft); color:var(--coral); display:flex; align-items:center; justify-content:center; font-size:16px; margin:0 auto 8px; }
    .det-drop-t { font-size:13px; font-weight:700; color:var(--ink); }
    .det-drop-p { font-size:11.5px; color:var(--muted); margin-top:3px; }
    .det .det-file { position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer; z-index:2; }

    /* picked file summary */
    .det-picked { display:flex; align-items:center; gap:12px; margin-top:12px; padding:12px 14px; border:1px solid rgba(16,185,129,.4); border-left:3px solid var(--green); border-radius:12px; background:var(--green-soft); }
    .det-picked-ic { width:36px; height:36px; border-radius:10px; background:#fff; color:var(--green); display:flex; align-items:center; justify-content:center; font-size:14px; flex:0 0 auto; }
    .det-picked-info { flex:1; min-width:0; }
    .det-picked-name { font-size:12.5px; font-weight:700; color:#065F46; word-break:break-word; }
    .det-picked-size { font-size:11px; color:#047857; margin-top:1px; }
    .det-picked-actions { display:flex; gap:8px; flex-shrink:0; }

    /* input box (payment proof etc.) */
    .det-input-box { width:100%; background:rgba(255,255,255,.5); border:1px solid rgba(26,26,46,.14); border-radius:12px; padding:11px 13px; font:13px var(--ink); }
    .det-input-box:focus { outline:none; border-color:var(--coral); box-shadow:0 0 0 4px rgba(255,107,107,.14); background:#fff; }

    /* payment block */
    .det-pay { border:1px solid var(--divider); border-radius:16px; padding:20px; background:transparent; }
    .det-pay-fee { font-size:14px; color:var(--ink); }
    .det-pay-fee b { font-size:20px; font-weight:800; color:var(--coral); margin-left:6px; }
    .det-pay-note { background:var(--blue-soft); border-radius:12px; padding:12px 14px; margin:14px 0; font-size:13px; color:var(--ink); }
    .det-bank { margin-top:16px; border-top:1px solid var(--divider); padding-top:16px; }
    .det-bank-info { background:var(--amber-soft); border-radius:12px; padding:12px 14px; margin-bottom:14px; font-size:13px; color:var(--ink); }
    .det-bank-info b { font-size:15px; }

    /* accepted / notice */
    .det-notice { display:flex; gap:14px; align-items:flex-start; border-radius:16px; padding:20px; border:1px solid rgba(16,185,129,.35); border-left:3px solid var(--green); background:var(--green-soft); margin-top:26px; }
    .det-notice i.det-notice-ic { color:var(--green); font-size:20px; margin-top:1px; }
    .det-notice-t { font-size:14px; font-weight:800; color:#065F46; }
    .det-notice-p { margin-top:4px; font-size:13px; line-height:1.6; color:#047857; }
    .det-notice .det-btn { margin-top:14px; }

    /* modals (inside scope) */
    .det .det-modal-backdrop { position:fixed; inset:0; z-index:90; background:rgba(26,26,46,.36); backdrop-filter:blur(3px); display:none; align-items:center; justify-content:center; padding:16px; }
    .det .det-modal-backdrop.is-open { display:flex; }
    .det .det-modal { width:100%; max-width:420px; background:#fff; border-radius:18px; padding:22px; box-shadow:0 24px 60px -18px rgba(26,26,46,.4); animation:detModalPop .2s cubic-bezier(.22,1.2,.36,1); }
    @keyframes detModalPop { from { opacity:0; transform:translateY(8px) scale(.98); } to { opacity:1; transform:translateY(0) scale(1); } }
    .det .det-modal-body { display:flex; gap:13px; margin-bottom:16px; }
    .det .det-modal-ic { flex:0 0 auto; width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:17px; }
    .det .det-modal-ic.red { background:var(--red-soft); color:var(--red); }
    .det .det-modal-ic.amber { background:var(--amber-soft); color:#b45309; }
    .det .det-modal-t { font-size:15px; font-weight:800; color:var(--ink); }
    .det .det-modal-p { font-size:12.5px; color:var(--muted); margin-top:3px; line-height:1.5; }
    .det .det-modal-list { margin-top:10px; padding:12px 14px; background:var(--amber-soft); border-radius:11px; }
    .det .det-modal-list p { font-size:11.5px; font-weight:700; color:#B45309; margin-bottom:6px; }
    .det .det-modal-list ul { list-style:none; padding:0; margin:0; }
    .det .det-modal-list li { display:flex; align-items:flex-start; gap:7px; font-size:11.5px; color:#B45309; margin-top:5px; }
    .det .det-modal-list li i { margin-top:1px; }
    .det .det-modal-ack { display:flex; align-items:flex-start; gap:10px; margin-top:16px; font-size:12px; color:var(--ink); cursor:pointer; }
    .det .det-modal-ack input { width:17px; height:17px; margin-top:1px; accent-color:var(--red); flex:0 0 auto; }
    .det .det-modal-foot { display:flex; justify-content:flex-end; gap:10px; margin-top:18px; padding-top:16px; border-top:1px solid var(--divider); }
    .det .det-modal textarea.det-input-box { width:100%; }

    @media (max-width:720px) {
      .det { padding:20px 18px 96px; border-radius:18px; }
      .det-fields { grid-template-columns:1fr; }
      .det-status-grid { grid-template-columns:1fr; }
      .det-file-actions { justify-content:flex-start; }
      .det-picked { flex-wrap:wrap; }
      .det-bar-inner .det-btn { flex:1; }
    }
  </style>

  <div class="det">
    <div class="det-inner">

      {{-- Crumbs + title --}}
      <div class="det-crumb">
        <a href="{{ route('registration.index') }}">Pendaftaran</a>
        <i class="fa-solid fa-chevron-right" style="font-size:9px"></i>
        <span>Detail Pendaftaran</span>
      </div>
      <h1 class="det-title">Detail Pendaftaran</h1>
      <p class="det-meta">Pantau status pendaftaran, kelola dokumen, dan selesaikan pembayaran kamu di sini.</p>

      {{-- Session banners --}}
      @if (session('success'))
        <div class="det-alert green">
          <i class="fa-solid fa-circle-check det-alert-ic"></i>
          <div class="det-alert-body"><p class="det-alert-p">{{ session('success') }}</p></div>
        </div>
      @endif

      @if (session('error'))
        <div class="det-alert red">
          <i class="fa-solid fa-circle-exclamation det-alert-ic"></i>
          <div class="det-alert-body"><p class="det-alert-p">{{ session('error') }}</p></div>
        </div>
      @endif
      @if (($errors ?? null) && $errors->any())
        <div class="det-alert red">
          <i class="fa-solid fa-circle-exclamation det-alert-ic"></i>
          <div class="det-alert-body">
            <p class="det-alert-t">Upload gagal</p>
            @foreach ($errors->all() as $err)
              <p class="det-alert-p">{{ $err }}</p>
            @endforeach
          </div>
        </div>
      @endif

      @if (!isset($isAdmin) || !$isAdmin)
        @if ($registration->status === 'withdrawn')
          <div class="det-alert amber">
            <i class="fa-solid fa-person-walking-arrow-right det-alert-ic"></i>
            <div class="det-alert-body">
              <p class="det-alert-t">Anda telah Mundur diri dari pendaftaran ini</p>
              <p class="det-alert-p">Anda dapat membuat pendaftaran baru jika periode pendaftaran masih dibuka.</p>
            </div>
          </div>
        @endif
      @endif

      @if (!isset($isAdmin) || !$isAdmin)
        @php
            $rejectedDocs = $registration->documents->whereNotNull('verification_notes');
        @endphp
        @if ($registration->status === 'rejected' || $rejectedDocs->count() > 0)
          <div class="det-alert red">
            <i class="fa-solid fa-triangle-exclamation det-alert-ic"></i>
            <div class="det-alert-body">
              <p class="det-alert-t">{{ $registration->status === 'rejected' ? 'Pendaftaran Anda ditolak' : 'Dokumen Anda ada yang ditolak' }}</p>
              <p class="det-alert-p">
                @if ($rejectedDocs->count() > 0)
                  @foreach ($rejectedDocs as $rejectedDoc)
                    <span class="block mt-1">{{ $rejectedDoc->document_type }}: {{ $rejectedDoc->verification_notes }}</span>
                  @endforeach
                @elseif ($registration->verified_notes)
                  <span class="block mt-1">Alasan: {{ $registration->verified_notes }}</span>
                @endif
              </p>
              <p class="det-alert-p mt-2">Silakan upload ulang dokumen yang ditolak di bagian <strong>Upload Dokumen</strong> di bawah.</p>
            </div>
          </div>
        @endif
        @if (!empty($registration->verified_notes) && $registration->status !== 'rejected' && $rejectedDocs->count() === 0)
          <div class="det-alert info">
            <i class="fa-solid fa-comment-dots det-alert-ic"></i>
            <div class="det-alert-body">
              <p class="det-alert-t">Catatan dari Panitia</p>
              <p class="det-alert-p">{{ $registration->verified_notes }}</p>
              @if($registration->verifiedBy)
                <p class="det-alert-p" style="font-size:11.5px;opacity:.85">Oleh: {{ $registration->verifiedBy->name }} — {{ $registration->updated_at->format('d M Y H:i') }}</p>
              @endif
            </div>
          </div>
        @endif
      @endif

      @if (!isset($isAdmin) || !$isAdmin)
        <x-re-registration-reminder :registration="$registration" />
        @if ($registration->status === 'pending' && in_array($registration->payment_status, ['unpaid', 'pending']) && $registration->deadline_at)
          @php
              $isDeadlineExpired = $registration->isDeadlineExpired();
              $hoursRemaining = $registration->getDeadlineHoursRemaining();
          @endphp
          @if ($isDeadlineExpired)
            <div class="det-alert red">
              <i class="fa-solid fa-circle-exclamation det-alert-ic"></i>
              <div class="det-alert-body">
                <p class="det-alert-t">Batas waktu telah terlewati!</p>
                <p class="det-alert-p">Pendaftaran ini akan segera dibatalkan otomatis karena melebihi batas waktu penyelesaian ({{ $registration->deadline_at->format('d M Y H:i') }}).</p>
              </div>
            </div>
          @elseif ($hoursRemaining !== null && $hoursRemaining <= 24)
            <div class="det-alert amber">
              <i class="fa-solid fa-clock det-alert-ic"></i>
              <div class="det-alert-body">
                <p class="det-alert-t">Segera selesaikan pendaftaran Anda!</p>
                <p class="det-alert-p">Sisa waktu: <strong>{{ $registration->getDeadlineLabel() }}</strong> (sampai {{ $registration->deadline_at->format('d M Y H:i') }}). Segera lengkapi dokumen dan lakukan pembayaran sebelum pendaftaran dibatalkan otomatis.</p>
              </div>
            </div>
          @else
            <div class="det-alert info">
              <i class="fa-solid fa-hourglass-half det-alert-ic"></i>
              <div class="det-alert-body">
                <p class="det-alert-p"><strong>Batas waktu penyelesaian:</strong> {{ $registration->deadline_at->format('d M Y H:i') }} (sisa {{ $registration->getDeadlineLabel() }})</p>
              </div>
            </div>
          @endif
        @endif
      @endif

      {{-- Registration number --}}
      <div class="det-regnum">
        <span class="det-regnum-ic"><i class="fa-solid fa-file-signature"></i></span>
        <div>
          <p class="det-regnum-num">{{ $registration->registration_number }}</p>
          <p class="det-regnum-sub">Dibuat: {{ $registration->created_at->format('d M Y H:i') }}</p>
        </div>
        @php
          $regTone = match(true) {
            str_contains($registration->status, 'rejected') => 'red',
            $registration->status === 'withdrawn' || $registration->status === 'canceled' => 'amber',
            $registration->status === 'accepted' || $registration->status === 're_registration_complete' || $registration->status === 'completed' => 'green',
            $registration->status === 'verified' => 'blue',
            default => 'amber',
          };
        @endphp
        <span class="det-pill {{ $regTone }} det-regnum-badge">{{ \App\Support\StatusBadge::registrationStatusLabel($registration->status) }}</span>
      </div>

      {{-- ===== INFORMASI PENDAFTAR ===== --}}
      <section class="det-sec">
        <div class="det-sec-head">
          <div class="det-sec-ic coral"><i class="fa-solid fa-user-graduate"></i></div>
          <div>
            <p class="det-sec-ttl">Informasi Pendaftaran</p>
            <p class="det-sec-desc">Data pendaftar dan detail pilihan pendaftaran.</p>
          </div>
        </div>
        <div class="det-fields">
          <div class="det-field">
            <p class="det-f-label">Nama</p>
            <p class="det-f-val">{{ $registration->applicant->full_name }}</p>
          </div>
          <div class="det-field">
            <p class="det-f-label">Email</p>
            <p class="det-f-val">{{ $registration->applicant->user->email }}</p>
          </div>
          @if ($registration->applicant->phone)
          <div class="det-field">
            <p class="det-f-label">Telepon</p>
            <p class="det-f-val">{{ $registration->applicant->phone }}</p>
          </div>
          @endif
          <div class="det-field">
            <p class="det-f-label">Jenjang</p>
            <p class="det-f-val">{{ $registration->registrationPeriod->schoolLevel->name }}</p>
          </div>
          <div class="det-field">
            <p class="det-f-label">Periode</p>
            <p class="det-f-val">{{ $registration->registrationPeriod->name }}</p>
          </div>
          <div class="det-field">
            <p class="det-f-label">Jalur</p>
            <p class="det-f-val">{{ $registration->registrationTrack->name }}</p>
          </div>
          <div class="det-field">
            <p class="det-f-label">Sekolah</p>
            <p class="det-f-val">{{ $registration->school->name }}</p>
          </div>
          @if($registration->major)
          <div class="det-field">
            <p class="det-f-label">Jurusan Pilihan</p>
            <p class="det-f-val">{{ $registration->major->name }}</p>
          </div>
          @endif
          @if($registration->finalMajor)
          <div class="det-field">
            <p class="det-f-label">Jurusan Diterima</p>
            <p class="det-f-val">{{ $registration->finalMajor->name }}</p>
          </div>
          @endif
        </div>
      </section>

      {{-- ===== STATUS ===== --}}
      <section class="det-sec">
        <div class="det-sec-head">
          <div class="det-sec-ic blue"><i class="fa-solid fa-gauge-high"></i></div>
          <div>
            <p class="det-sec-ttl">Status Pendaftaran &amp; Pembayaran</p>
            <p class="det-sec-desc">Pantau progres pendaftaran dan kewajiban pembayaran kamu.</p>
          </div>
        </div>

        <div class="det-status-grid">
          {{-- Status Pendaftaran --}}
          <div class="det-status">
            <div class="det-status-hd">
              <span class="det-status-ic"><i class="fa-solid fa-file-circle-check"></i></span>
              <p class="det-status-ttl">Status Pendaftaran</p>
            </div>
            @php
              $requiredDocs = [
                'foto', 'kartu_keluarga', 'akta_lahir', 'rapor',
              ];
              $trackName = $registration->registrationTrack->name ?? '';
              if (in_array($registration->registrationPeriod->schoolLevel->name, ['SMA', 'SMK'])) {
                $requiredDocs[] = 'ijazah_skl';
              }
              if ($trackName === 'Prestasi') {
                $requiredDocs[] = 'sertifikat_prestasi';
              } elseif ($trackName === 'Beasiswa') {
                $requiredDocs[] = 'surat_keterangan_tidak_mampu';
              }
              $uploadedTypes = $registration->documents->pluck('document_type')->all();
              $docsComplete = count(array_diff($requiredDocs, $uploadedTypes)) === 0;

              $hasRejectedDoc = $registration->documents->contains(fn ($doc) => $doc->verification_notes);
              if ($hasRejectedDoc || $registration->status === 'rejected') {
                $docsComplete = false;
              }

              if ($registration->status === 'pending' && !$docsComplete) {
                $statusLabel = 'Belum Lengkap';
                $statusColor = 'bg-gray-100 text-gray-800 border-gray-300';
              } else {
                $statusLabel = \App\Support\StatusBadge::registrationStatusLabel($registration->status);
                $statusColor = \App\Support\StatusBadge::registrationStatusClass($registration->status);
              }
              $statusTone = match(true) {
                str_contains($statusColor, 'green') => 'green',
                str_contains($statusColor, 'red') => 'red',
                str_contains($statusColor, 'yellow') => 'amber',
                str_contains($statusColor, 'blue') => 'blue',
                str_contains($statusColor, 'purple') => 'blue',
                str_contains($statusColor, 'orange') => 'amber',
                default => 'gray',
              };
            @endphp
            <span class="det-pill {{ $statusTone }}"><i class="fa-solid fa-circle"></i> {{ $statusLabel }}</span>

            @if ($registration->status === 'pending' && !$docsComplete)
              <div class="det-status-line"><i class="fa-solid fa-circle-info"></i><span>Lengkapi dokumen yang masih belum diupload.</span></div>
            @endif
            @if ($registration->documents_verified_at)
              <div class="det-status-line"><i class="fa-solid fa-circle-check"></i><span>Diverifikasi: <b>{{ $registration->documents_verified_at->format('d M Y H:i') }}</b></span></div>
            @endif
            @if ($registration->deadline_at && $registration->status === 'pending')
              @php
                $hoursRemaining = $registration->getDeadlineHoursRemaining();
                $isExpired = $registration->isDeadlineExpired();
              @endphp
              @if ($isExpired)
                <div class="det-status-line"><i class="fa-solid fa-triangle-exclamation"></i><span style="color:var(--red)">Batas waktu telah terlewati. Pendaftaran akan dibatalkan otomatis.</span></div>
              @elseif ($hoursRemaining !== null)
                @php
                  $timeTone = $hoursRemaining <= 24 ? 'var(--red)' : 'var(--ink)';
                  $timeLabel = $hoursRemaining > 24
                    ? floor($hoursRemaining / 24) . ' hari ' . ($hoursRemaining % 24) . ' jam'
                    : $hoursRemaining . ' jam';
                @endphp
                <div class="det-status-line"><i class="fa-solid fa-hourglass-end"></i><span>Sisa waktu: <b style="color:{{ $timeTone }}">{{ $timeLabel }}</b></span></div>
              @endif
            @endif
            @if ($registration->canceled_at)
              <div class="det-status-line"><i class="fa-solid fa-ban"></i><span style="color:var(--red)">Dibatalkan pada: {{ $registration->canceled_at->format('d M Y H:i') }}</span></div>
            @endif
            @if ($registration->withdrawn_at)
              <div class="det-status-line"><i class="fa-solid fa-person-walking-arrow-right"></i><span style="color:var(--amber)">Mengundurkan diri pada: {{ $registration->withdrawn_at->format('d M Y H:i') }}</span></div>
            @endif

            @if ($isStudentView && $registration->status === 'pending' && !$docsComplete)
              <button type="button" id="det-scroll-docs" class="det-btn coral sm"><i class="fa-solid fa-arrow-up-from-bracket"></i> Lengkapi Dokumen</button>
            @endif
          </div>

          {{-- Status Pembayaran --}}
          <div class="det-status pay">
            <div class="det-status-hd">
              <span class="det-status-ic"><i class="fa-solid fa-credit-card"></i></span>
              <p class="det-status-ttl">Status Pembayaran</p>
            </div>
            @php
              $paymentColor = \App\Support\StatusBadge::paymentStatusClass($registration->payment_status);
              $paymentLabel = \App\Support\StatusBadge::paymentStatusLabel($registration->payment_status);
              $payTone = match(true) {
                str_contains($paymentColor, 'green') => 'green',
                str_contains($paymentColor, 'red') => 'red',
                str_contains($paymentColor, 'yellow') => 'amber',
                default => 'gray',
              };
            @endphp
            <span class="det-pill {{ $payTone }}"><i class="fa-solid fa-circle"></i> {{ $paymentLabel }}</span>
            @if ($registration->payment_amount)
              <div class="det-status-line"><i class="fa-solid fa-wallet"></i><span>Jumlah: <b>Rp {{ number_format($registration->payment_amount, 0, ',', '.') }}</b></span></div>
            @endif
            @php
              $trackNameForPay = $registration->registrationTrack->name ?? '';
              $hasPaidPayment = $registration->payments()->whereIn('status', ['verified', 'paid'])->exists();
              $payLocked = ($registration->status !== 'verified' || $registration->payment_amount === null) && !$hasPaidPayment;
            @endphp
            @if ($payLocked)
              <div class="det-alert amber" style="margin-top:14px">
                <i class="fa-solid fa-lock det-alert-ic"></i>
                <div class="det-alert-body">
                  <p class="det-alert-t">Pembayaran belum tersedia</p>
                  <p class="det-alert-p">Pembayaran terbuka setelah seluruh berkas kamu <strong>Terverifikasi</strong> oleh panitia. Nominal biaya akan muncul di sini.</p>
                </div>
              </div>
            @endif
          </div>
        </div>
      </section>

      @if ($registration->notes)
      <section class="det-sec">
        <div class="det-sec-head">
          <div class="det-sec-ic amber"><i class="fa-solid fa-note-sticky"></i></div>
          <div>
            <p class="det-sec-ttl">Catatan</p>
            <p class="det-sec-desc">Catatan dari panitia untuk pendaftaran ini.</p>
          </div>
        </div>
        <p style="font-size:13px;color:var(--ink);line-height:1.6">{{ $registration->notes }}</p>
      </section>
      @endif

      @if($docsComplete && $registration->payment_status === 'paid' && !in_array($registration->status, ['accepted', 're_registration_complete']))
        <div class="det-alert info" style="margin-top:20px">
          <i class="fa-solid fa-circle-check det-alert-ic"></i>
          <div class="det-alert-body">
            <p class="det-alert-t">Dokumen dan Pembayaran Lengkap</p>
            <p class="det-alert-p">Menunggu verifikasi panitia — setelah diverifikasi akan muncul instruksi cetak kartu daftar ulang.</p>
          </div>
        </div>
      @endif

      {{-- ===== UPLOAD DOKUMEN ===== --}}
      <section class="det-sec" id="upload-section">
        <div class="det-sec-head">
          <div class="det-sec-ic coral"><i class="fa-solid fa-folder-open"></i></div>
          <div>
            <p class="det-sec-ttl">Upload Dokumen</p>
            <p class="det-sec-desc">Unggah berkas persyaratan. Klik atau seret file pada area yang tersedia.</p>
          </div>
        </div>

        @if($isWithdrawn)
          <div class="det-alert amber" style="margin-top:0">
            <i class="fa-solid fa-lock det-alert-ic"></i>
            <div class="det-alert-body">
              <p class="det-alert-t">Upload dokumen dikunci — Anda telah mengundurkan diri.</p>
              <p class="det-alert-p">Status semua dokumen diubah menjadi <strong>Ditolak</strong> (Pendaftar mengundurkan diri). Tidak dapat menambah atau mengganti dokumen.</p>
            </div>
          </div>
        @endif

        <div>
          @php
            $documentTypes = [
              'foto' => 'Pas Foto 3x4',
              'kartu_keluarga' => 'Kartu Keluarga',
              'akta_lahir' => 'Akta Kelahiran',
              'rapor' => 'Rapor (boleh lebih dari 1 file)',
            ];

            $trackName = $registration->registrationTrack->name ?? '';
            $isSMK = in_array($registration->registrationPeriod->schoolLevel->name, ['SMA', 'SMK']);

            if ($isSMK) {
              $documentTypes['ijazah_skl'] = 'Ijazah / SKL';
            }

            if ($trackName === 'Prestasi') {
              $documentTypes['sertifikat_prestasi'] = 'Sertifikat Prestasi';
            } elseif ($trackName === 'Beasiswa') {
              $documentTypes['surat_keterangan_tidak_mampu'] = 'Surat Keterangan Tidak Mampu';
            }

            $uploadedDocs = $registration->documents->groupBy('document_type');
            $multi = ['rapor'];
            $totalDocTypes = count($documentTypes);
            $uploadedTypeCount = $uploadedDocs->keys()->count();
          @endphp

          @if($isStudentView && !$isWithdrawn)
            <div class="det-progress">
              <div class="det-progress-head">
                <span id="det-progress-text">{{ $uploadedTypeCount }} dari {{ $totalDocTypes }} dokumen telah diupload</span>
                <span class="det-progress-pct" id="det-progress-pct">{{ $totalDocTypes ? round(($uploadedTypeCount / $totalDocTypes) * 100) : 0 }}%</span>
              </div>
              <div class="det-progress-track">
                <div class="det-progress-fill" id="det-progress-fill" style="width:{{ $totalDocTypes ? round(($uploadedTypeCount / $totalDocTypes) * 100) : 0 }}%"></div>
              </div>
            </div>
          @endif

          <form action="{{ route('registration.documents.upload', $registration) }}" method="POST" enctype="multipart/form-data" @if($isWithdrawn) onsubmit="return false;" @endif>
            @csrf

            @foreach($documentTypes as $type => $label)
              @php $docsOfType = $uploadedDocs->get($type, collect()); @endphp
              <div class="det-doc">
                @php
                  $docPill = 'gray'; $docPillText = 'Belum diupload';
                  if ($docsOfType->count() > 0) {
                    if ($docsOfType->every(fn($d) => $d->verified_at)) { $docPill = 'green'; $docPillText = 'Terverifikasi'; }
                    elseif ($docsOfType->contains(fn($d) => $d->verification_notes)) { $docPill = 'red'; $docPillText = 'Ditolak'; }
                    else { $docPill = 'amber'; $docPillText = 'Menunggu Verifikasi'; }
                  }
                @endphp
                <div class="det-doc-head">
                  <div>
                    <p class="det-doc-label">{{ $label }}</p>
                    <p class="det-doc-sub">
                      @if($docsOfType->count() > 0)
                        {{ $docsOfType->count() }} file diupload{{ in_array($type, $multi) ? ' · bisa lebih dari 1 file' : '' }}
                      @else
                        Belum ada file diupload
                      @endif
                    </p>
                  </div>
                  <span class="det-pill {{ $docPill }}">{{ $docPillText }}</span>
                </div>

                {{-- Existing uploaded file rows --}}
                @if($docsOfType->count() > 0)
                  @foreach($docsOfType as $doc)
                    <div class="det-file-row">
                      <span class="det-file-ic"><i class="fa-solid fa-file-lines"></i></span>
                      <div class="det-file-info">
                        <button type="button" onclick="showFileModal('{{ route('registration.documents.download', [$registration, $doc]) }}', '{{ $doc->file_name }}')" class="det-file-name">{{ $doc->file_name }}</button>
                        <p class="det-file-status">
                          @if($doc->verified_at)
                            <span class="det-pill green"><i class="fa-solid fa-circle-check"></i> Terverifikasi</span>
                          @elseif($doc->verification_notes)
                            <span class="det-pill red"><i class="fa-solid fa-circle-xmark"></i> Ditolak</span>
                          @else
                            <span class="det-pill amber"><i class="fa-solid fa-clock"></i> Menunggu Verifikasi</span>
                          @endif
                        </p>
                        @if($doc->verification_notes)
                          <span class="det-note-red"><strong>Alasan penolakan:</strong> {{ $doc->verification_notes }}</span>
                        @endif
                      </div>
                      <div class="det-file-actions">
                        @if(isset($isAdmin) && $isAdmin && !$doc->verified_at)
                          <button type="button" onclick="submitDocAction('{{ route('admin.documents.verify', $doc) }}', 'PATCH')" class="det-mini green"><i class="fa-solid fa-check"></i> Verifikasi</button>
                          <button type="button" onclick="openRejectModal({{ $doc->id }})" class="det-mini red"><i class="fa-solid fa-xmark"></i> Tolak</button>
                        @elseif($isStudentView && !$isWithdrawn)
                          <button type="button" onclick="submitDocAction('{{ route('registration.documents.delete', [$registration, $doc]) }}', 'DELETE')" class="det-mini red"><i class="fa-solid fa-trash"></i> Hapus</button>
                        @endif
                      </div>
                    </div>
                  @endforeach
                  @php $needsUploadHint = $docsOfType->isEmpty() || $docsOfType->contains(fn($d) => !empty($d->verification_notes)); @endphp
                  @if($isStudentView && !$isWithdrawn && $needsUploadHint)
                    <p class="det-doc-sub" style="margin-top:8px">{{ in_array($type, $multi) ? 'Tambah file lain / ganti dokumen di bawah.' : 'Pilih file jika ingin mengganti dokumen ini.' }}</p>
                  @endif
                @else
                  @if(isset($isAdmin) && $isAdmin)
                    <p class="det-doc-sub" style="margin-top:8px">Belum diupload</p>
                  @elseif($isWithdrawn)
                    <p class="det-doc-sub" style="margin-top:8px;color:var(--red);font-weight:600">Ditolak — pendaftar mengundurkan diri</p>
                  @endif
                @endif

                @php $needsUpload = $docsOfType->isEmpty() || $docsOfType->contains(fn($d) => !empty($d->verification_notes)); @endphp
                @if($isStudentView && !$isWithdrawn)
                  @if($needsUpload)
                  {{-- Custom upload area + hidden native input --}}
                  <label class="det-drop" id="det-drop-{{ $type }}" for="det-input-{{ $type }}">
                    <input type="file" name="documents[{{ $type }}]{{ in_array($type, $multi) ? '[]' : '' }}" {{ in_array($type, $multi) ? 'multiple' : '' }}
                        accept=".pdf,.jpg,.jpeg,.png" class="det-file" id="det-input-{{ $type }}" data-type="{{ $type }}">
                    <span class="det-drop-inner">
                      <span class="det-drop-ic"><i class="fa-solid fa-cloud-arrow-up"></i></span>
                      <span class="det-drop-t">Klik atau seret file ke sini</span>
                      <span class="det-drop-p">PDF, JPG, PNG · maks 2MB{{ in_array($type, $multi) ? ' · bisa pilih beberapa file' : '' }}</span>
                    </span>
                  </label>
                  <div class="det-picked" id="det-picked-{{ $type }}" style="display:none">
                    <span class="det-picked-ic"><i class="fa-solid fa-file"></i></span>
                    <div class="det-picked-info">
                      <p class="det-picked-name" id="det-picked-name-{{ $type }}"></p>
                      <p class="det-picked-size" id="det-picked-size-{{ $type }}"></p>
                    </div>
                    <div class="det-picked-actions">
                      <button type="button" class="det-mini ghost" data-ganti data-type="{{ $type }}"><i class="fa-solid fa-rotate"></i> Ganti</button>
                      <button type="button" class="det-mini red" data-hapus data-type="{{ $type }}"><i class="fa-solid fa-trash"></i> Hapus</button>
                    </div>
                  </div>
                  @else
                    <p class="det-doc-sub" style="margin-top:12px;color:var(--green);font-weight:600"><i class="fa-solid fa-circle-check"></i> Sudah terupload — menunggu verifikasi. Jika ditolak, opsi revisi akan muncul di sini.</p>
                  @endif
                @elseif($isWithdrawn)
                  <div style="margin-top:12px">
                    <span class="det-pill red"><i class="fa-solid fa-lock"></i> Terkunci</span>
                  </div>
                @endif
              </div>
            @endforeach

            @php
              $anyNeedsUpload = false;
              foreach($documentTypes as $t => $lbl){
                $d = $uploadedDocs->get($t, collect());
                if($d->isEmpty() || $d->contains(fn($x) => !empty($x->verification_notes))) { $anyNeedsUpload = true; break; }
              }
            @endphp
            @if($isStudentView && !$isWithdrawn && $anyNeedsUpload)
              <div style="margin-top:22px;padding-top:20px;border-top:1px solid var(--divider)">
                <button type="submit" class="det-btn coral"><i class="fa-solid fa-cloud-arrow-up"></i> Upload Semua Dokumen</button>
                <p class="det-doc-sub" style="margin-top:10px">PDF, JPG, PNG (max 2MB). Pilih file pada area upload, sekali klik akan mengunggah semua; dokumen yang sudah ada ditimpa.</p>
              </div>
            @elseif($isStudentView && !$isWithdrawn && !$anyNeedsUpload)
              <div style="margin-top:22px;padding-top:20px;border-top:1px solid var(--divider)">
                <p class="det-doc-sub" style="color:var(--green);font-weight:600"><i class="fa-solid fa-circle-check"></i> Semua dokumen telah terupload — menunggu verifikasi panitia.</p>
              </div>
            @endif
          </form>
        </div>
      </section>

      {{-- ===== PEMBAYARAN ===== --}}
      @php
        // Pembayaran online yang belum selesai (Xendit) bukan tagihan mengikat
        // → jangan blokir tombol bayar. Hanya pembayaran manual pending yang
        // masih menunggu verifikasi admin yang menghalangi pembuatan tagihan baru.
        $hasPendingPayment = $registration->payments()
            ->where('status', 'pending')
            ->where('payment_method', '!=', 'online')
            ->exists();
        $trackNameForPay = $registration->registrationTrack->name ?? '';
        $hasPaidPayment = $registration->payments()
            ->whereIn('status', ['verified', 'paid'])
            ->exists();
        $payLocked = ($registration->status !== 'verified' || $registration->payment_amount === null) && !$hasPaidPayment;
        $isWithdrawnPay = $registration->status === 'withdrawn';
      @endphp
      <section class="det-sec">
        <div class="det-sec-head">
          <div class="det-sec-ic blue"><i class="fa-solid fa-credit-card"></i></div>
          <div>
            <p class="det-sec-ttl">Pembayaran</p>
            <p class="det-sec-desc">Lengkapi pembayaran biaya pendaftaran kamu.</p>
          </div>
        </div>

        @if($isWithdrawnPay)
          <div class="det-alert red" style="margin-top:0">
            <i class="fa-solid fa-circle-xmark det-alert-ic"></i>
            <div class="det-alert-body">
              <p class="det-alert-t">Pembayaran dibatalkan — Anda telah mengundurkan diri</p>
              <p class="det-alert-p">Pendaftaran <strong>{{ $registration->registration_number }}</strong> dibatalkan pada {{ $registration->withdrawn_at?->format('d M Y H:i') }}. Status dokumen telah diubah menjadi <strong>Ditolak</strong> dan pembayaran tidak dapat dilanjutkan.</p>
            </div>
          </div>
        @elseif($payLocked)
          <div class="det-alert amber" style="margin-top:0">
            <i class="fa-solid fa-lock det-alert-ic"></i>
            <div class="det-alert-body">
              <p class="det-alert-t">Pembayaran jalur {{ $trackNameForPay }} belum tersedia</p>
              <p class="det-alert-p">Lengkapi berkas lalu tunggu panitia memverifikasi. Setelah status berkas <strong>Terverifikasi</strong>, nominal biaya akan muncul di sini.</p>
            </div>
          </div>
        @elseif(in_array($registration->payment_status, ['unpaid', 'failed', 'pending']) && !$hasPendingPayment)
          @php
            $levelId = $registration->registrationPeriod->school_level_id ?? null;
            $trackId = $registration->registration_track_id;
            $paymentAmount = $registration->payment_amount;
            if (is_null($paymentAmount) && $levelId) {
              $paymentAmount = App\Models\Setting::get("fee_{$levelId}_{$trackId}");
            }
            $paymentAmount = (float)($paymentAmount ?: 0);
            $trackNote = App\Models\Setting::get('note_' . $trackId);
          @endphp
          <div class="det-pay">
            <p class="det-pay-fee">Biaya Pendaftaran: <b>Rp {{ number_format((float)$paymentAmount, 0, ',', '.') }}</b></p>
            @if($trackNote)
              <div class="det-pay-note"><strong>Termasuk:</strong> {{ $trackNote }}</div>
            @endif

            <form action="{{ route('payments.store') }}" method="POST">
              @csrf
              <input type="hidden" name="registration_id" value="{{ $registration->id }}">
              <input type="hidden" name="payment_type" value="registration_fee">
              <input type="hidden" name="amount" value="{{ $paymentAmount }}">
              <input type="hidden" name="payment_method" value="online">
              <button type="submit" class="det-btn coral" style="width:100%">
                <i class="fa-solid fa-credit-card"></i> Bayar Online via Xendit
              </button>
              <p class="det-doc-sub" style="text-align:center;margin-top:8px">Transfer Bank, E-Wallet, Retail Store</p>
            </form>

            <div class="det-bank">
              @php
                $bankName = App\Models\Setting::get('bank_name', '');
                $bankNumber = App\Models\Setting::get('bank_account_number');
                $bankAccountName = App\Models\Setting::get('bank_account_name');
                $paymentNote = App\Models\Setting::get('payment_note');
              @endphp
              <div class="det-bank-info">
                <p style="font-weight:600;margin-bottom:6px">{{ $paymentNote ?: 'Transfer ke rekening berikut:' }}</p>
                @if($bankNumber)
                  <p style="font-weight:800;font-size:15px;color:var(--ink)">{{ $bankName }} - {{ $bankNumber }}</p>
                  <p style="margin-top:2px">a.n. {{ $bankAccountName }}</p>
                @else
                  <p>Nomor rekening belum diatur admin.</p>
                @endif
              </div>
              <p class="det-doc-sub" style="margin-bottom:10px">Setelah transfer, upload bukti transfer manual:</p>
              <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="registration_id" value="{{ $registration->id }}">
                <input type="hidden" name="payment_type" value="registration_fee">
                <input type="hidden" name="amount" value="{{ $paymentAmount }}">
                <input type="hidden" name="payment_method" value="bank_transfer">

                <div style="margin-bottom:12px">
                  <label class="det-f-label" style="display:block;margin-bottom:6px">Bukti Transfer</label>
                  <label class="det-drop" id="det-drop-proof" for="det-input-proof">
                    <input type="file" name="proof_file" accept=".pdf,.jpg,.jpeg,.png" required class="det-file" id="det-input-proof" data-type="proof">
                    <span class="det-drop-inner">
                      <span class="det-drop-ic"><i class="fa-solid fa-cloud-arrow-up"></i></span>
                      <span class="det-drop-t">Klik atau seret file ke sini</span>
                      <span class="det-drop-p">PDF, JPG, PNG · maks 2MB</span>
                    </span>
                  </label>
                  <div class="det-picked" id="det-picked-proof" style="display:none">
                    <span class="det-picked-ic"><i class="fa-solid fa-file"></i></span>
                    <div class="det-picked-info">
                      <p class="det-picked-name" id="det-picked-name-proof"></p>
                      <p class="det-picked-size" id="det-picked-size-proof"></p>
                    </div>
                    <div class="det-picked-actions">
                      <button type="button" class="det-mini ghost" data-ganti data-type="proof"><i class="fa-solid fa-rotate"></i> Ganti</button>
                      <button type="button" class="det-mini red" data-hapus data-type="proof"><i class="fa-solid fa-trash"></i> Hapus</button>
                    </div>
                  </div>
                  <p class="det-doc-sub" style="margin-top:6px">PDF, JPG, PNG (max 2MB)</p>
                </div>

                <button type="submit" class="det-btn green"><i class="fa-solid fa-cloud-arrow-up"></i> Upload Bukti Transfer</button>
              </form>
            </div>
          </div>
        @endif
      </section>

      {{-- ===== RIWAYAT PEMBAYARAN ===== --}}
      @php
        $payments = $registration->payments()->orderBy('created_at', 'desc')->get()
            ->reject(fn ($p) => \App\Models\Payment::isAbandonedOnline($p));
        $hiddenInvoices = $registration->payments()->get()->filter(fn ($p) => \App\Models\Payment::isAbandonedOnline($p))->count();
      @endphp
      @if($payments->count() > 0)
      <section class="det-sec">
        <div class="det-sec-head">
          <div class="det-sec-ic green"><i class="fa-solid fa-clock-rotate-left"></i></div>
          <div>
            <p class="det-sec-ttl">Riwayat Pembayaran</p>
            <p class="det-sec-desc">Daftar transaksi pembayaran pada pendaftaran ini.</p>
          </div>
        </div>
        <div>
          @foreach($payments as $payment)
            <div class="det-file-row">
              <span class="det-file-ic"><i class="fa-solid fa-receipt"></i></span>
              <div class="det-file-info">
                <p class="det-doc-label" style="font-size:13px">
                  @if($payment->payment_method === 'online')
                    Pembayaran Online (Xendit)
                  @else
                    Transfer Manual
                  @endif
                </p>
                @if($payment->payment_method === 'online' && $payment->xendit_payment_method)
                  <p class="det-doc-sub">Channel: <strong>{{ \App\Services\XenditService::friendlyXenditMethod($payment->xendit_payment_method) }}</strong> ({{ $payment->xendit_payment_method }})</p>
                @endif
                <p class="det-doc-sub" style="margin-top:2px">Rp {{ number_format($payment->amount, 0, ',', '.') }} · {{ $payment->created_at->format('d M Y H:i') }}</p>
                @if($payment->rejection_reason)
                  <span class="det-note-red"><strong>Ditolak:</strong> {{ $payment->rejection_reason }}</span>
                @endif
                @if($payment->notes)
                  <p class="det-doc-sub" style="margin-top:6px;background:#F3F4F6;padding:6px 10px;border-radius:8px">{{ $payment->notes }}</p>
                @endif
              </div>
              <div class="det-file-actions">
                @php
                  $paymentStatusColors = [
                    'pending' => 'amber',
                    'verified' => 'green',
                    'rejected' => 'red',
                  ];
                @endphp
                <span class="det-pill {{ $paymentStatusColors[$payment->status] ?? 'gray' }}">{{ ucfirst($payment->status) }}</span>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px">
                  <a href="{{ route('payments.show', $payment) }}" class="det-file-name">Lihat Detail →</a>
                  @if($payment->invoice_pdf)
                    <a href="{{ route('payments.invoice', $payment) }}" target="_blank" class="det-file-name">Invoice (PDF) →</a>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
        @if($hiddenInvoices > 0)
          <p class="det-doc-sub" style="margin-top:10px">{{ $hiddenInvoices }} pembayaran yang tidak dilanjutkan disembunyikan dari riwayat.</p>
        @endif
      </section>
      @endif

      {{-- ===== DITERIMA / DAFTAR ULANG ===== --}}
      @if(in_array($registration->status, ['accepted', 're_registration_complete']))
        @php
          $reReg = $registration->reRegistration;
        @endphp
        <div class="det-notice">
          <i class="fa-solid fa-circle-check det-notice-ic"></i>
          <div>
            <p class="det-notice-t">Anda telah diterima sebagai siswa {{ $registration->school->name }}</p>
            @if($registration->finalMajor)
              <p class="det-notice-p">Jurusan: {{ $registration->finalMajor->name }}</p>
            @endif
            @if($registration->applicant->student_number)
              <p class="det-notice-p">Nomor Induk Siswa (NIS): <strong>{{ $registration->applicant->student_number }}</strong></p>
            @endif

            @if($registration->status === 're_registration_complete')
              <p class="det-notice-p">Daftar ulang selesai — silakan unduh kartu daftar ulang.</p>
              <a href="{{ route('registration.proof', $registration) }}" target="_blank" class="det-btn green"><i class="fa-solid fa-download"></i> Unduh Kartu Daftar Ulang</a>
            @else
              @php
                $rrLevelId = $registration->registrationPeriod->school_level_id ?? null;
                $rrStart = $rrLevelId ? \App\Models\Setting::reRegistrationStartForLevel((int) $rrLevelId) : null;
                $rrEnd = $rrLevelId ? \App\Models\Setting::reRegistrationEndForLevel((int) $rrLevelId) : null;
              @endphp
              <p class="det-notice-p">Daftar ulang dilakukan <strong>offline</strong> di sekolah. Unduh kartu daftar ulang dan bawa dokumen asli sesuai jadwal yang ditentukan.</p>
              @if($rrStart || $rrEnd)
                <p class="det-notice-p">
                  <strong>Jadwal daftar ulang:</strong>
                  @if($rrStart && $rrEnd)
                    <strong>{{ \Illuminate\Support\Carbon::parse($rrStart)->translatedFormat('d F Y') }} – {{ \Illuminate\Support\Carbon::parse($rrEnd)->translatedFormat('d F Y') }}</strong>
                  @elseif($rrStart)
                    mulai <strong>{{ \Illuminate\Support\Carbon::parse($rrStart)->translatedFormat('d F Y') }}</strong>
                  @else
                    sampai <strong>{{ \Illuminate\Support\Carbon::parse($rrEnd)->translatedFormat('d F Y') }}</strong>
                  @endif
                </p>
              @endif
              <a href="{{ route('registration.proof', $registration) }}" target="_blank" class="det-btn green"><i class="fa-solid fa-download"></i> Unduh Kartu Daftar Ulang</a>
              @if($reReg && $reReg->verification_code)
                <p class="det-notice-p">Kode verifikasi: <strong style="font-family:ui-monospace,monospace;letter-spacing:.08em">{{ $reReg->verification_code }}</strong> — tunjukkan kepada panitia di sekolah.</p>
              @endif

              @php
                $reRegFee = (float) (\App\Models\Setting::get('re_registration_fee', 0) ?: 0);
                $reRegFeePaid = $registration->payments()
                    ->where('payment_type', 're_registration_fee')
                    ->where('status', 'verified')
                    ->exists();
                $reRegFeePending = $registration->payments()
                    ->where('payment_type', 're_registration_fee')
                    ->where('status', 'pending')
                    ->exists();
              @endphp
              @if($reRegFee > 0 && !$reRegFeePaid)
                <div style="margin-top:18px;padding-top:16px;border-top:1px solid rgba(16,185,129,.3)">
                  <p class="det-notice-t">Biaya Daftar Ulang: <strong style="color:var(--ink)">Rp {{ number_format($reRegFee, 0, ',', '.') }}</strong></p>
                  <p class="det-notice-p">Selesaikan pembayaran biaya daftar ulang sebelum/bersamaan dengan daftar ulang di sekolah.</p>
                  @if($reRegFeePending)
                    <p class="det-notice-p">Bukti pembayaran biaya daftar ulang Anda sedang <strong>menunggu verifikasi</strong> panitia.</p>
                  @else
                    <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data" style="margin-top:12px">
                      @csrf
                      <input type="hidden" name="registration_id" value="{{ $registration->id }}">
                      <input type="hidden" name="payment_type" value="re_registration_fee">
                      <input type="hidden" name="amount" value="{{ $reRegFee }}">
                      <input type="hidden" name="payment_method" value="bank_transfer">
                      <label class="det-f-label" style="display:block;margin-bottom:6px">Bukti Transfer Biaya Daftar Ulang</label>
                      <label class="det-drop" id="det-drop-proof_rr" for="det-input-proof_rr">
                        <input type="file" name="proof_file" accept=".pdf,.jpg,.jpeg,.png" required class="det-file" id="det-input-proof_rr" data-type="proof_rr">
                        <span class="det-drop-inner">
                          <span class="det-drop-ic"><i class="fa-solid fa-cloud-arrow-up"></i></span>
                          <span class="det-drop-t">Klik atau seret file ke sini</span>
                          <span class="det-drop-p">PDF, JPG, PNG · maks 2MB</span>
                        </span>
                      </label>
                      <div class="det-picked" id="det-picked-proof_rr" style="display:none">
                        <span class="det-picked-ic"><i class="fa-solid fa-file"></i></span>
                        <div class="det-picked-info">
                          <p class="det-picked-name" id="det-picked-name-proof_rr"></p>
                          <p class="det-picked-size" id="det-picked-size-proof_rr"></p>
                        </div>
                        <div class="det-picked-actions">
                          <button type="button" class="det-mini ghost" data-ganti data-type="proof_rr"><i class="fa-solid fa-rotate"></i> Ganti</button>
                          <button type="button" class="det-mini red" data-hapus data-type="proof_rr"><i class="fa-solid fa-trash"></i> Hapus</button>
                        </div>
                      </div>
                      <button type="submit" class="det-btn green" style="margin-top:10px"><i class="fa-solid fa-cloud-arrow-up"></i> Upload Bukti Bayar Daftar Ulang</button>
                    </form>
                  @endif
                </div>
              @elseif($reRegFeePaid)
                <p class="det-notice-p">✓ Biaya daftar ulang <strong>lunas</strong>.</p>
              @endif
            @endif
          </div>
        </div>
      @endif

      <div style="height:28px"></div>

      {{-- ===== MODALS (within scope) ===== --}}
      @if ((!isset($isAdmin) || !$isAdmin) && $registration->status === 'pending')
        {{-- Withdraw Modal 1 --}}
        <div id="withdraw-modal-1" class="det-modal-backdrop">
          <div class="det-modal" role="dialog" aria-modal="true">
            <div class="det-modal-body">
              <div class="det-modal-ic amber"><i class="fa-solid fa-triangle-exclamation"></i></div>
              <div>
                <p class="det-modal-t">Yakin ingin mengundurkan diri?</p>
                <p class="det-modal-p">Pendaftaran <strong>{{ $registration->registration_number }}</strong> akan dibatalkan.</p>
              </div>
            </div>
            <div class="det-modal-list">
              <p>Yang akan terjadi:</p>
              <ul>
                <li><i class="fa-solid fa-check"></i> Status pendaftaran menjadi <strong>Mundur Diri</strong></li>
                <li><i class="fa-solid fa-check"></i> Semua dokumen diubah menjadi <strong>Ditolak</strong></li>
                <li><i class="fa-solid fa-check"></i> Upload dokumen &amp; pembayaran akan <strong>terkunci</strong></li>
              </ul>
            </div>
            <div class="det-modal-foot">
              <button type="button" class="det-btn ghost sm" onclick="closeWithdrawModal1()">Batal</button>
              <button type="button" class="det-btn sm" style="background:var(--ink);color:#fff" onclick="confirmWithdrawStep1()">Ya, Lanjutkan</button>
            </div>
          </div>
        </div>

        {{-- Withdraw Modal 2 --}}
        <div id="withdraw-modal-2" class="det-modal-backdrop">
          <div class="det-modal" role="dialog" aria-modal="true">
            <div class="det-modal-body">
              <div class="det-modal-ic red"><i class="fa-solid fa-circle-xmark"></i></div>
              <div>
                <p class="det-modal-t">Konfirmasi Terakhir</p>
                <p class="det-modal-p" style="color:var(--red);font-weight:600">Tindakan ini tidak dapat dibatalkan!</p>
              </div>
            </div>
            <div class="det-modal-list" style="background:var(--red-soft)">
              <p style="color:var(--red)">Perhatian</p>
              <ul>
                <li style="color:#B91C1C"><i class="fa-solid fa-circle-info"></i> Setelah mengundurkan diri, pendaftaran ini <strong>tidak bisa dipulihkan</strong>. Jika ingin mendaftar lagi, Anda harus membuat pendaftaran baru selama periode masih dibuka.</li>
              </ul>
            </div>
            <label class="det-modal-ack">
              <input type="checkbox" id="withdraw-ack">
              <span>Saya memahami dan menyetujui bahwa pendaftaran akan dibatalkan permanen.</span>
            </label>
            <div class="det-modal-foot">
              <button type="button" class="det-btn ghost sm" onclick="closeWithdrawModal2()">Kembali</button>
              <button type="button" id="withdraw-final-btn" class="det-btn red sm" onclick="confirmWithdrawFinal()" disabled>Ya, Saya Mundur</button>
            </div>
          </div>
        </div>
      @endif

      {{-- Admin reject modal --}}
      @if(isset($isAdmin) && $isAdmin)
        <div id="reject-modal" class="det-modal-backdrop">
          <div class="det-modal" role="dialog" aria-modal="true">
            <div class="det-modal-body">
              <div class="det-modal-ic red"><i class="fa-solid fa-circle-xmark"></i></div>
              <div>
                <p class="det-modal-t">Tolak Dokumen</p>
                <p class="det-modal-p">Masukkan alasan penolakan. Alasan ini akan ditampilkan kepada siswa.</p>
              </div>
            </div>
            <textarea id="reject-notes" class="det-input-box" rows="3" placeholder="Alasan penolakan..."></textarea>
            <div class="det-modal-foot">
              <button type="button" class="det-btn ghost sm" onclick="closeRejectModal()">Batal</button>
              <button type="button" class="det-btn red sm" onclick="confirmReject()"><i class="fa-solid fa-xmark"></i> Tolak Dokumen</button>
            </div>
          </div>
        </div>
      @endif

      @include('components.file-preview-modal')
    </div>{{-- /det-inner --}}
  </div>{{-- /det --}}

  {{-- ===== STICKY ACTION BAR ===== --}}
  <div class="det-bar">
    <div class="det-bar-inner">
      <a href="{{ route('registration.index') }}" class="det-btn ghost">
        <i class="fa-solid fa-arrow-left"></i> Kembali
      </a>
      @if ((!isset($isAdmin) || !$isAdmin) && $registration->status === 'pending')
        <button type="button" onclick="openWithdrawModal1()" class="det-btn red">
          <i class="fa-solid fa-arrow-right-from-bracket"></i> Mundur dari Pendaftaran
        </button>
        <form id="withdraw-form" method="POST" action="{{ route('registration.withdraw', $registration) }}" class="hidden">
          @csrf
        </form>
      @endif
    </div>
  </div>

  @push('scripts')
  <script>
    // ===== Helpers =====
    function detEscapeHtml(s){ return String(s).replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }
    function detFormatSize(bytes){
      if(!bytes) return '';
      if(bytes < 1024) return bytes + ' B';
      if(bytes < 1024 * 1024) return (bytes/1024).toFixed(1) + ' KB';
      return (bytes/(1024*1024)).toFixed(2) + ' MB';
    }

    // ===== Progress counter =====
    var detCovered = new Set(@json($uploadedDocs->keys()));
    function detUpdateProgress(){
      document.querySelectorAll('#upload-section .det-file').forEach(function(inp){
        if(inp.files && inp.files.length > 0) detCovered.add(inp.getAttribute('data-type'));
      });
      var total = document.querySelectorAll('#upload-section .det-file').length;
      var count = Math.min(detCovered.size, total);
      var textEl = document.getElementById('det-progress-text');
      var fillEl = document.getElementById('det-progress-fill');
      var pctEl = document.getElementById('det-progress-pct');
      var pct = total ? Math.round((count/total)*100) : 0;
      if(textEl) textEl.textContent = count + ' dari ' + total + ' dokumen telah diupload';
      if(fillEl) fillEl.style.width = pct + '%';
      if(pctEl) pctEl.textContent = pct + '%';
    }

    // ===== Update picked summary for an input =====
    function detUpdatePicked(inp){
      var type = inp.getAttribute('data-type');
      var picked = document.getElementById('det-picked-' + type);
      var drop = document.getElementById('det-drop-' + type);
      if(!inp.files || inp.files.length === 0){
        if(picked) picked.style.display = 'none';
        if(drop) drop.style.display = 'flex';
        detUpdateProgress();
        return;
      }
      if(drop) drop.style.display = 'none';
      if(picked){
        picked.style.display = 'flex';
        var names = []; var bytes = 0;
        for(var i=0;i<inp.files.length;i++){ names.push(inp.files[i].name); bytes += inp.files[i].size; }
        var nameEl = document.getElementById('det-picked-name-' + type);
        var sizeEl = document.getElementById('det-picked-size-' + type);
        if(nameEl) nameEl.textContent = names.join(', ');
        if(sizeEl) sizeEl.textContent = detFormatSize(bytes);
      }
      detUpdateProgress();
    }

    document.addEventListener('DOMContentLoaded', function(){
      // Drag & drop on drop zones (click handled natively by <label for=...>)
      document.querySelectorAll('.det-drop').forEach(function(drop){
        drop.addEventListener('dragover', function(e){ e.preventDefault(); drop.classList.add('over'); });
        drop.addEventListener('dragleave', function(){ drop.classList.remove('over'); });
        drop.addEventListener('drop', function(e){
          e.preventDefault();
          drop.classList.remove('over');
          var inp = drop.querySelector('.det-file');
          if(!inp) return;
          var files = Array.prototype.slice.call(e.dataTransfer.files || []);
          var dt = new DataTransfer();
          if(inp.multiple){ files.forEach(function(f){ dt.items.add(f); }); }
          else if(files.length){ dt.items.add(files[0]); }
          try { inp.files = dt.files; } catch(err) { return; }
          inp.dispatchEvent(new Event('change', {bubbles:true}));
        });
      });

      // Change handler on native file inputs
      document.querySelectorAll('.det-file').forEach(function(inp){
        inp.addEventListener('change', function(){ detUpdatePicked(inp); });
      });

      // Ganti (re-open picker)
      document.querySelectorAll('[data-ganti]').forEach(function(btn){
        btn.addEventListener('click', function(){
          var inp = document.getElementById('det-input-' + btn.getAttribute('data-type'));
          if(inp) inp.click();
        });
      });

      // Hapus (clear input)
      document.querySelectorAll('[data-hapus]').forEach(function(btn){
        btn.addEventListener('click', function(){
          var inp = document.getElementById('det-input-' + btn.getAttribute('data-type'));
          if(inp){ inp.value = ''; detUpdatePicked(inp); }
        });
      });

      // Smooth scroll to upload section
      var scrollBtn = document.getElementById('det-scroll-docs');
      if(scrollBtn){
        scrollBtn.addEventListener('click', function(e){
          e.preventDefault();
          var target = document.getElementById('upload-section');
          if(target) target.scrollIntoView({behavior:'smooth', block:'start'});
        });
      }

      detUpdateProgress();

      // ===== Withdraw modal logic =====
      var ack = document.getElementById('withdraw-ack');
      var finBtn = document.getElementById('withdraw-final-btn');
      if(ack && finBtn){
        ack.addEventListener('change', function(){ finBtn.disabled = !this.checked; });
      }
      document.addEventListener('keydown', function(e){
        if(e.key === 'Escape'){ closeWithdrawModal1(); closeWithdrawModal2(); closeRejectModal(); }
      });
    });

    // ===== submitDocAction (document delete / verify) =====
    function submitDocAction(url, method) {
      var form = document.createElement('form');
      form.method = 'POST';
      form.action = url;
      form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]').content + '">'
        + (method !== 'POST' ? '<input type="hidden" name="_method" value="' + method + '">' : '');
      document.body.appendChild(form);
      form.submit();
    }

    // ===== Withdraw modal open/close/confirm =====
    function openWithdrawModal1() {
      document.getElementById('withdraw-modal-1').classList.add('is-open');
      document.body.style.overflow = 'hidden';
    }
    function closeWithdrawModal1() {
      var m = document.getElementById('withdraw-modal-1');
      if(m) m.classList.remove('is-open');
      document.body.style.overflow = '';
    }
    function confirmWithdrawStep1() {
      closeWithdrawModal1();
      document.getElementById('withdraw-modal-2').classList.add('is-open');
      document.body.style.overflow = 'hidden';
    }
    function closeWithdrawModal2() {
      var m = document.getElementById('withdraw-modal-2');
      if(m) m.classList.remove('is-open');
      document.body.style.overflow = '';
      var ack = document.getElementById('withdraw-ack');
      if(ack) ack.checked = false;
      var btn = document.getElementById('withdraw-final-btn');
      if(btn) btn.disabled = true;
    }
    function confirmWithdrawFinal() {
      var ack = document.getElementById('withdraw-ack');
      if(!ack || !ack.checked) return;
      document.getElementById('withdraw-form').submit();
    }

    // ===== Admin reject modal =====
    var rejectDocId = null;
    function openRejectModal(documentId) {
      rejectDocId = documentId;
      var notes = document.getElementById('reject-notes');
      if(notes) notes.value = '';
      var m = document.getElementById('reject-modal');
      if(m) m.classList.add('is-open');
      document.body.style.overflow = 'hidden';
    }
    function closeRejectModal() {
      rejectDocId = null;
      var m = document.getElementById('reject-modal');
      if(m) m.classList.remove('is-open');
      document.body.style.overflow = '';
    }
    function confirmReject() {
      var notesEl = document.getElementById('reject-notes');
      var notes = notesEl ? notesEl.value : '';
      if(!notes || !notes.trim()) return;
      var form = document.createElement('form');
      form.method = 'POST';
      form.action = '/admin/documents/' + rejectDocId + '/reject';
      var csrf = document.querySelector('meta[name="csrf-token"]').content;
      form.innerHTML = '<input type="hidden" name="_token" value="' + csrf + '">'
        + '<input type="hidden" name="_method" value="PATCH">'
        + '<input type="hidden" name="verification_notes" value="' + detEscapeHtml(notes.trim()) + '">';
      document.body.appendChild(form);
      form.submit();
    }
  </script>
  @endpush
</x-student-layout>

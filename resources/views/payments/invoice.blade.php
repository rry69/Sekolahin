<x-student-layout title="Invoice Pembayaran">
  @php
    $isVerified = $payment->status === 'verified';
    $isRejected = $payment->status === 'rejected';
    $isPending  = !$isVerified && !$isRejected;

    $hasProof   = (bool) $payment->proof_file;
    $isManual   = $payment->payment_method === 'bank_transfer';
    $isOnline   = $payment->payment_method === 'online';

    $invoiceNumber = $payment->invoice_number;
    $regNumber = $registration->registration_number;
    $amount = (float) $payment->amount;
    $amountLabel = 'Rp ' . number_format($amount, 0, ',', '.');

    // Batas waktu pembayaran: prioritaskan deadline pendaftaran, fallback ke created_at + durasi
    $deadline = $registration->deadline_at
        ?? now()->addHours((int) \App\Models\Setting::get('payment_deadline_hours', 72));
    $deadlineDiff = now()->diffInSeconds($deadline, false);
    $deadlineHours = (int) floor($deadlineDiff / 3600);
    $deadlineExpired = $deadlineDiff <= 0;

    // Status dinamis:
    // - verified            → LUNAS
    // - rejected            → DITOLAK
    // - pending + bukti     → MENUNGGU VERIFIKASI
    // - pending (belum)     → MENUNGGU PEMBAYARAN
    $statusTone = $isVerified ? 'green' : ($isRejected ? 'red' : 'amber');
    $statusLabel = $isVerified ? 'LUNAS'
        : ($isRejected ? 'DITOLAK'
        : ($hasProof ? 'MENUNGGU VERIFIKASI' : 'MENUNGGU PEMBAYARAN'));
    $statusIcon  = $isVerified ? 'fa-circle-check'
        : ($isRejected ? 'fa-circle-xmark'
        : ($hasProof ? 'fa-hourglass-half' : 'fa-clock'));

    // Rekening tujuan pembayaran (dari pengaturan admin)
    $bankName = \App\Models\Setting::get('bank_name', '');
    $bankNumber = \App\Models\Setting::get('bank_account_number');
    $bankAccountName = \App\Models\Setting::get('bank_account_name');
    $paymentNote = \App\Models\Setting::get('payment_note');

    $proofUrl = $hasProof ? route('payments.proof', $payment) : null;
    $proofUploadedAt = $hasProof ? optional($payment->created_at)->format('d M Y H:i') : null;

    $terbilang = app(\App\Services\InvoiceService::class)->terbilang($amount);

    // Logo sekolah (sinkron dengan PDF): tampilkan jika admin sudah mengunggah logo.
    $school = $registration->school;
    $schoolLogoUrl = $school->logo_path ? \Illuminate\Support\Facades\Storage::url($school->logo_path) : null;
  @endphp

  <style>
    .inv { --coral:#FF6B6B; --coral-2:#FF8E6E; --coral-soft:#FFE5E3; --ink:#1a1a2e; --muted:#8a8f9d; --divider:rgba(26,26,46,.10); --green:#10B981; --green-soft:#D1FAE5; --red:#EF4444; --red-soft:#FEE2E2; --amber:#D97706; --amber-soft:#FEF3C7; --blue:#2563EB; --blue-soft:#DBEAFE; --gray:#6b7280; --gray-soft:#F3F4F6; position:relative; border-radius:24px; padding:28px 28px 56px; background:#f6f7fb; }
    .inv .inv-inner { width:100%; max-width:820px; margin:0 auto; }

    /* crumbs + title */
    .inv-crumb { font-size:12.5px; color:var(--muted); margin-bottom:6px; display:flex; align-items:center; gap:7px; flex-wrap:wrap; }
    .inv-crumb a { color:var(--coral); font-weight:600; } .inv-crumb a:hover { text-decoration:underline; }
    .inv-title { font-size:26px; font-weight:800; color:var(--ink); letter-spacing:-0.01em; line-height:1.2; }
    .inv-meta { font-size:13px; color:var(--muted); margin-top:6px; }

    /* header sekolah */
    .inv-school { margin-top:20px; padding:20px 22px; border:1px solid var(--divider); border-radius:18px; display:flex; align-items:flex-start; gap:14px; flex-wrap:wrap; }
    .inv-school-ic { width:48px; height:48px; border-radius:14px; background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; display:flex; align-items:center; justify-content:center; font-size:20px; box-shadow:0 10px 20px -10px rgba(255,107,107,.6); flex:0 0 auto; }
    .inv-school-name { font-size:16px; font-weight:800; color:var(--ink); }
    .inv-school-addr { font-size:12.5px; color:var(--muted); margin-top:2px; }
    .inv-school-badge { margin-left:auto; }

    /* pills */
    .inv-pill { display:inline-flex; align-items:center; gap:6px; padding:5px 12px; border-radius:99px; font-size:11.5px; font-weight:700; white-space:nowrap; }
    .inv-pill.green { background:var(--green-soft); color:#047857; }
    .inv-pill.red { background:var(--red-soft); color:#B91C1C; }
    .inv-pill.amber { background:var(--amber-soft); color:#B45309; }
    .inv-pill.blue { background:var(--blue-soft); color:#1D4ED8; }
    .inv-pill.gray { background:var(--gray-soft); color:var(--gray); }

    /* alert deadline */
    .inv-deadline { display:flex; gap:12px; align-items:flex-start; margin-top:14px; padding:14px 16px; border-radius:14px; border:1px solid transparent; }
    .inv-deadline i.inv-dl-ic { width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:13px; flex:0 0 auto; }
    .inv-deadline .inv-dl-t { font-size:13px; font-weight:800; }
    .inv-deadline .inv-dl-p { font-size:12.5px; margin-top:2px; opacity:.95; }
    .inv-deadline.amber { background:var(--amber-soft); border-color:rgba(217,119,6,.3); }
    .inv-deadline.amber i { background:var(--amber); color:#fff; }
    .inv-deadline.amber .inv-dl-t, .inv-deadline.amber .inv-dl-p { color:#B45309; }
    .inv-deadline.red { background:var(--red-soft); border-color:rgba(239,68,68,.25); }
    .inv-deadline.red i { background:var(--red); color:#fff; }
    .inv-deadline.red .inv-dl-t, .inv-deadline.red .inv-dl-p { color:#B91C1C; }
    .inv-deadline.green { background:var(--green-soft); border-color:rgba(16,185,129,.3); }
    .inv-deadline.green i { background:var(--green); color:#fff; }
    .inv-deadline.green .inv-dl-t, .inv-deadline.green .inv-dl-p { color:#047857; }
    .inv-deadline b { font-weight:800; }

    /* amount highlight */
    .inv-amount { margin-top:14px; border:1px solid var(--divider); border-left:4px solid var(--coral); border-radius:18px; padding:22px 24px; display:flex; align-items:center; gap:16px; flex-wrap:wrap; background:linear-gradient(180deg, rgba(255,107,107,.06), rgba(255,107,107,.02)); }
    .inv-amount-ic { width:52px; height:52px; border-radius:15px; background:var(--coral-soft); color:var(--coral); display:flex; align-items:center; justify-content:center; font-size:21px; flex:0 0 auto; }
    .inv-amount-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }
    .inv-amount-value { font-size:30px; font-weight:800; color:var(--ink); letter-spacing:-0.01em; line-height:1.1; margin-top:2px; }
    .inv-amount-terbilang { font-size:12.5px; color:var(--muted); margin-top:4px; }
    .inv-amount-status { margin-left:auto; }

    /* section */
    .inv-sec { border-top:1px solid var(--divider); padding:24px 0 6px; }
    .inv-sec:first-of-type { border-top:none; padding-top:20px; }
    .inv-sec-head { display:flex; align-items:center; gap:12px; margin-bottom:14px; flex-wrap:wrap; }
    .inv-sec-ic { width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:16px; flex:0 0 auto; }
    .inv-sec-ic.coral { background:var(--coral-soft); color:var(--coral); }
    .inv-sec-ic.blue { background:var(--blue-soft); color:var(--blue); }
    .inv-sec-ic.amber { background:var(--amber-soft); color:var(--amber); }
    .inv-sec-ic.green { background:var(--green-soft); color:var(--green); }
    .inv-sec-ttl { font-size:14px; font-weight:800; color:var(--ink); }
    .inv-sec-desc { font-size:12px; color:var(--muted); margin-top:1px; }

    /* info grid */
    .inv-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:0; }
    .inv-cell { padding:12px 4px; border-bottom:1px solid var(--divider); }
    .inv-cell:nth-last-child(-n+2) { border-bottom:none; }
    .inv-cell .inv-c-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }
    .inv-cell .inv-c-val { margin-top:3px; font-size:14px; font-weight:600; color:var(--ink); display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .inv-copy { display:inline-flex; align-items:center; gap:5px; padding:4px 9px; border-radius:8px; background:var(--gray-soft); color:var(--gray); font-size:11px; font-weight:700; cursor:pointer; border:none; transition:background .15s,color .15s; }
    .inv-copy:hover { background:var(--coral-soft); color:var(--coral); }
    .inv-copy.copied { background:var(--green-soft); color:var(--green); }
    .inv-cell-full { grid-column:1 / -1; }

    /* payment methods */
    .inv-methods { display:grid; grid-template-columns:1fr; gap:14px; }
    .inv-method { width:100%; border:1px solid var(--divider); border-radius:16px; padding:18px; display:flex; flex-direction:column; gap:12px; }
    .inv-method .inv-m-ic { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:17px; }
    .inv-method.primary .inv-m-ic { background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; box-shadow:0 8px 18px -8px rgba(255,107,107,.6); }
    .inv-method.alt .inv-m-ic { background:var(--blue-soft); color:var(--blue); }
    .inv-method .inv-m-ttl { font-size:13.5px; font-weight:800; color:var(--ink); }
    .inv-method .inv-m-desc { font-size:12px; color:var(--muted); line-height:1.5; }
    .inv-method .inv-m-foot { margin-top:auto; }

    /* bank card (manual) */
    .inv-bank { border:1px solid var(--divider); border-radius:16px; overflow:hidden; }
    .inv-bank-head { display:flex; align-items:center; gap:10px; padding:14px 16px; background:linear-gradient(135deg, rgba(255,107,107,.08), rgba(255,107,107,.03)); border-bottom:1px solid var(--divider); }
    .inv-bank-head-ic { width:38px; height:38px; border-radius:11px; background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; display:flex; align-items:center; justify-content:center; font-size:16px; flex:0 0 auto; }
    .inv-bank-head-t { font-size:13px; font-weight:800; color:var(--ink); }
    .inv-bank-head-p { font-size:11.5px; color:var(--muted); margin-top:1px; }
    .inv-bank-rows { padding:6px 16px 14px; }
    .inv-bank-row { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:11px 0; border-bottom:1px dashed var(--divider); flex-wrap:wrap; }
    .inv-bank-row:last-child { border-bottom:none; }
    .inv-bank-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }
    .inv-bank-value { font-size:15px; font-weight:800; color:var(--ink); display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .inv-bank-value .mono { font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace; letter-spacing:.03em; }
    .inv-bank-note { padding:0 16px 14px; }
    .inv-bank-note p { font-size:12.5px; color:#7c4a03; background:var(--amber-soft); border:1px solid rgba(217,119,6,.25); border-radius:10px; padding:10px 12px; line-height:1.5; }

    /* steps (instruksi manual) */
    .inv-steps { display:grid; grid-template-columns:1fr; gap:10px; margin-top:4px; }
    .inv-step { display:flex; gap:12px; align-items:flex-start; }
    .inv-step-n { width:26px; height:26px; border-radius:50%; background:var(--blue-soft); color:var(--blue); font-size:12px; font-weight:800; display:flex; align-items:center; justify-content:center; flex:0 0 auto; margin-top:1px; }
    .inv-step-t { font-size:13px; font-weight:700; color:var(--ink); }
    .inv-step-p { font-size:12px; color:var(--muted); margin-top:1px; line-height:1.5; }

    /* proof card */
    .inv-proof { border:1px solid var(--divider); border-radius:16px; padding:16px; display:flex; align-items:center; gap:14px; flex-wrap:wrap; }
    .inv-proof-ic { width:46px; height:46px; border-radius:13px; display:flex; align-items:center; justify-content:center; font-size:18px; flex:0 0 auto; }
    .inv-proof-ic.green { background:var(--green-soft); color:var(--green); }
    .inv-proof-ic.gray { background:var(--gray-soft); color:var(--gray); }
    .inv-proof-info { flex:1 1 200px; min-width:0; }
    .inv-proof-t { font-size:13.5px; font-weight:800; color:var(--ink); }
    .inv-proof-p { font-size:12px; color:var(--muted); margin-top:2px; }
    .inv-proof-action { flex:0 0 auto; }

    /* buttons */
    .inv-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:13px 20px; border-radius:12px; font-size:14px; font-weight:700; transition:transform .15s, box-shadow .15s, background .15s; min-height:46px; cursor:pointer; border:none; text-decoration:none; }
    .inv-btn.coral { background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; box-shadow:0 10px 22px -10px rgba(255,107,107,.65); }
    .inv-btn.coral:hover { transform:translateY(-1px); box-shadow:0 14px 26px -10px rgba(255,107,107,.7); }
    .inv-btn.blue { background:var(--blue); color:#fff; box-shadow:0 10px 22px -10px rgba(37,99,235,.55); }
    .inv-btn.blue:hover { transform:translateY(-1px); }
    .inv-btn.ghost { background:rgba(255,255,255,.6); color:var(--ink); border:1px solid rgba(26,26,46,.12); }
    .inv-btn.ghost:hover { background:#fff; color:var(--coral); border-color:var(--coral); }
    .inv-btn.green { background:var(--green); color:#fff; }
    .inv-btn.green:hover { transform:translateY(-1px); }

    /* action bar */
    .inv-actions { margin-top:24px; padding-top:20px; border-top:1px solid var(--divider); display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; }

    /* alert info */
    .inv-alert { display:flex; gap:12px; align-items:flex-start; border-radius:14px; padding:14px 16px; margin-top:14px; border:1px solid transparent; }
    .inv-alert i { width:22px; height:22px; border-radius:7px; display:flex; align-items:center; justify-content:center; font-size:11px; flex:0 0 auto; margin-top:1px; }
    .inv-alert.blue { background:var(--blue-soft); border-color:rgba(37,99,235,.25); }
    .inv-alert.blue i { background:var(--blue); color:#fff; }
    .inv-alert.blue .inv-alert-t, .inv-alert.blue .inv-alert-p { color:#1D4ED8; }
    .inv-alert.red { background:var(--red-soft); border-color:rgba(239,68,68,.25); }
    .inv-alert.red i { background:var(--red); color:#fff; }
    .inv-alert.red .inv-alert-t, .inv-alert.red .inv-alert-p { color:#B91C1C; }
    .inv-alert.green { background:var(--green-soft); border-color:rgba(16,185,129,.3); }
    .inv-alert.green i { background:var(--green); color:#fff; }
    .inv-alert.green .inv-alert-t, .inv-alert.green .inv-alert-p { color:#047857; }
    .inv-alert.amber { background:var(--amber-soft); border-color:rgba(217,119,6,.3); }
    .inv-alert.amber i { background:var(--amber); color:#fff; }
    .inv-alert.amber .inv-alert-t, .inv-alert.amber .inv-alert-p { color:#B45309; }
    .inv-alert .inv-alert-t { font-weight:700; font-size:13.5px; }
    .inv-alert .inv-alert-p { font-size:13px; margin-top:2px; opacity:.92; }

    @media (max-width:720px) {
      .inv { padding:20px 16px 40px; border-radius:18px; }
      .inv-grid { grid-template-columns:1fr; }
      .inv-cell:nth-last-child(-n+2) { border-bottom:1px solid var(--divider); }
      .inv-cell:last-child { border-bottom:none; }
      .inv-methods { grid-template-columns:1fr; }
      .inv-actions { grid-template-columns:1fr; }
      .inv-amount-value { font-size:25px; }
      .inv-school-badge { margin-left:0; }
    }
  </style>

  <div class="inv">
    <div class="inv-inner">

      {{-- Crumbs + title --}}
      <div class="inv-crumb">
        <a href="{{ route('registration.index') }}">Pendaftaran</a>
        <i class="fa-solid fa-chevron-right" style="font-size:9px"></i>
        <a href="{{ route('registration.show', $registration) }}">Detail Pendaftaran</a>
        <i class="fa-solid fa-chevron-right" style="font-size:9px"></i>
        <span>Invoice Pembayaran</span>
      </div>
      <h1 class="inv-title">Invoice Pembayaran</h1>
      <p class="inv-meta">Detail tagihan dan petunjuk pembayaran pendaftaran kamu.</p>

      {{-- Flash --}}
      @if (session('success'))
        <div class="inv-alert green" style="margin-top:16px">
          <i class="fa-solid fa-circle-check"></i>
          <div><p class="inv-alert-p">{{ session('success') }}</p></div>
        </div>
      @endif
      @if (session('error'))
        <div class="inv-alert red" style="margin-top:16px">
          <i class="fa-solid fa-circle-exclamation"></i>
          <div><p class="inv-alert-p">{{ session('error') }}</p></div>
        </div>
      @endif

      {{-- School header --}}
      <div class="inv-school">
        @if ($schoolLogoUrl)
          <span class="inv-school-ic" style="background:transparent;box-shadow:none;overflow:hidden;padding:0;">
            <img src="{{ $schoolLogoUrl }}" alt="Logo {{ $school->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:14px;">
          </span>
        @else
          <span class="inv-school-ic"><i class="fa-solid fa-graduation-cap"></i></span>
        @endif
        <div>
          <p class="inv-school-name">{{ $school->name }}</p>
          <p class="inv-school-addr">{{ $school->address }}</p>
          <p class="inv-school-addr" style="margin-top:4px;font-weight:700;color:var(--ink);letter-spacing:.04em">INVOICE PEMBAYARAN</p>
        </div>
        <span class="inv-pill {{ $statusTone }} inv-school-badge" id="inv-status-pill"><i class="fa-solid {{ $statusIcon }}" id="inv-status-icon"></i> {{ $statusLabel }}</span>
      </div>

      {{-- Deadline highlight --}}
      @if ($isPending)
        <div class="inv-deadline {{ $deadlineExpired ? 'red' : ($deadlineHours <= 24 ? 'red' : ($deadlineHours <= 72 ? 'amber' : 'green')) }}" id="inv-deadline-banner">
          <i class="fa-solid {{ $deadlineExpired ? 'fa-circle-exclamation' : 'fa-hourglass-half' }} inv-dl-ic"></i>
          <div>
            @if ($deadlineExpired)
              <p class="inv-dl-t">Batas waktu pembayaran telah berakhir</p>
              <p class="inv-dl-p">Tagihan ini sudah melewati batas waktu pada <b>{{ $deadline->format('d M Y H:i') }}</b>. Silakan buat tagihan baru melalui halaman pendaftaran jika masih diperlukan.</p>
            @else
              <p class="inv-dl-t">Selesaikan pembayaran sebelum <b>{{ $deadline->format('d M Y H:i') }}</b></p>
              <p class="inv-dl-p">
                Sisa waktu: <b id="inv-countdown" data-deadline="{{ $deadline->timestamp }}">{{ $deadline->diffForHumans(['parts' => 2]) }}</b>
                @if ($deadlineHours <= 24)
                  — <span style="font-weight:700">segera! batas waktu kurang dari 24 jam.</span>
                @elseif ($deadlineHours <= 72)
                  — <span style="font-weight:700">jangan ditunda, selesaikan sebelum batas waktu.</span>
                @endif
              </p>
            @endif
          </div>
        </div>
      @elseif ($isVerified)
        {{-- Banner sukses pengganti batas waktu saat sudah lunas --}}
        <div class="inv-deadline green" id="inv-success-banner">
          <i class="fa-solid fa-circle-check inv-dl-ic"></i>
          <div>
            <p class="inv-dl-t">Pembayaran berhasil diverifikasi</p>
            <p class="inv-dl-p">Tagihan ini sudah lunas. Terima kasih!</p>
          </div>
        </div>
      @endif

      {{-- Amount highlight --}}
      <div class="inv-amount">
        <span class="inv-amount-ic"><i class="fa-solid fa-wallet"></i></span>
        <div>
          <p class="inv-amount-label">Total Tagihan</p>
          <p class="inv-amount-value">{{ $amountLabel }}</p>
          <p class="inv-amount-terbilang">Terbilang: {{ ucfirst($terbilang) }} Rupiah</p>
        </div>
        <span class="inv-pill {{ $statusTone }} inv-amount-status" id="inv-amount-status"><i class="fa-solid {{ $statusIcon }}" id="inv-amount-status-icon"></i> {{ $statusLabel }}</span>
      </div>

      {{-- Info grid --}}
      <section class="inv-sec">
        <div class="inv-sec-head">
          <div class="inv-sec-ic blue"><i class="fa-solid fa-receipt"></i></div>
          <div>
            <p class="inv-sec-ttl">Informasi Invoice</p>
            <p class="inv-sec-desc">Nomor dan detail tagihan pembayaran.</p>
          </div>
        </div>
        <div class="inv-grid">
          @if ($invoiceNumber)
          <div class="inv-cell">
            <p class="inv-c-label">No. Invoice</p>
            <p class="inv-c-val">
              <span>{{ $invoiceNumber }}</span>
              <button type="button" class="inv-copy" onclick="invCopy('{{ $invoiceNumber }}', this)" aria-label="Salin nomor invoice"><i class="fa-regular fa-copy"></i> Salin</button>
            </p>
          </div>
          @endif
          <div class="inv-cell">
            <p class="inv-c-label">No. Registrasi</p>
            <p class="inv-c-val">
              <span>{{ $regNumber }}</span>
              <button type="button" class="inv-copy" onclick="invCopy('{{ $regNumber }}', this)" aria-label="Salin nomor registrasi"><i class="fa-regular fa-copy"></i> Salin</button>
            </p>
          </div>
          <div class="inv-cell">
            <p class="inv-c-label">Tanggal Terbit</p>
            <p class="inv-c-val">{{ optional($payment->invoice_issued_at ?? $payment->created_at)->format('d M Y H:i') }}</p>
          </div>
          <div class="inv-cell">
            <p class="inv-c-label">Jenis Biaya</p>
            <p class="inv-c-val">{{ $payment->payment_type === 'registration_fee' ? 'Biaya Pendaftaran' : 'Biaya Daftar Ulang' }}</p>
          </div>
          <div class="inv-cell">
            <p class="inv-c-label">Nama Lengkap</p>
            <p class="inv-c-val">{{ $registration->applicant->full_name }}</p>
          </div>
          <div class="inv-cell">
            <p class="inv-c-label">NISN</p>
            <p class="inv-c-val">{{ $registration->applicant->nisn }}</p>
          </div>
          <div class="inv-cell">
            <p class="inv-c-label">Jenjang</p>
            <p class="inv-c-val">{{ $registration->registrationPeriod->schoolLevel->name }}</p>
          </div>
          <div class="inv-cell">
            <p class="inv-c-label">Periode</p>
            <p class="inv-c-val">{{ $registration->registrationPeriod->name }}</p>
          </div>
          <div class="inv-cell">
            <p class="inv-c-label">Jalur</p>
            <p class="inv-c-val">{{ $registration->registrationTrack->name }}</p>
          </div>
          <div class="inv-cell">
            <p class="inv-c-label">Metode</p>
            <p class="inv-c-val">{{ $isOnline ? 'Online (Xendit)' : 'Transfer Bank (Manual)' }}</p>
          </div>
        </div>
      </section>

      {{-- Payment section --}}
      <section class="inv-sec">
        <div class="inv-sec-head">
          <div class="inv-sec-ic coral"><i class="fa-solid fa-credit-card"></i></div>
          <div>
            <p class="inv-sec-ttl">Metode Pembayaran</p>
            <p class="inv-sec-desc">
              @if ($isManual)
                Transfer ke rekening resmi, lalu unggah bukti transfer untuk diverifikasi panitia.
              @else
                Pilih cara pembayaran yang paling mudah untuk kamu.
              @endif
            </p>
          </div>
        </div>

        <div id="inv-pay-body">
        @if ($isVerified)
          <div class="inv-alert green">
            <i class="fa-solid fa-circle-check"></i>
            <div>
              <p class="inv-alert-t">Pembayaran telah lunas</p>
              <p class="inv-alert-p">Tagihan ini sudah diverifikasi oleh panitia. Terima kasih!</p>
            </div>
          </div>
        @elseif ($isRejected)
          <div class="inv-alert red">
            <i class="fa-solid fa-circle-xmark"></i>
            <div>
              <p class="inv-alert-t">Pembayaran ditolak</p>
              <p class="inv-alert-p">
                @if ($payment->rejection_reason)
                  Alasan: {{ $payment->rejection_reason }}.
                @endif
                Silakan lakukan pembayaran ulang melalui halaman pendaftaran.
              </p>
            </div>
          </div>
        @else
          <div class="inv-methods">
            @if ($isManual)
              {{-- ===== MANUAL (Transfer Bank) ===== --}}
              <div class="inv-method primary">
                <span class="inv-m-ic"><i class="fa-solid fa-building-columns"></i></span>
                <div>
                  <p class="inv-m-ttl">Transfer Bank (Manual)</p>
                  <p class="inv-m-desc">Lakukan transfer sesuai nominal tagihan, lalu unggah bukti transfer. Pembayaran diverifikasi oleh panitia.</p>
                </div>
              </div>

              {{-- Rekening tujuan --}}
              <div class="inv-bank">
                <div class="inv-bank-head">
                  <span class="inv-bank-head-ic"><i class="fa-solid fa-landmark"></i></span>
                  <div>
                    <p class="inv-bank-head-t">Rekening Tujuan Pembayaran</p>
                    <p class="inv-bank-head-p">Transfer ke rekening resmi berikut:</p>
                  </div>
                </div>
                <div class="inv-bank-rows">
                  <div class="inv-bank-row">
                    <span class="inv-bank-label">Nama Bank</span>
                    <span class="inv-bank-value">{{ $bankName ?: 'BCA' }}</span>
                  </div>
                  <div class="inv-bank-row">
                    <span class="inv-bank-label">Nomor Rekening</span>
                    <span class="inv-bank-value">
                      @if ($bankNumber)
                        <span class="mono">{{ $bankNumber }}</span>
                        <button type="button" class="inv-copy" onclick="invCopy('{{ $bankNumber }}', this)" aria-label="Salin nomor rekening"><i class="fa-regular fa-copy"></i> Salin</button>
                      @else
                        <span style="color:var(--muted);font-weight:600">Belum diatur admin</span>
                      @endif
                    </span>
                  </div>
                  <div class="inv-bank-row">
                    <span class="inv-bank-label">Atas Nama</span>
                    <span class="inv-bank-value">{{ $bankAccountName ?: '-' }}</span>
                  </div>
                  <div class="inv-bank-row">
                    <span class="inv-bank-label">Nominal Transfer</span>
                    <span class="inv-bank-value" style="color:var(--coral)">{{ $amountLabel }}</span>
                  </div>
                </div>
                @if ($paymentNote)
                  <div class="inv-bank-note"><p>{{ $paymentNote }}</p></div>
                @endif
              </div>

              {{-- Instruksi singkat --}}
              <div class="inv-steps">
                <div class="inv-step">
                  <span class="inv-step-n">1</span>
                  <div>
                    <p class="inv-step-t">Transfer sesuai nominal</p>
                    <p class="inv-step-p">Transfer <b>{{ $amountLabel }}</b> ke {{ $bankName ?: 'BCA' }} {{ $bankNumber ?: '' }} a.n. {{ $bankAccountName ?: '-' }}.</p>
                  </div>
                </div>
                <div class="inv-step">
                  <span class="inv-step-n">2</span>
                  <div>
                    <p class="inv-step-t">{{ $hasProof ? 'Bukti sudah diunggah' : 'Unggah bukti transfer' }}</p>
                    <p class="inv-step-p">
                      @if ($hasProof)
                        Bukti transfer kamu sudah diterima dan sedang menunggu verifikasi panitia.
                      @else
                        Setelah transfer, unggah bukti (foto/scan) melalui halaman <a href="{{ route('registration.show', $registration) }}" style="color:var(--blue);font-weight:700">Detail Pendaftaran</a>.
                      @endif
                    </p>
                  </div>
                </div>
                <div class="inv-step">
                  <span class="inv-step-n">3</span>
                  <div>
                    <p class="inv-step-t">Tunggu verifikasi panitia</p>
                    <p class="inv-step-p">Status akan berubah menjadi <b>Lunas</b> setelah bukti diverifikasi. Pantau halaman ini — status diperbarui otomatis.</p>
                  </div>
                </div>
              </div>
            @else
              {{-- ===== ONLINE (Xendit) ===== --}}
              @if ($canPayOnline)
                <div class="inv-method primary">
                  <span class="inv-m-ic"><i class="fa-solid fa-bolt"></i></span>
                  <div>
                    <p class="inv-m-ttl">Lanjut Bayar Online (Xendit)</p>
                    <p class="inv-m-desc">Transfer Bank, E-Wallet, atau Retail Store. Pembayaran diproses otomatis.</p>
                  </div>
                  <div class="inv-m-foot">
                    <a href="{{ $payment->xendit_invoice_url }}" target="_blank" rel="noopener" class="inv-btn coral" style="width:100%">
                      <i class="fa-solid fa-credit-card"></i> Lanjut Bayar Online
                    </a>
                  </div>
                </div>
              @endif
            @endif
          </div>
        @endif
        </div>{{-- /inv-pay-body --}}
      </section>

      {{-- Bukti Transfer section --}}
      @if ($isManual || $hasProof)
      <section class="inv-sec">
        <div class="inv-sec-head">
          <div class="inv-sec-ic {{ $hasProof ? 'green' : 'amber' }}"><i class="fa-solid {{ $hasProof ? 'fa-file-circle-check' : 'fa-file-circle-question' }}"></i></div>
          <div>
            <p class="inv-sec-ttl">Bukti Transfer</p>
            <p class="inv-sec-desc">Status bukti pembayaran yang kamu unggah.</p>
          </div>
        </div>

        <div class="inv-proof">
          <span class="inv-proof-ic {{ $hasProof ? 'green' : 'gray' }}"><i class="fa-solid {{ $hasProof ? 'fa-circle-check' : 'fa-cloud-arrow-up' }}"></i></span>
          <div class="inv-proof-info">
            <p class="inv-proof-t">{{ $hasProof ? 'Bukti transfer sudah diunggah' : 'Belum ada bukti transfer' }}</p>
            <p class="inv-proof-p">
              @if ($hasProof)
                Diunggah pada {{ $proofUploadedAt }}. Panitia akan memverifikasi bukti kamu.
              @else
                Lakukan transfer lalu unggah bukti melalui halaman pendaftaran.
              @endif
            </p>
          </div>
          @if ($hasProof && $proofUrl)
            <div class="inv-proof-action">
              <a href="{{ $proofUrl }}" target="_blank" rel="noopener" class="inv-btn ghost">
                <i class="fa-solid fa-image"></i> Lihat Bukti Transfer
              </a>
            </div>
          @endif
        </div>
      </section>
      @endif

      {{-- Actions --}}
      <div class="inv-actions">
        @if ($canPrintInvoice)
          <a href="{{ route('payments.invoice', $payment) }}" target="_blank" class="inv-btn ghost" id="inv-print-btn">
            <i class="fa-solid fa-download"></i> Download PDF
          </a>
        @endif
        <a href="{{ route('registration.show', $registration) }}" class="inv-btn ghost">
          <i class="fa-solid fa-arrow-left"></i> Kembali ke Pendaftaran
        </a>
      </div>

    </div>
  </div>

  @push('scripts')
  <script>
    // ===== Copy helper (clipboard + fallback) =====
    function invCopy(text, btn){
      function done(){
        if(!btn) return;
        var old = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin';
        btn.classList.add('copied');
        if(window.showToast) showToast('Nomor berhasil disalin');
        setTimeout(function(){ btn.innerHTML = old; btn.classList.remove('copied'); }, 1800);
      }
      if(navigator.clipboard && navigator.clipboard.writeText){
        navigator.clipboard.writeText(text).then(done).catch(function(){ invCopyFallback(text, done); });
      } else {
        invCopyFallback(text, done);
      }
    }
    function invCopyFallback(text, done){
      var ta = document.createElement('textarea');
      ta.value = text;
      ta.style.position = 'fixed';
      ta.style.opacity = '0';
      document.body.appendChild(ta);
      ta.select();
      try { document.execCommand('copy'); done(); } catch(e) {}
      document.body.removeChild(ta);
    }

    // ===== Live countdown =====
    document.addEventListener('DOMContentLoaded', function(){
      var el = document.getElementById('inv-countdown');
      if(!el) return;
      var deadline = parseInt(el.getAttribute('data-deadline'), 10) * 1000;
      function fmt(n){ return String(n).padStart(2,'0'); }
      function tick(){
        var diff = deadline - Date.now();
        if(diff <= 0){ el.textContent = 'Batas waktu telah berakhir'; return; }
        var s = Math.floor(diff/1000);
        var d = Math.floor(s/86400), h = Math.floor((s%86400)/3600), m = Math.floor((s%3600)/60), sec = s%60;
        el.textContent = d > 0
          ? d + ' hari ' + h + ' jam ' + m + ' menit'
          : h + ':' + fmt(m) + ':' + fmt(sec) + ' jam';
      }
      tick();
      // Simpan timer di window agar bisa dihentikan saat status berubah LUNAS/DITOLAK
      window._invCountdownTimer = setInterval(tick, 1000);
    });

    // ===== Auto-refresh status pembayaran (tanpa reload) =====
    // Polling aktif untuk SEMUA pembayaran yang masih pending (belum lunas):
    // - Online: menunggu konfirmasi Xendit
    // - Manual: menunggu verifikasi bukti oleh panitia
    document.addEventListener('DOMContentLoaded', function(){
      var isOnline = {{ $isOnline ? 'true' : 'false' }};
      var hasProof = {{ $hasProof ? 'true' : 'false' }};
      var wasPending = {{ $isPending ? 'true' : 'false' }};
      var pollUrl = {{ Js::from(route('payments.invoice.status', $payment)) }};

      if(!wasPending) return;

      function statusInfo(verified, rejected){
        if(verified) return { label:'LUNAS', icon:'fa-circle-check', tone:'green' };
        if(rejected) return { label:'DITOLAK', icon:'fa-circle-xmark', tone:'red' };
        if(hasProof) return { label:'MENUNGGU VERIFIKASI', icon:'fa-hourglass-half', tone:'amber' };
        return { label:'MENUNGGU PEMBAYARAN', icon:'fa-clock', tone:'amber' };
      }

      function setStatus(verified, rejected){
        var s = statusInfo(verified, rejected);
        var pill = document.getElementById('inv-status-pill');
        var icon = document.getElementById('inv-status-icon');
        var amountStatus = document.getElementById('inv-amount-status');
        var amountIcon = document.getElementById('inv-amount-status-icon');
        if(pill){
          pill.className = 'inv-pill ' + s.tone + ' inv-school-badge';
          pill.innerHTML = '<i class="fa-solid ' + s.icon + '"></i> ' + s.label;
        }
        if(amountStatus){
          amountStatus.className = 'inv-pill ' + s.tone + ' inv-amount-status';
          amountStatus.innerHTML = '<i class="fa-solid ' + s.icon + '"></i> ' + s.label;
        }
        if(icon) icon.className = 'fa-solid ' + s.icon;
        if(amountIcon) amountIcon.className = 'fa-solid ' + s.icon;
        // Sesuaikan banner: batas waktu hanya relevan saat masih menunggu pembayaran.
        // Saat LUNAS → ganti banner sukses; saat DITOLAK → sembunyikan banner.
        swapDeadlineBanner(verified, rejected);
      }

      // Ganti banner batas waktu sesuai status:
      // - pending (belum dibayar)  → banner batas waktu tetap tampil
      // - LUNAS                    → banner batas waktu dihapus, banner sukses hijau muncul
      // - DITOLAK                  → banner batas waktu dihapus, tanpa pengganti
      function swapDeadlineBanner(verified, rejected){
        var banner = document.getElementById('inv-deadline-banner');
        var success = document.getElementById('inv-success-banner');
        if(!banner && !success) return; // halaman dirender langsung dalam status final → tidak ada banner deadline

        if(verified){
          // Hentikan countdown — pembayaran sudah selesai
          if(window._invCountdownTimer){ clearInterval(window._invCountdownTimer); window._invCountdownTimer = null; }
          if(banner) banner.remove();
          if(!success){
            // Buat banner sukses di posisi yang sama (sebelum amount highlight)
            var amount = document.querySelector('.inv-amount');
            var wrap = document.createElement('div');
            wrap.className = 'inv-deadline green';
            wrap.id = 'inv-success-banner';
            wrap.innerHTML = '<i class="fa-solid fa-circle-check inv-dl-ic"></i><div><p class="inv-dl-t">Pembayaran berhasil diverifikasi</p><p class="inv-dl-p">Tagihan ini sudah lunas. Terima kasih!</p></div>';
            if(amount && amount.parentNode){ amount.parentNode.insertBefore(wrap, amount); }
          }
        } else if(rejected){
          // Hentikan countdown — tidak perlu mengejar deadline lagi
          if(window._invCountdownTimer){ clearInterval(window._invCountdownTimer); window._invCountdownTimer = null; }
          if(banner) banner.remove();
          if(success) success.remove();
        }
        // else: masih pending → banner batas waktu & countdown tetap berjalan
      }

      function showPrintButton(){
        var btn = document.getElementById('inv-print-btn');
        if(btn){ btn.style.display = 'inline-flex'; return; }
        // Tombol belum ada di DOM (online dirender saat masih pending) → buat sekarang
        var downloadUrl = {{ Js::from(route('payments.invoice', $payment)) }};
        var actions = document.querySelector('.inv-actions');
        if(!actions) return;
        var a = document.createElement('a');
        a.id = 'inv-print-btn';
        a.href = downloadUrl;
        a.target = '_blank';
        a.rel = 'noopener';
        a.className = 'inv-btn ghost';
        a.innerHTML = '<i class="fa-solid fa-download"></i> Download PDF';
        // Taruh di posisi pertama (sebelum tombol Kembali)
        actions.insertBefore(a, actions.firstChild);
      }

      function payBodyVerified(){
        var sec = document.getElementById('inv-pay-body');
        if(sec) sec.innerHTML = '<div class="inv-alert green"><i class="fa-solid fa-circle-check"></i><div><p class="inv-alert-t">Pembayaran telah lunas</p><p class="inv-alert-p">Tagihan ini sudah diverifikasi oleh panitia. Terima kasih!</p></div></div>';
      }
      function payBodyRejected(){
        var sec = document.getElementById('inv-pay-body');
        if(sec) sec.innerHTML = '<div class="inv-alert red"><i class="fa-solid fa-circle-xmark"></i><div><p class="inv-alert-t">Pembayaran ditolak</p><p class="inv-alert-p">Silakan lakukan pembayaran ulang melalui halaman pendaftaran.</p></div></div>';
      }

      function poll(){
        fetch(pollUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
          .then(function(r){ return r.json(); })
          .then(function(data){
            if(data.payment_status === 'verified'){
              setStatus(true, false);
              showPrintButton();
              clearInterval(timer);
              if(window.showToast) showToast('Pembayaran berhasil — invoice dapat dicetak');
              payBodyVerified();
              return;
            }
            if(data.payment_status === 'rejected'){
              setStatus(false, true);
              clearInterval(timer);
              payBodyRejected();
              return;
            }
            // Masih pending — jika status bukti berubah (mis. dari polling sebelumnya),
            // perbarui label sesuai data terbaru.
            if(typeof data.has_proof === 'boolean' && data.has_proof !== hasProof){
              hasProof = data.has_proof;
              setStatus(false, false);
            }
          })
          .catch(function(){ /* jaringan error — coba lagi di tick berikutnya */ });
      }

      var timer = setInterval(poll, 5000);
    });
  </script>
  @endpush
</x-student-layout>

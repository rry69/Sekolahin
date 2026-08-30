<x-student-layout title="Pendaftaran">
  @php
    // Helper: konversi kelas Tailwind StatusBadge ke tone reg-ic-*
    $statTone = function(string $cls): string {
      if (str_contains($cls, 'emerald')) return 'green';
      if (str_contains($cls, 'red')) return 'red';
      if (str_contains($cls, 'yellow')) return 'amber';
      if (str_contains($cls, 'blue')) return 'blue';
      if (str_contains($cls, 'indigo')) return 'indigo';
      return 'gray';
    };

    // ===== Statistik progres (bermakna walau hanya 1 pendaftaran) =====
    $statusCard = \App\Support\StatusBadge::registrationStatusCard($activeRegistration?->status ?? null);
    $paymentCard = \App\Support\StatusBadge::paymentStatusCard($activeRegistration?->payment_status ?? null);

    $docPct = $docStats['total'] > 0 ? round(($docStats['verified'] / $docStats['total']) * 100) : 0;
    $docAllVerified = $docStats['total'] > 0 && $docStats['verified'] >= $docStats['total'];

    $deadlineInfo = ['label' => '-', 'cls' => 'reg-gray', 'icon' => 'fa-hourglass-half', 'txt' => 'Tidak ada batas waktu aktif'];
    if ($deadline) {
      if ($deadline['expired']) {
        $deadlineInfo = ['label' => 'Terlewati', 'cls' => 'reg-red', 'icon' => 'fa-triangle-exclamation', 'txt' => 'Pendaftaran akan dibatalkan otomatis'];
      } elseif ($deadline['hours'] !== null && $deadline['hours'] <= 24) {
        $deadlineInfo = ['label' => $deadline['label'], 'cls' => 'reg-amber', 'icon' => 'fa-hourglass-end', 'txt' => 'Sisa waktu penyelesaian pendaftaran'];
      } else {
        $deadlineInfo = ['label' => $deadline['label'], 'cls' => 'reg-blue', 'icon' => 'fa-hourglass-half', 'txt' => 'Sisa waktu penyelesaian pendaftaran'];
      }
    }

    // ===== Timeline alur pendaftaran =====
    $stepDefs = [
      ['label' => 'Profil', 'desc' => 'Biodata lengkap'],
      ['label' => 'Daftar', 'desc' => 'Pendaftaran dibuat'],
      ['label' => 'Dokumen', 'desc' => 'Berkas diupload'],
      ['label' => 'Verifikasi', 'desc' => 'Dicek panitia'],
      ['label' => 'Bayar', 'desc' => 'Pembayaran lunas'],
      ['label' => 'Diterima', 'desc' => 'Daftar ulang'],
    ];
    $flowState = 'normal';
    $stepState = ['todo', 'todo', 'todo', 'todo', 'todo', 'todo'];
    if ($activeRegistration) {
      $regStatus = $activeRegistration->status;
      $regPaid = $activeRegistration->payment_status === 'paid';
      $allDocs = $docStats['total'] > 0 && $docStats['uploaded'] >= $docStats['total'];
      $someDocs = $docStats['uploaded'] > 0;

      if ($regStatus === 'canceled') $flowState = 'canceled';
      elseif ($regStatus === 'withdrawn') $flowState = 'withdrawn';
      elseif ($regStatus === 'rejected') $flowState = 'rejected';

      $stepState[0] = 'done'; $stepState[1] = 'done';

      if ($allDocs) $stepState[2] = 'done';
      elseif ($someDocs) $stepState[2] = 'current';

      if (in_array($regStatus, ['verified', 'accepted', 're_registration_complete'])) $stepState[3] = 'done';
      if ($regPaid) $stepState[4] = 'done';
      if (in_array($regStatus, ['accepted', 're_registration_complete'])) $stepState[5] = 'done';

      if ($flowState === 'rejected') $stepState[3] = 'rejected';
      elseif ($flowState === 'canceled' || $flowState === 'withdrawn') $stepState = array_fill(0, 6, 'todo');
      else {
        $alreadyCurrent = in_array('current', $stepState, true);
        if (!$alreadyCurrent) {
          foreach ($stepState as $i => $s) { if ($s === 'todo') { $stepState[$i] = 'current'; break; } }
        }
      }
    }

    // ===== Kartu statistik (reusable config) =====
    $statBlocks = [
      ['label' => 'Dokumen Terverifikasi', 'value' => '<span class="reg-stat-num">'.$docStats['verified'].'<span class="reg-stat-slash">/'.$docStats['total'].'</span></span>',
        'icon' => 'fa-file-lines', 'cls' => 'reg-ic-blue',
        'sub' => $docAllVerified ? 'Semua berkas terverifikasi' : ($docStats['uploaded'] > 0 ? $docStats['uploaded'].' berkas terupload' : 'Belum ada berkas diupload'),
        'bar' => true, 'pct' => $docPct, 'barDone' => $docAllVerified],
      ['label' => 'Pembayaran', 'value' => $paymentCard['label'], 'icon' => $paymentCard['icon'], 'cls' => 'reg-ic-'.$statTone($paymentCard['cls']),
        'sub' => match ($activeRegistration?->payment_status) { 'paid' => 'Pembayaran sudah lunas', 'pending' => 'Menunggu konfirmasi panitia', 'failed' => 'Pembayaran gagal — coba lagi', default => 'Belum ada pembayaran' }],
      ['label' => 'Batas Waktu', 'value' => $deadlineInfo['label'], 'icon' => $deadlineInfo['icon'], 'cls' => 'reg-ic-'.$deadlineInfo['cls'],
        'sub' => $deadlineInfo['txt']],
      ['label' => 'Tahap Saat Ini', 'value' => $statusCard['label'], 'icon' => 'fa-route', 'cls' => 'reg-ic-'.$statTone($statusCard['cls']),
        'sub' => match ($activeRegistration?->status) {
          'accepted' => 'Segera lakukan daftar ulang',
          're_registration_complete' => 'Proses selesai — selamat!',
          'rejected' => 'Perbaiki sesuai catatan panitia',
          'canceled' => 'Pendaftaran dibatalkan',
          'withdrawn' => 'Pendaftaran dibatalkan (mengundurkan diri)',
          default => 'Ikuti langkah pada alur di bawah',
        }],
    ];
  @endphp

  <style>
    .reg {
      --coral:#FF6B6B; --coral-2:#FF8E6E; --coral-soft:#FFE5E3;
      --ink:#1a1a2e; --muted:#8a8f9d; --divider:rgba(26,26,46,.10);
      --green:#10B981; --green-soft:#D1FAE5; --red:#EF4444; --red-soft:#FEE2E2;
      --amber:#D97706; --amber-soft:#FEF3C7; --blue:#2563EB; --blue-soft:#DBEAFE; --indigo:#6366F1; --indigo-soft:#E0E7FF;
      position:relative; border-radius:24px; padding:28px 28px 44px; background:#f6f7fb;
    }
    .reg .reg-inner { max-width:1080px; margin:0 auto; }
    .reg-crumb { font-size:12.5px; color:var(--muted); margin-bottom:6px; display:flex; align-items:center; gap:7px; flex-wrap:wrap; }
    .reg-crumb a { color:var(--coral); font-weight:600; }
    .reg-crumb a:hover { text-decoration:underline; }
    .reg-title { font-size:26px; font-weight:800; color:var(--ink); letter-spacing:-0.01em; line-height:1.2; }
    .reg-meta { font-size:13px; color:var(--muted); margin-top:6px; }

    /* alerts */
    .reg-alert { display:flex; gap:13px; align-items:flex-start; border-radius:14px; padding:14px 16px; margin-top:20px; border:1px solid transparent; }
    .reg-alert svg.hi.reg-alert-ic { font-size:16px; flex:0 0 auto; margin-top:2px; }
    .reg-alert .reg-alert-body { flex:1; min-width:0; }
    .reg-alert .reg-alert-t { font-weight:700; font-size:13.5px; }
    .reg-alert .reg-alert-p { font-size:13px; margin-top:2px; opacity:.92; }
    .reg-alert .reg-alert-a { display:inline-flex; align-items:center; gap:6px; margin-top:10px; padding:8px 15px; border-radius:10px; font-size:12.5px; font-weight:700; color:#fff; }
    .reg-alert.red { background:var(--red-soft); border-color:rgba(239,68,68,.25); }
    .reg-alert.red svg.hi.reg-alert-ic { color:var(--red); }
    .reg-alert.red .reg-alert-t, .reg-alert.red .reg-alert-p { color:#B91C1C; }
    .reg-alert.red .reg-alert-a { background:var(--red); }
    .reg-alert.amber { background:var(--amber-soft); border-color:rgba(217,119,6,.3); }
    .reg-alert.amber svg.hi.reg-alert-ic { color:var(--amber); }
    .reg-alert.amber .reg-alert-t, .reg-alert.amber .reg-alert-p { color:#B45309; }
    .reg-alert.amber .reg-alert-a { background:var(--amber); }
    .reg-alert.blue { background:var(--blue-soft); border-color:rgba(37,99,235,.25); }
    .reg-alert.blue svg.hi.reg-alert-ic { color:var(--blue); }
    .reg-alert.blue .reg-alert-t, .reg-alert.blue .reg-alert-p { color:#1D4ED8; }
    .reg-alert.blue .reg-alert-a { background:var(--blue); }
    .reg-alert.info { background:var(--indigo-soft); border-color:rgba(99,102,241,.25); }
    .reg-alert.info svg.hi.reg-alert-ic { color:var(--indigo); }
    .reg-alert.info .reg-alert-t, .reg-alert.info .reg-alert-p { color:#4338CA; }
    .reg-alert.info .reg-alert-a { background:var(--indigo); }
    .reg-alert.success { background:var(--green-soft); border-color:rgba(16,185,129,.3); }
    .reg-alert.success svg.hi.reg-alert-ic { color:var(--green); }
    .reg-alert.success .reg-alert-t, .reg-alert.success .reg-alert-p { color:#047857; }

    /* re-registration reminder restyle (component uses indigo classes) */
    .reg .mb-4.bg-indigo-50, .reg .mb-4.bg-indigo-50.border-l-4 { background:var(--indigo-soft); border-left-color:var(--indigo); border-radius:14px; padding:14px 16px; }
    .reg .mb-4.bg-indigo-50 p.text-indigo-900 { color:#4338CA; font-weight:700; font-size:13.5px; }
    .reg .mb-4.bg-indigo-50 p.text-indigo-800 { color:#3730A3; font-size:13px; }
    .reg .mb-4.bg-indigo-50 p.text-indigo-700 { color:#4338CA; font-size:12px; }
    .reg .mb-4.bg-indigo-50 a.bg-indigo-600 { background:linear-gradient(135deg,var(--coral),var(--coral-2)); border-radius:10px; font-weight:700; box-shadow:0 8px 18px -8px rgba(255,107,107,.6); }

    /* stat blocks */
    .reg-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); margin-top:22px; border-top:1px solid var(--divider); }
    .reg-stat { padding:20px 18px 18px 0; }
    .reg-stat + .reg-stat { border-left:1px solid var(--divider); padding-left:18px; }
    .reg-stat-head { display:flex; align-items:flex-start; justify-content:space-between; gap:10px; }
    .reg-stat-label { font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); }
    .reg-stat-ic { display:inline-flex; align-items:center; justify-content:center; font-size:22px; line-height:1; flex:0 0 auto; background:none; border-radius:0; box-shadow:none; width:auto; height:auto; }
    .reg-stat-num { font-size:26px; font-weight:800; color:var(--ink); line-height:1.1; }
    .reg-stat-slash { font-size:15px; color:var(--muted); font-weight:500; }
    .reg-stat-val { font-size:20px; font-weight:800; color:var(--ink); line-height:1.2; }
    .reg-stat-sub { font-size:12px; color:var(--muted); margin-top:10px; }
    .reg-stat-bar { height:6px; background:rgba(26,26,46,.08); border-radius:99px; overflow:hidden; margin-top:12px; }
    .reg-stat-bar i { display:block; height:100%; border-radius:99px; background:linear-gradient(90deg,var(--green),#34D399); }
    .reg-stat-bar i.blue { background:linear-gradient(90deg,var(--blue),#60A5FA); }

    /* timeline stepper v2 — numbered + connector + popover */
        .reg-timeline { margin-top:6px; border-top:1px solid var(--divider); padding:24px 0 8px; }
        .reg-tl-head { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:20px; }
        .reg-tl-ttl { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--ink); }
        .reg-tl-badge { font-size:11px; font-weight:700; color:#B91C1C; background: transparent; border: 1px solid currentColor; padding:5px 12px; border-radius:99px; }
        .reg-tl-meta { font-size:11.5px; font-weight:600; color:var(--muted); }
        .reg-steps { display:flex; position:relative; }
        .reg-step { position:relative; flex:1 1 0; min-width:0; display:flex; flex-direction:column; align-items:center; text-align:center; padding:0 6px; cursor:pointer; }
        /* connector antar step */
        .reg-step:not(:first-child)::before { content:''; position:absolute; top:18px; left:-50%; right:50%; height:3px; background:#E5E7EB; z-index:1; transition:background .25s; }
        .reg-step.done:not(:first-child)::before { background:linear-gradient(90deg,var(--green),#34D399); }
        .reg-step.current:not(:first-child)::before { background:linear-gradient(90deg,var(--green),var(--coral)); }
        .reg-step.rejected:not(:first-child)::before { background:var(--red); }
        /* lingkaran nomor / status */
        .reg-step-ic { position:relative; z-index:2; width:38px; height:38px; border-radius:50%; border:2px solid #D1D5DB; background:#fff; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; color:#9CA3AF; transition:transform .18s, box-shadow .18s; }
        .reg-step-ic i { font-size:13px; line-height:1; }
        .reg-step:hover .reg-step-ic { transform:translateY(-2px); }
        .reg-step.done .reg-step-ic { background:linear-gradient(135deg,var(--green),#34D399); border-color:var(--green); color:#fff; box-shadow:0 6px 14px -6px rgba(16,185,129,.55); }
        .reg-step.current .reg-step-ic { border-color:var(--coral); color:var(--coral); background:#fff; box-shadow:0 0 0 4px rgba(255,107,107,.18); animation:regPulse 2s infinite; }
        .reg-step.rejected .reg-step-ic { background:var(--red); border-color:var(--red); color:#fff; box-shadow:0 6px 14px -6px rgba(239,68,68,.55); }
        @keyframes regPulse { 0%,100% { box-shadow:0 0 0 4px rgba(255,107,107,.18);} 50% { box-shadow:0 0 0 7px rgba(255,107,107,.10);} }
        /* label */
        .reg-step-lb { margin-top:9px; font-size:11.5px; font-weight:700; color:var(--muted); transition:color .18s; }
        .reg-step.done .reg-step-lb { color:var(--green); }
        .reg-step.current .reg-step-lb { color:var(--coral); }
        .reg-step.rejected .reg-step-lb { color:var(--red); }
        .reg-step:hover .reg-step-lb { color:var(--ink); }
        .reg-step-desc { font-size:10px; color:var(--muted); margin-top:2px; }
        /* popover detail per step */
        .reg-pop { position:absolute; bottom:calc(100% + 12px); left:50%; transform:translateX(-50%) translateY(4px); width:230px; background:#fff; border-radius:14px; box-shadow:0 18px 40px -12px rgba(26,26,46,.28);  padding:14px 15px; opacity:0; visibility:hidden; transition:opacity .16s, transform .16s, visibility .16s; z-index:30; text-align:left; }
        .reg-pop::after { content:''; position:absolute; top:100%; left:50%; transform:translateX(-50%); border:7px solid transparent; border-top-color:#fff; }
        .reg-step.open .reg-pop { opacity:1; visibility:visible; transform:translateX(-50%) translateY(0); }
        .reg-step:first-child .reg-pop { left:0; transform:translateY(4px); }
        .reg-step:first-child.open .reg-pop, .reg-step:first-child:hover .reg-pop { transform:translateY(0); }
        .reg-step:last-child .reg-pop { left:auto; right:0; transform:translateY(4px); }
        .reg-step:last-child.open .reg-pop, .reg-step:last-child:hover .reg-pop { transform:translateY(0); }
        .reg-step:first-child .reg-pop::after { left:22px; }
        .reg-step:last-child .reg-pop::after { left:auto; right:22px; }
        @media (hover:hover) {
          .reg-step:hover .reg-pop { opacity:1; visibility:visible; transform:translateX(-50%) translateY(0); }
        }
        .reg-pop-t { font-size:12px; font-weight:800; color:var(--ink); display:flex; align-items:center; gap:6px; margin-bottom:8px; }
        .reg-pop-t svg.hi { font-size:11px; color:var(--coral); }
        .reg-pop-row { display:flex; gap:8px; font-size:11.5px; color:var(--muted); margin-top:5px; line-height:1.4; }
        .reg-pop-row b { font-weight:700; color:var(--ink); white-space:nowrap; }
        .reg-pop-note { font-size:11px; margin-top:8px; padding:7px 9px; border-radius:8px; background:var(--gray-soft); color:var(--muted); line-height:1.45; }
        .reg-pop-note.ok { background:var(--green-soft); color:#047857; }
        .reg-pop-note.warn { background:var(--amber-soft); color:#B45309; }
        .reg-pop-note.bad { background:var(--red-soft); color:#B91C1C; }
        .reg-pop-a { display:inline-flex; align-items:center; gap:6px; margin-top:10px; font-size:11.5px; font-weight:700; color:var(--coral); text-decoration:none; }
        .reg-pop-a:hover { text-decoration:underline; }

    /* list */
    .reg-list { margin-top:6px; display:flex; flex-direction:column; border-top:1px solid var(--divider); padding-top:22px; }
    .reg-list-hd { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:8px; }
    .reg-list-ttl { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--ink); }
    .reg-row { display:flex; align-items:center; gap:16px; padding:18px 6px; border-top:1px solid var(--divider); transition:background .15s; }
    .reg-row:first-of-type { border-top:none; padding-top:10px; }
    .reg-row:hover { background:rgba(255,255,255,.45); }
    .reg-row-ic { display:inline-flex; align-items:center; justify-content:center; font-size:24px; line-height:1; flex:0 0 auto; background:none; border-radius:0; box-shadow:none; width:auto; height:auto; color:var(--coral); }
    .reg-row-body { flex:1; min-width:0; }
    .reg-row-no { font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:.03em; }
    .reg-row-title { font-size:15px; font-weight:700; color:var(--ink); margin-top:2px; display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .reg-row-meta { display:flex; flex-wrap:wrap; gap:6px 16px; margin-top:7px; font-size:12px; color:var(--muted); }
    .reg-row-meta b { font-weight:600; color:var(--ink); }
    .reg-row-right { flex:0 0 auto; display:flex; flex-direction:column; align-items:flex-end; gap:10px; }
    .reg-pill { display:inline-flex; align-items:center; gap:6px; padding:4px 12px; border-radius:99px; font-size:11px; font-weight:700; }
    .reg-pill.green { background: transparent; border: 1px solid currentColor; color:#047857; }
    .reg-pill.amber { background: transparent; border: 1px solid currentColor; color:#B45309; }
    .reg-pill.red { background: transparent; border: 1px solid currentColor; color:#B91C1C; }
    .reg-pill.blue { background: transparent; border: 1px solid currentColor; color:#1D4ED8; }
    .reg-pill.gray { background: transparent; border: 1px solid currentColor; color:var(--gray); }
    .reg-pill.indigo { background: transparent; border: 1px solid currentColor; color:#4338CA; }
    .reg-deadline { font-size:11.5px; display:inline-flex; align-items:center; gap:6px; }
    .reg-deadline.over { color:var(--red); font-weight:700; }
    .reg-deadline.soon { color:var(--amber); font-weight:700; }
    .reg-deadline.normal { color:var(--muted); }
    .reg-doc { display:flex; align-items:center; gap:8px; font-size:12px; color:var(--muted); }
    .reg-doc b { color:var(--ink); }
    .reg-doc-bar { width:54px; height:5px; background:rgba(26,26,46,.08); border-radius:99px; overflow:hidden; }
    .reg-doc-bar i { display:block; height:100%; background:linear-gradient(90deg,var(--coral),var(--coral-2)); }
    .reg-doc-bar i.ok { background:linear-gradient(90deg,var(--green),#34D399); }
    .reg-link { display:inline-flex; align-items:center; gap:6px; font-size:12.5px; font-weight:700; color:var(--coral); }
    .reg-link:hover { text-decoration:underline; }

    /* buttons */
    .reg-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:11px 18px; border-radius:11px; font-size:13px; font-weight:700; transition:transform .15s, box-shadow .15s; }
    .reg-btn.coral { background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; box-shadow:0 10px 22px -10px rgba(255,107,107,.65); }
    .reg-btn.coral:hover { transform:translateY(-1px); box-shadow:0 14px 26px -10px rgba(255,107,107,.7); }

    /* ===== Prasyarat banner (biodata siap / belum) ===== */
    .reg-prereq { margin-top:22px; border-radius:14px; padding:14px 16px; display:flex; align-items:center; gap:13px; flex-wrap:wrap; border:1px solid transparent; }
    .reg-prereq.ready { background:var(--green-soft); border-color:rgba(16,185,129,.3); }
    .reg-prereq.warn { background:var(--amber-soft); border-color:rgba(217,119,6,.32); }
    .reg-prereq-ic { display:inline-flex; align-items:center; justify-content:center; font-size:22px; line-height:1; flex:0 0 auto; background:none; border-radius:0; box-shadow:none; width:auto; height:auto; }
    .reg-prereq.ready .reg-prereq-ic { color:var(--green); }
    .reg-prereq.warn .reg-prereq-ic { color:var(--amber); }
    .reg-prereq-body { flex:1; min-width:0; }
    .reg-prereq-t { font-weight:700; font-size:13.5px; }
    .reg-prereq.ready .reg-prereq-t { color:#047857; }
    .reg-prereq.warn .reg-prereq-t { color:#B45309; }
    .reg-prereq-p { font-size:13px; margin-top:1px; }
    .reg-prereq.ready .reg-prereq-p { color:#059669; }
    .reg-prereq.warn .reg-prereq-p { color:#92400E; }
    .reg-prereq-btn { display:inline-flex; align-items:center; gap:7px; padding:9px 16px; border-radius:10px; font-size:12.5px; font-weight:700; color:#fff; }
    .reg-prereq.warn .reg-prereq-btn { background:var(--amber); box-shadow:0 8px 18px -8px rgba(217,119,6,.6); }
    .reg-prereq.warn .reg-prereq-btn:hover { filter:brightness(1.05); transform:translateY(-1px); }
    .reg-prereq.ready .reg-prereq-btn { background:linear-gradient(135deg,var(--green),#34D399); box-shadow:0 8px 18px -8px rgba(16,185,129,.6); }

    /* ===== Hero CTA card ===== */
    .reg-hero { margin:6px auto 0; max-width:640px; text-align:center; padding:44px 30px 34px; border-top:1px solid var(--divider); }
    .reg-hero-ic { display:inline-flex; align-items:center; justify-content:center; margin:0 auto; font-size:40px; line-height:1; color:var(--coral); background:none; border-radius:0; box-shadow:none; width:auto; height:auto; position:relative; }
    .reg-hero h3 { font-size:22px; font-weight:800; color:var(--ink); margin-top:24px; letter-spacing:-0.01em; }
    .reg-hero p { max-width:430px; margin:9px auto 0; font-size:14px; line-height:1.6; color:var(--muted); }
    .reg-hero .reg-btn { margin-top:26px; padding:13px 26px; font-size:14px; border-radius:12px; }
    .reg-hero .reg-btn svg.hi { transition:transform .18s; }
    .reg-hero .reg-btn:hover svg.hi { transform:translateX(2px) scale(1.1); }

    /* ===== Alur Pendaftaran stepper (4 langkah) ===== */
    .reg-flow { margin-top:26px; }
    .reg-flow-hd { display:flex; align-items:center; gap:10px; margin-bottom:4px; }
    .reg-flow-ttl { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--ink); }
    .reg-flow-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); margin-top:0; }
    .reg-flow-step { position:relative; padding:22px 16px 10px 0; text-align:left; }
    .reg-flow-step + .reg-flow-step { border-left:1px solid var(--divider); padding-left:18px; }
    .reg-flow-num { width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg,var(--coral),var(--coral-2)); color:#fff; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:800; box-shadow:0 8px 16px -8px rgba(255,107,107,.6); }
    .reg-flow-t { font-size:13.5px; font-weight:700; color:var(--ink); margin-top:12px; }
    .reg-flow-d { font-size:12px; color:var(--muted); margin-top:3px; line-height:1.45; }

    /* ===== Widgets grid (Jadwal + Bantuan) ===== */
    .reg-widgets { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); border-top:1px solid var(--divider); margin-top:6px; padding-top:24px; }
    .reg-widget { padding:0 20px 0 0; }
    .reg-widget + .reg-widget { border-left:1px solid var(--divider); padding-left:24px; }
    .reg-widget-hd { display:flex; align-items:center; gap:11px; margin-bottom:16px; }
    .reg-widget-ic { display:inline-flex; align-items:center; justify-content:center; font-size:22px; line-height:1; flex:0 0 auto; background:none; border-radius:0; box-shadow:none; width:auto; height:auto; }
    .reg-widget-ic.cal { color:var(--blue); }
    .reg-widget-ic.help { color:var(--indigo); }
    .reg-widget-ttl { font-size:13px; font-weight:700; color:var(--ink); }
    .reg-widget-sub { font-size:11px; color:var(--muted); margin-top:1px; }
    .reg-jadwal-empty { font-size:13px; color:var(--muted); padding:8px 0; }
    .reg-jadwal-row { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; padding:11px 0; border-top:1px solid var(--divider); }
    .reg-jadwal-row:first-of-type { border-top:none; }
    .reg-jadwal-lv { font-size:12.5px; font-weight:700; color:var(--ink); }
    .reg-jadwal-name { font-size:11.5px; color:var(--muted); margin-top:1px; }
    .reg-jadwal-date { font-size:11.5px; color:var(--muted); margin-top:3px; display:flex; align-items:center; gap:5px; }
    .reg-jadwal-date svg.hi { font-size:9px; opacity:.7; }
    .reg-jadwal-badge { font-size:10.5px; font-weight:700; padding:4px 10px; border-radius:99px; flex:0 0 auto; }
    .reg-jadwal-badge.open { background: transparent; border: 1px solid currentColor; color:#047857; }
    .reg-jadwal-badge.closed { background: transparent; border: 1px solid currentColor; color:var(--gray); }
    .reg-widget-line { border-top:1px solid var(--divider); margin:6px 0 14px; }
    .reg-faq { }
    .reg-faq-item { padding:11px 0; border-top:1px solid var(--divider); }
    .reg-faq-item:first-of-type { border-top:none; padding-top:0; }
    .reg-faq-q { display:flex; align-items:center; gap:8px; font-size:13px; font-weight:700; color:var(--ink); cursor:pointer; }
    .reg-faq-q svg.hi { color:var(--coral); font-size:11px; }
    .reg-faq-a { font-size:12.5px; color:var(--muted); margin-top:5px; line-height:1.5; display:none; padding-left:19px; }
    .reg-faq-item.open .reg-faq-a { display:block; }
    .reg-faq-item.open .reg-faq-q svg.hi { transform:rotate(180deg); }
    .reg-help-cta { margin-top:14px; display:inline-flex; align-items:center; gap:8px; padding:10px 16px; border-radius:10px; font-size:12.5px; font-weight:700; color:var(--indigo); background:var(--indigo-soft); transition:background .15s, color .15s; }
    .reg-help-cta:hover { background:var(--indigo); color:#fff; }

    @media (max-width:1024px) {
      .reg-stats { grid-template-columns:repeat(2,minmax(0,1fr)); }
      .reg-flow-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
    }
    @media (max-width:640px) {
      .reg-stats { grid-template-columns:1fr; }
      .reg { padding:20px 18px 32px; border-radius:18px; }
      .reg-row { flex-wrap:wrap; }
      .reg-row-right { width:100%; align-items:flex-start; flex-direction:row; justify-content:space-between; }
      .reg-tl-head { flex-wrap:wrap; }
      .reg-tl-meta { width:100%; }
      .reg-step { min-width:0; padding:0 3px; }
      .reg-step-ic { width:32px; height:32px; font-size:12px; }
      .reg-step:not(:first-child)::before { top:16px; }
      .reg-step-lb { font-size:10px; }
      .reg-step-desc { font-size:9px; }
      .reg-pop { position:fixed; left:16px; right:16px; bottom:16px; top:auto; width:auto; transform:translateY(8px); z-index:95; }
      .reg-pop::after { display:none; }
      .reg-step.open .reg-pop, .reg-step:hover .reg-pop { transform:translateY(0); }
      .reg-step:first-child .reg-pop, .reg-step:last-child .reg-pop { left:16px; right:16px; transform:translateY(8px); }
      .reg-step:first-child.open .reg-pop, .reg-step:last-child.open .reg-pop { transform:translateY(0); }
      .reg-flow-grid { grid-template-columns:1fr; }
      .reg-widgets { grid-template-columns:1fr; }
      .reg-hero { padding:34px 20px 30px; }
      .reg-prereq { align-items:flex-start; }
    }
  </style>

  <div class="reg">
    <div class="reg-inner">
      {{-- Crumbs + title --}}
      <div class="reg-crumb">
        <a href="{{ route('dashboard') }}">Beranda</a>
        <x-hi icon="fa-chevron-right" style="font-size:9px" />
        <span>Pendaftaran</span>
      </div>
      <h1 class="reg-title">Pendaftaran</h1>
      <p class="reg-meta">Kelola pendaftaran murid baru Anda dan pantau progresnya.</p>

      {{-- Session success --}}
      @if (session('success'))
        <div class="reg-alert success">
          <x-hi icon="fa-circle-check" class="reg-alert-ic" />
          <div class="reg-alert-body">
            <p class="reg-alert-p">{{ session('success') }}</p>
          </div>
        </div>
      @endif

      @php
        $activeReg = $registrations->firstWhere(function ($r) {
            return $r->status === 'pending' && in_array($r->payment_status, ['unpaid', 'pending']);
        });
        $reminderReg = $registrations->firstWhere('status', 'accepted');
      @endphp

      {{-- Re-registration reminder (dipanggil component, gaya override scoped) --}}
      @if($reminderReg)
        <x-re-registration-reminder :registration="$reminderReg" />
      @endif

      {{-- Deadline alert banner --}}
      @if ($activeReg && $activeReg->deadline_at)
        @php
          $isExpired = $activeReg->isDeadlineExpired();
          $hoursRemaining = $activeReg->getDeadlineHoursRemaining();
        @endphp
        @if ($isExpired)
          <div class="reg-alert red">
            <x-hi icon="fa-triangle-exclamation" class="reg-alert-ic" />
            <div class="reg-alert-body">
              <p class="reg-alert-t">Batas waktu pendaftaran telah terlewati!</p>
              <p class="reg-alert-p">Pendaftaran {{ $activeReg->registration_number }} akan segera dibatalkan otomatis karena melebihi batas waktu.</p>
              <a href="{{ route('registration.show', $activeReg) }}" class="reg-alert-a">Lihat Detail <x-hi icon="fa-arrow-right" /></a>
            </div>
          </div>
        @elseif ($hoursRemaining !== null && $hoursRemaining <= 24)
          <div class="reg-alert amber">
            <x-hi icon="fa-hourglass-end" class="reg-alert-ic" />
            <div class="reg-alert-body">
              <p class="reg-alert-t">Sisa waktu pendaftaran: {{ $activeReg->getDeadlineLabel() }}</p>
              <p class="reg-alert-p">Segera lengkapi dokumen dan lakukan pembayaran sebelum pendaftaran {{ $activeReg->registration_number }} dibatalkan otomatis.</p>
              <a href="{{ route('registration.show', $activeReg) }}" class="reg-alert-a">Lihat Detail <x-hi icon="fa-arrow-right" /></a>
            </div>
          </div>
        @else
          <div class="reg-alert blue">
            <x-hi icon="fa-hourglass-half" class="reg-alert-ic" />
            <div class="reg-alert-body">
              <p class="reg-alert-t">Batas waktu pendaftaran: {{ $activeReg->deadline_at->format('d M Y H:i') }}</p>
              <p class="reg-alert-p">Pendaftaran {{ $activeReg->registration_number }} memiliki sisa waktu {{ $activeReg->getDeadlineLabel() }} untuk melengkapi dokumen dan pembayaran.</p>
              <a href="{{ route('registration.show', $activeReg) }}" class="reg-alert-a">Lihat Detail <x-hi icon="fa-arrow-right" /></a>
            </div>
          </div>
        @endif
      @endif

      @if ($activeRegistration)
        {{-- Kartu statistik progres --}}
        <div class="reg-stats">
          @foreach ($statBlocks as $b)
            <div class="reg-stat">
              <div class="reg-stat-head">
                <div>
                  <p class="reg-stat-label">{{ $b['label'] }}</p>
                  @if (isset($b['bar']))
                    <p class="reg-stat-num">{!! $b['value'] !!}</p>
                  @else
                    <p class="reg-stat-val">{{ $b['value'] }}</p>
                  @endif
                </div>
                <div class="reg-stat-ic {{ $b['cls'] }}"><x-hi icon="{{ $b['icon'] }}" /></div>
              </div>
              @if (isset($b['bar']))
                <div class="reg-stat-bar"><i class="{{ $b['barDone'] ? '' : 'blue' }}" style="width:{{ $b['pct'] }}%"></i></div>
              @endif
              <p class="reg-stat-sub">{{ $b['sub'] }}</p>
            </div>
          @endforeach
        </div>

        {{-- Timeline alur pendaftaran --}}
                @if ($flowState !== 'canceled' && $flowState !== 'withdrawn')
                  @php
                    // Detail dinamis per step (dipakai popover)
                    $applicantName = $applicant?->full_name ?? auth()->user()->name ?? '-';
                    $regNo = $activeRegistration?->registration_number ?? '-';
                    $schName = $activeRegistration?->school?->name ?? '-';
                    $mjrName = $activeRegistration?->major?->name ?? $activeRegistration?->finalMajor?->name ?? '-';
                    $trkName = $activeRegistration?->registrationTrack?->name ?? '-';
                    $payStatus = $activeRegistration?->payment_status ?? null;
                    $payAmount = $activeRegistration?->payment_amount;
                    $payLabel = match ($payStatus) { 'paid' => 'Lunas', 'pending' => 'Menunggu konfirmasi', 'failed' => 'Gagal — coba lagi', default => 'Belum ada' };
                    $payTone = match ($payStatus) { 'paid' => 'ok', 'failed' => 'bad', default => 'warn' };
                    $firstPaid = $activeRegistration?->payments()->first();
                    $docCnt = $docStats['verified'] ?? 0; $docTot = $docStats['total'] ?? 0;
                    $stepDetail = [
                      0 => ['t' => 'Profil', 'rows' => [['Nama', $applicantName]], 'note' => $applicant?->isProfileComplete() ? ['ok','Biodata lengkap — siap lanjut'] : ['warn','Biodata belum lengkap'], 'href' => $applicant?->isProfileComplete() ? null : route('applicant.profile'), 'link' => 'Lengkapi Biodata'],
                      1 => ['t' => 'Daftar', 'rows' => [['No. Pendaftaran', $regNo], ['Sekolah', $schName]], 'note' => $activeRegistration ? ['ok','Pendaftaran ' . ($trkName ?: '-') . ' aktif'] : ['warn','Belum ada pendaftaran'], 'href' => $activeRegistration ? route('registration.show', $activeRegistration) : null, 'link' => 'Lihat Detail'],
                      2 => ['t' => 'Dokumen', 'rows' => [['Status', $docTot ? $docCnt.'/'.$docTot.' terverifikasi' : 'Belum ada']], 'note' => $docTot && $docCnt >= $docTot ? ['ok','Semua berkas terverifikasi'] : ($docTot ? ['warn','Ada berkas belum diverifikasi panitia'] : ['warn','Upload berkas untuk lanjut']), 'href' => $activeRegistration ? route('registration.show', $activeRegistration) : null, 'link' => 'Kelola Dokumen'],
                      3 => ['t' => 'Verifikasi', 'rows' => [['Status', \App\Support\StatusBadge::registrationStatusLabel($activeRegistration?->status ?? null)]], 'note' => in_array($stepState[3], ['done','rejected']) ? ($stepState[3] === 'rejected' ? ['bad','Berkas ditolak — perbaiki segera'] : ['ok','Data diverifikasi panitia']) : ['warn','Menunggu pengecekan panitia'], 'href' => $activeRegistration && $stepState[3] === 'rejected' ? route('registration.show', $activeRegistration) : null, 'link' => 'Perbaiki'],
                      4 => ['t' => 'Bayar', 'rows' => [['Status', $payLabel]], 'note' => $payStatus === 'paid' ? ['ok','Pembayaran lunas'] : ($payStatus === 'failed' ? ['bad','Pembayaran gagal — coba lagi'] : ['warn','Belum ada pembayaran']), 'href' => $firstPaid ? route('payments.show', $firstPaid) : ($activeRegistration ? route('registration.show', $activeRegistration) : null), 'link' => 'Lihat Pembayaran'],
                      5 => ['t' => 'Diterima', 'rows' => [['Status', \App\Support\StatusBadge::registrationStatusLabel($activeRegistration?->status ?? null)]], 'note' => $stepState[5] === 'done' ? ['ok','Selamat! Proses selesai'] : ['warn','Menunggu pengumuman'], 'href' => $activeRegistration ? route('registration.show', $activeRegistration) : null, 'link' => 'Lihat Detail'],
                    ];
                    $doneCount = count(array_filter($stepState, fn($v) => $v === 'done'));
                    $currentIdx = array_search('current', $stepState, true);
                    $skipNames = []; foreach ($stepDefs as $i => $st) { if ($stepState[$i] !== 'done') $skipNames[] = $st['label']; }
                    $remainingTxt = $skipNames ? implode(', ', $skipNames) : 'Semua langkah selesai';
                    if ($currentIdx !== false && $stepState[$currentIdx] !== 'done') { $remainingTxt = $stepDefs[$currentIdx]['label'] . ($skipNames ? ' · ' . implode(', ', array_slice($skipNames, 1)) : ''); }
                  @endphp
                  <div class="reg-timeline">
                    <div class="reg-tl-head">
                      <h3 class="reg-tl-ttl">Alur Pendaftaran Anda</h3>
                      <span class="reg-tl-meta">{{ $doneCount }} dari 6 langkah selesai · Sisa: {{ $remainingTxt }}</span>
                      @if ($flowState === 'rejected')
                        <span class="reg-tl-badge"><x-hi icon="fa-triangle-exclamation" style="font-size:11px" /> Berkas ditolak — perbaiki dan upload ulang</span>
                      @endif
                    </div>
                    <div class="reg-steps">
                      @foreach ($stepDefs as $i => $step)
                        @php
                          $s = $stepState[$i] ?? 'todo';
                          $d = $stepDetail[$i] ?? null;
                        @endphp
                        <div class="reg-step {{ $s }}" data-step="{{ $i }}" tabindex="0" role="button" aria-label="{{ $step['label'] }}">
                          <div class="reg-step-ic">
                            @if ($s === 'done') <i class="fa-solid fa-check"></i>
                            @elseif ($s === 'rejected') <i class="fa-solid fa-xmark"></i>
                            @else {{ $i + 1 }}
                            @endif
                          </div>
                          <p class="reg-step-lb">{{ $step['label'] }}</p>
                          <p class="reg-step-desc">{{ $step['desc'] }}</p>
                          @if ($d && (($s !== 'todo') || collect($stepState)->filter(fn($v) => $v === 'current')->count() === 1 || $i === $currentIdx))
                            <div class="reg-pop">
                              <p class="reg-pop-t"><x-hi icon="fa-circle-info" /> {{ $d['t'] }}</p>
                              @foreach ($d['rows'] as $r)
                                <div class="reg-pop-row"><b>{{ $r[0] }}:</b><span>{{ $r[1] }}</span></div>
                              @endforeach
                              @if ($d['note'])
                                <div class="reg-pop-note {{ $d['note'][0] }}"><span class="reg-pop-note-ic">●</span> {{ $d['note'][1] }}</div>
                              @endif
                              @if ($d['href'])
                                <a href="{{ $d['href'] }}" class="reg-pop-a">{{ $d['link'] }} <x-hi icon="fa-arrow-right" style="font-size:9px" /></a>
                              @endif
                            </div>
                          @endif
                        </div>
                      @endforeach
                    </div>
                  </div>
                @endif
      @endif

      {{-- Daftar pendaftaran / empty state --}}
      @if ($registrations->isEmpty())
        @php
          // Prasyarat biodata
          $applicantComplete = isset($applicant) && $applicant ? $applicant->isProfileComplete() : false;
          // Periode pendaftaran aktif (jadwal)
          $activePeriods = \App\Models\RegistrationPeriod::where('is_active', true)->with('schoolLevel')->get();
          $today = \Illuminate\Support\Carbon::today();
          // Kontak bantuan (static default + fallback setting)
          $helpPhone = \App\Models\Setting::get('contact_phone') ?: '+62 812-3456-7890';
          $helpEmail = \App\Models\Setting::get('contact_email') ?: 'info@sekolahin.id';
          $faq = [
            ['q' => 'Bagaimana cara memulai pendaftaran?', 'a' => 'Lengkapi biodata kamu, lalu klik tombol "Buat Pendaftaran", pilih jenjang, sekolah, dan jalur yang tersedia.'],
            ['q' => 'Dokumen apa saja yang harus disiapkan?', 'a' => 'Umumnya Pas Foto, Kartu Keluarga, Akta Lahir, dan Rapor. Jenis dokumen tambahan tergantung jenjang dan jalur yang dipilih.'],
            ['q' => 'Apakah ada biaya pendaftaran?', 'a' => 'Biaya mengikuti ketentuan jalur dan jenjang. Rincian biaya akan muncul saat Anda masuk ke halaman detail pendaftaran.'],
            ['q' => 'Siapa yang saya hubungi jika terkendala?', 'a' => 'Anda dapat menghubungi panitia melalui kontak di bawah, atau gunakan tombol bantuan di pojok layar.'],
          ];
        @endphp

        {{-- (1) Banner prasyarat biodata --}}
        @if ($applicantComplete)
          <div class="reg-prereq ready">
            <div class="reg-prereq-ic"><x-hi icon="fa-circle-check" /></div>
            <div class="reg-prereq-body">
              <p class="reg-prereq-t">Biodata Siap</p>
              <p class="reg-prereq-p">Data diri kamu sudah lengkap. Kamu siap membuat pendaftaran baru.</p>
            </div>
            <a href="{{ route('applicant.profile') }}" class="reg-prereq-btn"><x-hi icon="fa-pen" /> Perbarui Biodata</a>
          </div>
        @else
          <div class="reg-prereq warn">
            <div class="reg-prereq-ic"><x-hi icon="fa-triangle-exclamation" /></div>
            <div class="reg-prereq-body">
              <p class="reg-prereq-t">Lengkapi Biodata kamu terlebih dahulu</p>
              <p class="reg-prereq-p">Sebelum memulai pendaftaran, lengkapi data diri kamu agar prosesnya lebih cepat.</p>
            </div>
            <a href="{{ route('applicant.profile') }}" class="reg-prereq-btn"><x-hi icon="fa-arrow-right" /> Ke Biodata</a>
          </div>
        @endif

        {{-- (2) Hero CTA card --}}
        <div class="reg-hero">
          <div class="reg-hero-ic">
            <x-hi icon="fa-file-lines" />
          </div>
          <h3>Siap memulai perjalananmu?</h3>
          <p>Daftarkan dirimu ke sekolah impian. Pilih jenjang, sekolah, dan jalur pendaftaran yang tersedia untuk memulai.</p>
          @if ($applicantComplete)
            <a href="{{ route('registration.create') }}" class="reg-btn coral">
              <x-hi icon="fa-plus" /> Buat Pendaftaran
            </a>
          @else
            <a href="{{ route('applicant.profile') }}" class="reg-btn coral">
              <x-hi icon="fa-pen" /> Lengkapi Biodata Dulu
            </a>
          @endif
        </div>

        {{-- (3) Alur Pendaftaran --}}
        <div class="reg-flow">
          <div class="reg-flow-hd"><x-hi icon="fa-route" style="color:var(--coral);font-size:13px" /><h3 class="reg-flow-ttl">Alur Pendaftaran</h3></div>
          <div class="reg-flow-grid">
            @php
              $flowSteps = [
                ['n' => 1, 't' => 'Isi Biodata & Berkas', 'd' => 'Lengkapi data diri dan siapkan berkas yang diperlukan.'],
                ['n' => 2, 't' => 'Pilih Sekolah & Jurusan', 'd' => 'Pilih jenjang, sekolah, dan jurusan yang kamu inginkan.'],
                ['n' => 3, 't' => 'Verifikasi oleh Admin', 'd' => 'Panitia memverifikasi dokumen dan pembayaran kamu.'],
                ['n' => 4, 't' => 'Pengumuman Hasil', 'd' => 'Pantau status dan terima hasil seleksi pendaftaranmu.'],
              ];
            @endphp
            @foreach ($flowSteps as $fs)
              <div class="reg-flow-step">
                <div class="reg-flow-num">{{ $fs['n'] }}</div>
                <p class="reg-flow-t">{{ $fs['t'] }}</p>
                <p class="reg-flow-d">{{ $fs['d'] }}</p>
              </div>
            @endforeach
          </div>
        </div>

        {{-- (4) Widgets grid: Jadwal + Bantuan --}}
        <div class="reg-widgets">
          {{-- Jadwal Pendaftaran --}}
          <div class="reg-widget">
            <div class="reg-widget-hd">
              <div class="reg-widget-ic cal"><x-hi icon="fa-calendar-days" /></div>
              <div>
                <p class="reg-widget-ttl">Jadwal Pendaftaran</p>
                <p class="reg-widget-sub">Periode yang sedang berjalan</p>
              </div>
            </div>
            @if ($activePeriods->isEmpty())
              <p class="reg-jadwal-empty">Belum ada jadwal pendaftaran saat ini. Hubungi panitia untuk informasi lebih lanjut.</p>
            @else
              @foreach ($activePeriods as $period)
                @php
                  $lv = $period->schoolLevel->name ?? 'Semua';
                  $open = true;
                  $statusLabel = 'Dibuka';
                  $statusCls = 'open';
                  if ($period->start_date && $today->lt(\Illuminate\Support\Carbon::parse($period->start_date))) {
                    $open = false; $statusLabel = 'Akan Dibuka'; $statusCls = 'closed';
                  } elseif ($period->end_date && $today->gt(\Illuminate\Support\Carbon::parse($period->end_date))) {
                    $open = false; $statusLabel = 'Ditutup'; $statusCls = 'closed';
                  }
                @endphp
                <div class="reg-jadwal-row">
                  <div>
                    <p class="reg-jadwal-lv">{{ $lv }}</p>
                    <p class="reg-jadwal-name">{{ $period->name }}</p>
                    <p class="reg-jadwal-date">
                      <x-hi icon="fa-calendar-day" />
                      @if ($period->start_date)
                        {{ \Illuminate\Support\Carbon::parse($period->start_date)->translatedFormat('d M') }}
                        @if ($period->end_date)
                          – {{ \Illuminate\Support\Carbon::parse($period->end_date)->translatedFormat('d M Y') }}
                        @endif
                      @else
                        Segera
                      @endif
                    </p>
                  </div>
                  <span class="reg-jadwal-badge {{ $statusCls }}">{{ $statusLabel }}</span>
                </div>
              @endforeach
            @endif
          </div>

          {{-- Bantuan / Pusat Informasi --}}
          <div class="reg-widget">
            <div class="reg-widget-hd">
              <div class="reg-widget-ic help"><x-hi icon="fa-circle-question" /></div>
              <div>
                <p class="reg-widget-ttl">Bantuan &amp; Pusat Informasi</p>
                <p class="reg-widget-sub">Butuh bantuan? Kami siap membantu.</p>
              </div>
            </div>
            <div class="reg-faq">
              @foreach ($faq as $i => $f)
                <div class="reg-faq-item" data-faq>
                  <button type="button" class="reg-faq-q" onclick="toggleFaq(this)">
                    <x-hi icon="fa-chevron-down" />
                    <span>{{ $f['q'] }}</span>
                  </button>
                  <p class="reg-faq-a">{{ $f['a'] }}</p>
                </div>
              @endforeach
            </div>
            <div class="reg-widget-line"></div>
            <p style="font-size:12.5px;color:var(--muted);display:flex;align-items:center;gap:8px;flex-wrap:wrap">
              <span><x-hi icon="fa-phone" style="color:var(--coral);margin-right:5px" />{{ $helpPhone }}</span>
              <span><x-hi icon="fa-envelope" style="color:var(--coral);margin-right:5px" />{{ $helpEmail }}</span>
            </p>
            <a href="{{ route('registration.create') }}" class="reg-help-cta" onclick="openHelpWidget()">
              <x-hi icon="fa-comments" /> Panduan Langkah demi Langkah
            </a>
          </div>
        </div>

        @push('scripts')
        <script>
          function toggleFaq(btn) {
            const item = btn.closest('[data-faq]');
            if (item) item.classList.toggle('open');
          }
          // Timeline step: toggle popover (klik buka, klik lagi / klik di luar / Escape tutup)
          document.addEventListener('DOMContentLoaded', function () {
            const steps = document.querySelectorAll('.reg-step[data-step]');
            function closeAll(except) {
              steps.forEach(function (st) { if (st !== except) st.classList.remove('open'); });
            }
            steps.forEach(function (st) {
              st.addEventListener('click', function (e) {
                if (e.target.closest('a')) return; // biarkan link bekerja normal
                const wasOpen = st.classList.contains('open');
                closeAll(st);
                if (!wasOpen) st.classList.add('open');
                e.stopPropagation();
              });
              st.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); st.click(); }
              });
            });
            document.addEventListener('click', function () { closeAll(null); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeAll(null); });
          });
          // Jika x-help-widget diekspos, buka modal panduannya; jika tidak, fallback scroll ke alur.
          function openHelpWidget() {
            var w = document.querySelector('[x-data]');
            if (w && window.__toggleHelp) { window.__toggleHelp(); return; }
            var el = document.querySelector('.reg-flow'); if (el) el.scrollIntoView({ behavior: 'smooth' });
          }
        </script>
        @endpush
      @else
        <div class="reg-list">
          <div class="reg-list-hd">
            <h3 class="reg-list-ttl">Riwayat Pendaftaran</h3>
          </div>
          @foreach ($registrations as $reg)
            @php
              $reqTypes = $reg->requiredDocumentTypes();
              $upTypes = $reg->documents->pluck('document_type')->unique();
              $verTypes = $reg->documents->whereNotNull('verified_at')->pluck('document_type')->unique();
              $upCount = $upTypes->intersect($reqTypes)->count();
              $verCount = $verTypes->intersect($reqTypes)->count();
              $totalReq = count($reqTypes);
              $docPct = $totalReq > 0 ? round(($verCount / $totalReq) * 100) : 0;
              $docAllDone = $totalReq > 0 && $verCount >= $totalReq;
              $hasDeadline = $reg->deadline_at && $reg->status === 'pending';
              $dlExpired = $hasDeadline ? $reg->isDeadlineExpired() : false;
              $dlHours = $hasDeadline ? $reg->getDeadlineHoursRemaining() : null;
              $majorName = $reg->major?->name ?? $reg->finalMajor?->name ?? null;

              $rs = $reg->status;
              $pillCls = match ($rs) {
                'pending' => 'amber', 'verified' => 'blue', 'accepted' => 'green',
                're_registration_complete' => 'indigo', 'rejected' => 'red',
                'canceled' => 'gray', 'withdrawn' => 'gray', 'completed' => 'green',
                default => 'gray',
              };
              $pillTxt = \App\Support\StatusBadge::registrationStatusLabel($rs);

              if ($hasDeadline) {
                if ($dlExpired) $deadlineCls = 'over';
                elseif ($dlHours !== null && $dlHours <= 24) $deadlineCls = 'soon';
                else $deadlineCls = 'normal';
                $deadlineTxt = $reg->getDeadlineLabel();
              } else {
                $deadlineCls = 'normal'; $deadlineTxt = '—';
              }
            @endphp
            <div class="reg-row">
              <div class="reg-row-ic"><x-hi icon="fa-file-lines" /></div>
              <div class="reg-row-body">
                <p class="reg-row-no">No. Pendaftaran</p>
                <p class="reg-row-title">{{ $reg->registration_number }}</p>
                <div class="reg-row-meta">
                  <span><b>{{ $reg->registrationPeriod->schoolLevel->name }}</b></span>
                  <span>{{ $reg->school?->name ?? '-' }}</span>
                  @if ($majorName)<span>{{ $majorName }}</span>@endif
                  <span>{{ $reg->registrationPeriod->name }}</span>
                  <span>{{ $reg->registrationTrack->name }}</span>
                </div>
              </div>
              <div class="reg-row-right">
                <span class="reg-pill {{ $pillCls }}">{{ $pillTxt }}</span>
                <div class="reg-doc">
                  <b>{{ $verCount }}/{{ $totalReq }}</b> Dokumen
                  <div class="reg-doc-bar"><i class="{{ $docAllDone ? 'ok' : '' }}" style="width:{{ $docPct }}%"></i></div>
                </div>
                <span class="reg-deadline {{ $deadlineCls }}">
                  <x-hi icon="fa-hourglass-half" /> {{ $deadlineTxt }}
                </span>
                <a href="{{ route('registration.show', $reg) }}" class="reg-link">Lihat Detail <x-hi icon="fa-arrow-right" /></a>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
</x-student-layout>
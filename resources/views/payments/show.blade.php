<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('registration.index') }}" class="inline-flex items-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100" aria-label="Kembali ke Pendaftaran">
                <i class="fa-solid fa-arrow-left text-lg"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Pembayaran
            </h2>
            <div class="ms-auto">
                <x-notification-panel />
            </div>
        </div>
    </x-slot>

    @php
        $registration = $payment->registration;
        $applicant = $registration->applicant;
        $school = $registration->school;

        $isOnline = $payment->payment_method === 'online';
        $isVerified = $payment->status === 'verified';
        $isRejected = $payment->status === 'rejected';
        $isPending = $payment->status === 'pending';

        // --- Diverifikasi oleh (jangan pernah '-') ---
        // 1) Admin manual → nama admin
        // 2) Online otomatis (Xendit) → 'Otomatis (Xendit)'
        // 3) fallback → 'Sistem'
        $verifierName = $payment->verifier->name ?? null;
        if ($verifierName) {
            $verifiedByLabel = $verifierName;
        } elseif ($isOnline) {
            $verifiedByLabel = 'Otomatis (Xendit)';
        } else {
            $verifiedByLabel = 'Sistem';
        }

        // --- Channel / metode ramah user (tanpa kode teknis) ---
        if ($isOnline) {
            $channelLabel = $payment->xendit_payment_method
                ? \App\Services\XenditService::friendlyXenditMethod($payment->xendit_payment_method)
                : 'Online (Xendit)';
            $methodLabel = 'Online (Xendit)';
        } else {
            $channelLabel = 'Transfer Bank (Manual)';
            $methodLabel = 'Transfer Bank (Manual)';
        }

        // --- Tanggal Pembayaran (verified_at → created_at fallback) ---
        $paidDate = $payment->verified_at ?? $payment->created_at;

        // --- Catatan bersih: buang duplikasi "via Xendit via Xendit" (display fix) ---
        $cleanNotes = $payment->notes;
        if ($cleanNotes && substr_count($cleanNotes, 'via Xendit') > 1) {
            $cleanNotes = preg_replace('/(?:via Xendit\s*)+$/i', 'via Xendit', $cleanNotes);
            // Kemungkinan pola: "... Direct Debit via Xendit via Xendit" → ganti jadi "... via Xendit"
            $cleanNotes = preg_replace('/\bvia Xendit via Xendit\b/i', 'via Xendit', $cleanNotes);
            // Bersihkan juga pola ganda lain
            while (str_contains(strtolower($cleanNotes), 'via xendit via xendit')) {
                $cleanNotes = preg_replace('/via Xendit via Xendit/i', 'via Xendit', $cleanNotes);
            }
            $cleanNotes = trim($cleanNotes);
        }
    @endphp

    <style>
        .pay-show {
            --coral: #FF6B6B; --coral-2: #FF8E6E; --coral-soft: #FFE5E3;
            --ink: #1a1a2e; --muted: #8a8f9d; --divider: rgba(26,26,46,.10);
            --green: #10B981; --green-soft: #D1FAE5;
            --red: #EF4444; --red-soft: #FEE2E2;
            --amber: #F59E0B; --amber-soft: #FEF3C7;
            --blue: #3B82F6; --blue-soft: #DBEAFE;
            --gray: #6b7280; --gray-soft: #F3F4F6;
        }
        .pay-show .ps-inner { max-width: 820px; margin: 0 auto; }

        /* card utama */
        .pay-show .ps-card {
            position: relative;
            border-radius: 24px;
            background: #f6f7fb;
            padding: 28px 28px 40px;
        }

        /* header */
        .pay-show .ps-head { display: flex; align-items: flex-start; gap: 14px; flex-wrap: wrap; }
        .pay-show .ps-head-ic {
            width: 52px; height: 52px; border-radius: 15px;
            background: linear-gradient(135deg, var(--coral), var(--coral-2));
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-size: 21px; box-shadow: 0 10px 22px -10px rgba(255,107,107,.6); flex: 0 0 auto;
        }
        .pay-show .ps-title { font-size: 22px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; }
        .pay-show .ps-sub { font-size: 13px; color: var(--muted); margin-top: 4px; }
        .pay-show .ps-badge { margin-left: auto; }

        /* pill status (Bringova) */
        .pay-show .ps-pill {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 13px; border-radius: 99px; font-size: 11.5px; font-weight: 800;
            white-space: nowrap;
        }
        .pay-show .ps-pill.green { background: var(--green-soft); color: #047857; }
        .pay-show .ps-pill.red { background: var(--red-soft); color: #B91C1C; }
        .pay-show .ps-pill.amber { background: var(--amber-soft); color: #B45309; }
        .pay-show .ps-pill.gray { background: var(--gray-soft); color: var(--gray); }

        /* section */
        .pay-show .ps-sec { border-top: 1px solid var(--divider); padding: 24px 0 6px; }
        .pay-show .ps-sec:first-of-type { border-top: none; padding-top: 22px; }
        .pay-show .ps-sec-head { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; flex-wrap: wrap; }
        .pay-show .ps-sec-ic {
            width: 40px; height: 40px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex: 0 0 auto;
        }
        .pay-show .ps-sec-ic.coral { background: var(--coral-soft); color: var(--coral); }
        .pay-show .ps-sec-ic.blue { background: var(--blue-soft); color: var(--blue); }
        .pay-show .ps-sec-ic.green { background: var(--green-soft); color: var(--green); }
        .pay-show .ps-sec-ic.amber { background: var(--amber-soft); color: var(--amber); }
        .pay-show .ps-sec-ic.red { background: var(--red-soft); color: var(--red); }
        .pay-show .ps-sec-ttl { font-size: 14px; font-weight: 800; color: var(--ink); }
        .pay-show .ps-sec-desc { font-size: 12px; color: var(--muted); margin-top: 1px; }

        /* grid info */
        .pay-show .ps-grid { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 0; }
        .pay-show .ps-cell { padding: 12px 4px; border-bottom: 1px solid var(--divider); }
        .pay-show .ps-cell:nth-last-child(-n+2) { border-bottom: none; }
        .pay-show .ps-cell .ps-label {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .06em; color: var(--muted);
        }
        .pay-show .ps-cell .ps-value { margin-top: 3px; font-size: 14px; font-weight: 600; color: var(--ink); line-height: 1.4; }
        .pay-show .ps-cell .ps-value .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; letter-spacing: .02em; }
        .pay-show .ps-cell-full { grid-column: 1 / -1; }

        /* amount highlight */
        .pay-show .ps-amount {
            margin-top: 16px; border: 1px solid var(--divider);
            border-left: 4px solid var(--coral); border-radius: 18px;
            padding: 20px 22px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
            background: linear-gradient(180deg, rgba(255,107,107,.06), rgba(255,107,107,.02));
        }
        .pay-show .ps-amount-ic {
            width: 48px; height: 48px; border-radius: 14px; background: var(--coral-soft);
            color: var(--coral); display: flex; align-items: center; justify-content: center;
            font-size: 19px; flex: 0 0 auto;
        }
        .pay-show .ps-amount-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); }
        .pay-show .ps-amount-value { font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; line-height: 1.1; margin-top: 2px; }

        /* alert / info box */
        .pay-show .ps-alert {
            display: flex; gap: 12px; align-items: flex-start;
            border-radius: 14px; padding: 14px 16px; margin-top: 16px;
            border: 1px solid transparent;
        }
        .pay-show .ps-alert i.ps-alert-ic {
            width: 22px; height: 22px; border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; flex: 0 0 auto; margin-top: 1px;
        }
        .pay-show .ps-alert.green { background: var(--green-soft); border-color: rgba(16,185,129,.3); }
        .pay-show .ps-alert.green i.ps-alert-ic { background: var(--green); color: #fff; }
        .pay-show .ps-alert.green .ps-alert-t, .pay-show .ps-alert.green .ps-alert-p { color: #047857; }
        .pay-show .ps-alert.red { background: var(--red-soft); border-color: rgba(239,68,68,.25); }
        .pay-show .ps-alert.red i.ps-alert-ic { background: var(--red); color: #fff; }
        .pay-show .ps-alert.red .ps-alert-t, .pay-show .ps-alert.red .ps-alert-p { color: #B91C1C; }
        .pay-show .ps-alert.amber { background: var(--amber-soft); border-color: rgba(245,158,11,.3); }
        .pay-show .ps-alert.amber i.ps-alert-ic { background: var(--amber); color: #fff; }
        .pay-show .ps-alert.amber .ps-alert-t, .pay-show .ps-alert.amber .ps-alert-p { color: #B45309; }
        .pay-show .ps-alert .ps-alert-t { font-weight: 800; font-size: 13.5px; }
        .pay-show .ps-alert .ps-alert-p { font-size: 13px; margin-top: 2px; opacity: .94; line-height: 1.5; }

        /* bukti card */
        .pay-show .ps-proof {
            border: 1px solid var(--divider); border-radius: 16px; padding: 16px;
            display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
        }
        .pay-show .ps-proof-ic {
            width: 46px; height: 46px; border-radius: 13px;
            background: var(--gray-soft); color: var(--gray);
            display: flex; align-items: center; justify-content: center; font-size: 18px; flex: 0 0 auto;
        }
        .pay-show .ps-proof-info { flex: 1 1 200px; min-width: 0; }
        .pay-show .ps-proof-t { font-size: 13.5px; font-weight: 800; color: var(--ink); }
        .pay-show .ps-proof-p { font-size: 12px; color: var(--muted); margin-top: 2px; word-break: break-all; }
        .pay-show .ps-proof-action { flex: 0 0 auto; }

        /* buttons */
        .pay-show .ps-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 11px 18px; border-radius: 12px; font-size: 13.5px; font-weight: 700;
            text-decoration: none; cursor: pointer; border: none;
            transition: transform .15s, box-shadow .15s, background .15s; min-height: 44px;
        }
        .pay-show .ps-btn.coral { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color: #fff; box-shadow: 0 8px 18px -8px rgba(255,107,107,.6); }
        .pay-show .ps-btn.coral:hover { transform: translateY(-1px); }
        .pay-show .ps-btn.blue { background: var(--blue); color: #fff; box-shadow: 0 8px 18px -8px rgba(37,99,235,.5); }
        .pay-show .ps-btn.blue:hover { transform: translateY(-1px); }
        .pay-show .ps-btn.ghost { background: rgba(255,255,255,.6); color: var(--ink); border: 1px solid rgba(26,26,46,.12); }
        .pay-show .ps-btn.ghost:hover { background: #fff; color: var(--coral); border-color: var(--coral); }

        /* footer actions */
        .pay-show .ps-actions {
            margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--divider);
            display: flex; justify-content: flex-end; gap: 12px; flex-wrap: wrap;
        }

        /* reason box */
        .pay-show .ps-reason {
            margin-top: 12px; background: rgba(255,255,255,.6);
            border: 1px solid rgba(239,68,68,.2); border-radius: 12px; padding: 12px 14px;
        }
        .pay-show .ps-reason .ps-r-label { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); }
        .pay-show .ps-reason .ps-r-text { font-size: 13px; color: var(--ink); margin-top: 3px; line-height: 1.5; }

        @media (max-width: 720px) {
            .pay-show .ps-card { padding: 20px 16px 32px; border-radius: 18px; }
            .pay-show .ps-grid { grid-template-columns: 1fr; }
            .pay-show .ps-cell:nth-last-child(-n+2) { border-bottom: 1px solid var(--divider); }
            .pay-show .ps-cell:last-child { border-bottom: none; }
            .pay-show .ps-actions { justify-content: stretch; }
            .pay-show .ps-actions .ps-btn { width: 100%; }
            .pay-show .ps-badge { margin-left: 0; }
        }
    </style>

    <div class="py-10 pay-show">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="ps-card">
                {{-- Header --}}
                <div class="ps-head">
                    <span class="ps-head-ic"><i class="fa-solid fa-wallet"></i></span>
                    <div>
                        <h3 class="ps-title">Pembayaran #{{ $payment->id }}</h3>
                        <p class="ps-sub">No. Registrasi: <span class="mono" style="font-family:ui-monospace,monospace">{{ $registration->registration_number }}</span></p>
                    </div>
                    <span class="ps-badge">
                        <span class="ps-pill {{ $isVerified ? 'green' : ($isRejected ? 'red' : 'amber') }}">
                            <i class="fa-solid {{ $isVerified ? 'fa-circle-check' : ($isRejected ? 'fa-circle-xmark' : 'fa-clock') }}"></i>
                            {{ $isVerified ? 'LUNAS' : ($isRejected ? 'DITOLAK' : 'MENUNGGU') }}
                        </span>
                    </span>
                </div>

                {{-- Status alert --}}
                @if ($isVerified)
                    <div class="ps-alert green">
                        <i class="fa-solid fa-circle-check ps-alert-ic"></i>
                        <div>
                            <p class="ps-alert-t">Pembayaran Terverifikasi</p>
                            <p class="ps-alert-p">Diverifikasi oleh: <b>{{ $verifiedByLabel }}</b> &middot; {{ $paidDate ? $paidDate->format('d M Y H:i') : '-' }}</p>
                        </div>
                    </div>
                @elseif ($isRejected)
                    <div class="ps-alert red">
                        <i class="fa-solid fa-circle-xmark ps-alert-ic"></i>
                        <div>
                            <p class="ps-alert-t">Pembayaran Ditolak</p>
                            <p class="ps-alert-p">Ditolak oleh: <b>{{ $verifiedByLabel }}</b> &middot; {{ $paidDate ? $paidDate->format('d M Y H:i') : '-' }}</p>
                            @if ($payment->rejection_reason)
                                <div class="ps-reason">
                                    <p class="ps-r-label">Alasan</p>
                                    <p class="ps-r-text">{{ $payment->rejection_reason }}</p>
                                </div>
                            @endif
                            <p class="ps-alert-p" style="margin-top:8px">Silakan upload ulang bukti pembayaran yang sesuai.</p>
                        </div>
                    </div>
                @else
                    <div class="ps-alert amber">
                        <i class="fa-solid fa-clock ps-alert-ic"></i>
                        <div>
                            <p class="ps-alert-t">Menunggu Pembayaran</p>
                            <p class="ps-alert-p">Pembayaran Anda sedang diproses. Mohon tunggu konfirmasi.</p>
                        </div>
                    </div>
                @endif

                {{-- Informasi Pembayaran --}}
                <section class="ps-sec">
                    <div class="ps-sec-head">
                        <div class="ps-sec-ic blue"><i class="fa-solid fa-receipt"></i></div>
                        <div>
                            <p class="ps-sec-ttl">Informasi Pembayaran</p>
                            <p class="ps-sec-desc">Nomor, metode, dan detail tagihan.</p>
                        </div>
                    </div>

                    <div class="ps-grid">
                        @if($payment->invoice_number)
                            <div class="ps-cell">
                                <p class="ps-label">No. Invoice</p>
                                <p class="ps-value mono">{{ $payment->invoice_number }}</p>
                            </div>
                        @endif
                        <div class="ps-cell">
                            <p class="ps-label">Tipe Pembayaran</p>
                            <p class="ps-value">{{ $payment->payment_type === 'registration_fee' ? 'Biaya Pendaftaran' : 'Biaya Daftar Ulang' }}</p>
                        </div>
                        <div class="ps-cell">
                            <p class="ps-label">Metode</p>
                            <p class="ps-value">{{ $methodLabel }}</p>
                            @if ($isOnline && $channelLabel && $channelLabel !== $methodLabel)
                                <p class="ps-label" style="margin-top:8px">Channel</p>
                                <p class="ps-value" style="font-weight:500">{{ $channelLabel }}</p>
                            @endif
                        </div>
                        <div class="ps-cell">
                            <p class="ps-label">Tanggal Pembayaran</p>
                            <p class="ps-value">{{ $paidDate ? $paidDate->format('d M Y H:i') : '-' }}</p>
                        </div>

                        {{-- Siswa & Sekolah --}}
                        <div class="ps-cell">
                            <p class="ps-label">Nama Siswa</p>
                            <p class="ps-value">{{ $applicant->full_name ?? '-' }}</p>
                        </div>
                        <div class="ps-cell">
                            <p class="ps-label">Sekolah</p>
                            <p class="ps-value">{{ $school->name ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Amount highlight --}}
                    <div class="ps-amount">
                        <span class="ps-amount-ic"><i class="fa-solid fa-coins"></i></span>
                        <div>
                            <p class="ps-amount-label">Jumlah Dibayar</p>
                            <p class="ps-amount-value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    {{-- Link Unduh PDF --}}
                    @if($payment->invoice_pdf)
                        <div style="margin-top:16px">
                            <a href="{{ route('payments.invoice', $payment) }}" target="_blank" class="ps-btn coral">
                                <i class="fa-solid fa-file-pdf"></i> Unduh Invoice (PDF)
                            </a>
                        </div>
                    @endif
                </section>

                {{-- Bukti Pembayaran --}}
                @if ($payment->proof_file)
                    <section class="ps-sec">
                        <div class="ps-sec-head">
                            <div class="ps-sec-ic coral"><i class="fa-solid fa-paperclip"></i></div>
                            <div>
                                <p class="ps-sec-ttl">Bukti Pembayaran</p>
                                <p class="ps-sec-desc">Dokumen bukti transfer yang diunggah.</p>
                            </div>
                        </div>
                        <div class="ps-proof">
                            <span class="ps-proof-ic"><i class="fa-solid fa-file-lines"></i></span>
                            <div class="ps-proof-info">
                                <p class="ps-proof-t">Bukti Transfer</p>
                                <p class="ps-proof-p">{{ basename($payment->proof_file) }}</p>
                            </div>
                            <div class="ps-proof-action">
                                <button type="button" onclick="showFileModal('{{ route('payments.proof', $payment) }}', 'Bukti Pembayaran')" class="ps-btn blue">
                                    <i class="fa-solid fa-eye"></i> Lihat Bukti
                                </button>
                            </div>
                        </div>
                    </section>
                @endif

                {{-- Catatan (bersih) --}}
                @if ($cleanNotes)
                    <section class="ps-sec">
                        <div class="ps-sec-head">
                            <div class="ps-sec-ic amber"><i class="fa-solid fa-note-sticky"></i></div>
                            <div>
                                <p class="ps-sec-ttl">Catatan</p>
                                <p class="ps-sec-desc">Informasi tambahan dari sistem / admin.</p>
                            </div>
                        </div>
                        <p style="font-size:13.5px;color:var(--ink);line-height:1.6">{{ $cleanNotes }}</p>
                    </section>
                @endif

                {{-- Actions --}}
                <div class="ps-actions">
                    <a href="{{ route('registration.show', $registration) }}" class="ps-btn ghost">
                        <i class="fa-solid fa-arrow-left"></i> Kembali ke Pendaftaran
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('components.file-preview-modal')
</x-app-layout>

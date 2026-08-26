<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
            <div class="flex gap-3 items-center">
                <x-notification-panel />
                <x-app-button variant="secondary" :href="route('applicant.profile')" size="md" class="!p-2" aria-label="Biodata Saya" title="Biodata Saya">
                    <i class="fa-solid fa-id-card text-lg"></i>
                </x-app-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @php
                $activeReg = $registrations->firstWhere(function ($r) {
                    return $r->status === 'pending' && in_array($r->payment_status, ['unpaid', 'pending']);
                });
            @endphp
            @php
                $reminderReg = $registrations->firstWhere('status', 'accepted');
            @endphp
            @if($reminderReg)
                <x-re-registration-reminder :registration="$reminderReg" />
            @endif
            @if ($activeReg && $activeReg->deadline_at)
                @php
                    $isExpired = $activeReg->isDeadlineExpired();
                    $hoursRemaining = $activeReg->getDeadlineHoursRemaining();
                @endphp
                @if ($isExpired)
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <p class="font-medium">⚠ Batas waktu pendaftaran Anda telah terlewati!</p>
                        <p class="text-sm mt-1">Pendaftaran {{ $activeReg->registration_number }} akan segera dibatalkan otomatis karena melebihi batas waktu.</p>
                        <a href="{{ route('registration.show', $activeReg) }}" class="mt-2 inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                            Lihat Detail
                        </a>
                    </div>
                @elseif ($hoursRemaining !== null && $hoursRemaining <= 24)
                    <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                        <p class="font-medium">⏰ Sisa waktu pendaftaran: {{ $activeReg->getDeadlineLabel() }}</p>
                        <p class="text-sm mt-1">Segera lengkapi dokumen dan lakukan pembayaran sebelum pendaftaran {{ $activeReg->registration_number }} dibatalkan otomatis.</p>
                        <a href="{{ route('registration.show', $activeReg) }}" class="mt-2 inline-block bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                            Lihat Detail
                        </a>
                    </div>
                @else
                    <div class="mb-4 bg-blue-50 border border-blue-300 text-blue-700 px-4 py-3 rounded">
                        <p class="font-medium">🕒 Batas waktu pendaftaran: {{ $activeReg->deadline_at->format('d M Y H:i') }}</p>
                        <p class="text-sm mt-1">Pendaftaran {{ $activeReg->registration_number }} memiliki sisa waktu {{ $activeReg->getDeadlineLabel() }} untuk melengkapi dokumen dan pembayaran.</p>
                        <a href="{{ route('registration.show', $activeReg) }}" class="mt-2 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Lihat Detail
                        </a>
                    </div>
                @endif
            @endif

            @if ($activeRegistration)
                @php
                    // ===== Statistik progres (bermakna walau hanya 1 pendaftaran) =====
                    $statusCard = \App\Support\StatusBadge::registrationStatusCard($activeRegistration->status);
                    $paymentCard = \App\Support\StatusBadge::paymentStatusCard($activeRegistration->payment_status);

                    $docPct = $docStats['total'] > 0 ? round(($docStats['verified'] / $docStats['total']) * 100) : 0;
                    $docAllVerified = $docStats['total'] > 0 && $docStats['verified'] >= $docStats['total'];

                    $deadlineInfo = ['label' => '-', 'cls' => 'bg-gray-50 text-gray-500', 'icon' => 'fa-hourglass-half'];
                    if ($deadline) {
                        if ($deadline['expired']) {
                            $deadlineInfo = ['label' => 'Terlewati', 'cls' => 'bg-red-50 text-red-600', 'icon' => 'fa-triangle-exclamation'];
                        } elseif ($deadline['hours'] !== null && $deadline['hours'] <= 24) {
                            $deadlineInfo = ['label' => $deadline['label'], 'cls' => 'bg-amber-50 text-amber-600', 'icon' => 'fa-hourglass-end'];
                        } else {
                            $deadlineInfo = ['label' => $deadline['label'], 'cls' => 'bg-blue-50 text-blue-600', 'icon' => 'fa-hourglass-half'];
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

                        if ($regStatus === 'canceled') {
                            $flowState = 'canceled';
                        } elseif ($regStatus === 'withdrawn') {
                            $flowState = 'withdrawn';
                        } elseif ($regStatus === 'rejected') {
                            $flowState = 'rejected';
                        }

                        $stepState[0] = 'done'; // Profil
                        $stepState[1] = 'done'; // Pendaftaran dibuat

                        // Dokumen: selesai bila semua wajib terupload; aktif bila sebagian terupload
                        if ($allDocs) {
                            $stepState[2] = 'done';
                        } elseif ($someDocs) {
                            $stepState[2] = 'current';
                        }

                        // Verifikasi: selesai bila status sudah lewat verifikasi
                        if (in_array($regStatus, ['verified', 'accepted', 're_registration_complete'])) {
                            $stepState[3] = 'done';
                        }

                        // Bayar: selesai bila lunas
                        if ($regPaid) {
                            $stepState[4] = 'done';
                        }

                        // Diterima
                        if (in_array($regStatus, ['accepted', 're_registration_complete'])) {
                            $stepState[5] = 'done';
                        }

                        if ($flowState === 'rejected') {
                            $stepState[3] = 'rejected';
                        } elseif ($flowState === 'canceled' || $flowState === 'withdrawn') {
                            $stepState = array_fill(0, 6, 'todo');
                        } else {
                            // Pastikan HANYA SATU tahap aktif: kalau sudah ada 'current'
                            // (mis. dokumen sebagian), jangan timpa dengan yang lain.
                            $alreadyCurrent = in_array('current', $stepState, true);
                            if (!$alreadyCurrent) {
                                foreach ($stepState as $i => $s) {
                                    if ($s === 'todo') {
                                        $stepState[$i] = 'current';
                                        break;
                                    }
                                }
                            }
                        }
                    }
                @endphp

                {{-- Kartu statistik progres --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    {{-- Dokumen --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Dokumen Terverifikasi</p>
                                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $docStats['verified'] }}<span class="text-lg text-gray-400 font-medium">/{{ $docStats['total'] }}</span></p>
                            </div>
                            <div class="w-11 h-11 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid fa-file-lines"></i>
                            </div>
                        </div>
                        <div class="mt-4 h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all {{ $docAllVerified ? 'bg-emerald-500' : 'bg-indigo-500' }}" style="width: {{ $docPct }}%"></div>
                        </div>
                        <p class="mt-2 text-xs text-gray-400">{{ $docAllVerified ? 'Semua berkas terverifikasi' : ($docStats['uploaded'] > 0 ? $docStats['uploaded'] . ' berkas terupload' : 'Belum ada berkas diupload') }}</p>
                    </div>

                    {{-- Pembayaran --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pembayaran</p>
                                <p class="mt-2 text-2xl font-bold {{ str_contains($paymentCard['cls'], 'emerald') ? 'text-emerald-600' : 'text-gray-900' }}">{{ $paymentCard['label'] }}</p>
                            </div>
                            <div class="w-11 h-11 rounded-lg {{ $paymentCard['cls'] }} flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid {{ $paymentCard['icon'] }}"></i>
                            </div>
                        </div>
                        <p class="mt-4 text-xs text-gray-400">
                            @if ($activeRegistration->payment_status === 'paid')
                                Pembayaran sudah lunas
                            @elseif ($activeRegistration->payment_status === 'pending')
                                Menunggu konfirmasi panitia
                            @elseif ($activeRegistration->payment_status === 'failed')
                                Pembayaran gagal — coba lagi
                            @else
                                Belum ada pembayaran
                            @endif
                        </p>
                    </div>

                    {{-- Batas waktu --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Batas Waktu</p>
                                <p class="mt-2 text-2xl font-bold {{ str_contains($deadlineInfo['cls'], 'red') ? 'text-red-600' : (str_contains($deadlineInfo['cls'], 'amber') ? 'text-amber-600' : 'text-gray-900') }}">{{ $deadlineInfo['label'] }}</p>
                            </div>
                            <div class="w-11 h-11 rounded-lg {{ $deadlineInfo['cls'] }} flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid {{ $deadlineInfo['icon'] }}"></i>
                            </div>
                        </div>
                        <p class="mt-4 text-xs text-gray-400">
                            @if ($deadline)
                                @if ($deadline['expired'])
                                    Pendaftaran akan dibatalkan otomatis
                                @else
                                    Sisa waktu penyelesaian pendaftaran
                                @endif
                            @else
                                Tidak ada batas waktu aktif
                            @endif
                        </p>
                    </div>

                    {{-- Tahap saat ini --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tahap Saat Ini</p>
                                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $statusCard['label'] }}</p>
                            </div>
                            <div class="w-11 h-11 rounded-lg {{ $statusCard['cls'] }} flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid fa-route"></i>
                            </div>
                        </div>
                        <p class="mt-4 text-xs text-gray-400">
                            @if ($activeRegistration->status === 'accepted')
                                Segera lakukan daftar ulang
                            @elseif ($activeRegistration->status === 're_registration_complete')
                                Proses selesai — selamat! 🎉
                            @elseif ($activeRegistration->status === 'rejected')
                                Perbaiki sesuai catatan panitia
                            @elseif ($activeRegistration->status === 'canceled')
                                Pendaftaran dibatalkan
                            @elseif ($activeRegistration->status === 'withdrawn')
                                Pendaftaran dibatalkan (mengundurkan diri)
                            @else
                                Ikuti langkah pada alur di bawah
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Timeline alur pendaftaran --}}
                @if ($flowState !== 'canceled' && $flowState !== 'withdrawn')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Alur Pendaftaran Anda</h3>
                        @if ($flowState === 'rejected')
                            <span class="text-xs font-semibold text-red-600 bg-red-50 px-3 py-1 rounded-full">⚠ Berkas ditolak — perbaiki dan upload ulang</span>
                        @endif
                    </div>
                    <div class="flex flex-wrap md:flex-nowrap items-start">
                        @foreach ($stepDefs as $i => $step)
                            @php
                                $s = $stepState[$i] ?? 'todo';
                                $circleCls = match ($s) {
                                    'done' => 'bg-emerald-500 border-emerald-500 text-white',
                                    'current' => 'bg-white border-indigo-500 text-indigo-600 ring-4 ring-indigo-100',
                                    'rejected' => 'bg-red-500 border-red-500 text-white',
                                    default => 'bg-white border-gray-300 text-gray-400',
                                };
                                $labelCls = match ($s) {
                                    'done' => 'text-emerald-600',
                                    'current' => 'text-indigo-600',
                                    'rejected' => 'text-red-600',
                                    default => 'text-gray-400',
                                };
                            @endphp
                            <div class="relative flex-1 min-w-[96px] flex flex-col items-center text-center px-1 mb-4 md:mb-0">
                                @if ($i > 0)
                                    <div class="hidden md:block absolute top-5 left-[-50%] right-[50%] h-0.5 {{ in_array($s, ['done']) ? 'bg-emerald-400' : 'bg-gray-200' }}"></div>
                                @endif
                                <div class="relative z-10 w-10 h-10 rounded-full border-2 flex items-center justify-center text-sm font-bold {{ $circleCls }}">
                                    @if ($s === 'done')
                                        <i class="fa-solid fa-check text-xs"></i>
                                    @elseif ($s === 'rejected')
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </div>
                                <p class="mt-2 text-xs font-semibold {{ $labelCls }}">{{ $step['label'] }}</p>
                                <p class="text-[10px] text-gray-400 leading-tight">{{ $step['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($registrations->isEmpty())
                        <div class="text-center py-12 px-4">
                            {{-- Ilustrasi: dokumen + badge plus --}}
                            <div class="relative mx-auto h-24 w-24">
                                <div class="absolute inset-0 rounded-2xl bg-eggplore-primary-50 rotate-3"></div>
                                <div class="absolute inset-0 rounded-2xl bg-eggplore-primary-100 -rotate-3"></div>
                                <div class="absolute inset-0 flex items-center justify-center rounded-2xl bg-white border border-eggplore-neutral-200 shadow-sm">
                                    <svg class="h-10 w-10 text-eggplore-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="absolute -right-1 -bottom-1 flex h-8 w-8 items-center justify-center rounded-full bg-eggplore-success text-white shadow-md">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                            </div>

                            <h3 class="mt-6 text-xl font-bold text-eggplore-neutral-900">Siap memulai perjalananmu?</h3>
                            <p class="mx-auto mt-2 max-w-sm text-sm text-eggplore-neutral-500">
                                Daftarkan dirimu ke sekolah impian. Pilih jenjang, sekolah, dan jalur pendaftaran yang tersedia.
                            </p>

                            @if (auth()->user()->applicant)
                                <div class="mt-7">
                                    <a href="{{ route('registration.create') }}"
                                       class="inline-flex items-center gap-2 bg-eggplore-primary text-white px-8 h-12 rounded-btn text-sm font-semibold shadow-sm hover:bg-eggplore-primary-600 hover:shadow-brand active:bg-eggplore-primary-700 active:scale-[0.98] transition-all">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Buat Pendaftaran
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="space-y-4">
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
                                @endphp
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sm:p-6">
                                    {{-- Header kartu: nomor + status --}}
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                                <i class="fa-solid fa-file-lines text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">No. Pendaftaran</p>
                                                <p class="font-mono text-base font-semibold text-gray-900">{{ $reg->registration_number }}</p>
                                            </div>
                                        </div>
                                        <x-status-badge :status="$reg->status" type="registration" />
                                    </div>

                                    {{-- Info utama --}}
                                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                                        <div>
                                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Jenjang</p>
                                            <p class="mt-0.5 text-sm font-medium text-gray-900">{{ $reg->registrationPeriod->schoolLevel->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Sekolah</p>
                                            <p class="mt-0.5 text-sm font-medium text-gray-900">{{ $reg->school?->name ?? '-' }}</p>
                                        </div>
                                        @if ($majorName)
                                        <div>
                                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Jurusan</p>
                                            <p class="mt-0.5 text-sm font-medium text-gray-900">{{ $majorName }}</p>
                                        </div>
                                        @endif
                                        <div>
                                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Periode</p>
                                            <p class="mt-0.5 text-sm font-medium text-gray-900">{{ $reg->registrationPeriod->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Jalur</p>
                                            <p class="mt-0.5 text-sm font-medium text-gray-900">{{ $reg->registrationTrack->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Batas Waktu</p>
                                            @if ($hasDeadline)
                                                @if ($dlExpired)
                                                    <p class="mt-0.5 text-sm font-medium text-red-600">Terlewati</p>
                                                @elseif ($dlHours !== null && $dlHours <= 24)
                                                    <p class="mt-0.5 text-sm font-medium text-yellow-600">{{ $reg->getDeadlineLabel() }}</p>
                                                @else
                                                    <p class="mt-0.5 text-sm font-medium text-gray-900">{{ $reg->getDeadlineLabel() }}</p>
                                                @endif
                                            @else
                                                <p class="mt-0.5 text-sm text-gray-300">—</p>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Footer: dokumen + pembayaran + aksi --}}
                                    <div class="mt-5 pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
                                        <div class="flex flex-wrap items-center gap-x-8 gap-y-4">
                                            <div>
                                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Dokumen</p>
                                                <div class="mt-1.5 flex items-center gap-2">
                                                    <span class="font-mono text-sm font-semibold {{ $docAllDone ? 'text-green-600' : ($upCount > 0 ? 'text-yellow-600' : 'text-gray-500') }}">{{ $verCount }}/{{ $totalReq }}</span>
                                                    <div class="w-20 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                                        <div class="h-full rounded-full {{ $docAllDone ? 'bg-green-500' : 'bg-yellow-500' }}" style="width: {{ $docPct }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Pembayaran</p>
                                                <div class="mt-1.5"><x-status-badge :status="$reg->payment_status" type="payment" /></div>
                                            </div>
                                        </div>
                                        <a href="{{ route('registration.show', $reg) }}"
                                           class="inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 hover:text-indigo-900 transition-colors">
                                            Lihat Detail <i class="fa-solid fa-arrow-right text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

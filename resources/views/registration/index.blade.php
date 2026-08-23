<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pendaftaran Saya
            </h2>
            <div class="flex gap-3 items-center">
                <a href="{{ route('notifications.index') }}" class="relative inline-flex items-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none" aria-label="Notifikasi">
                    <i class="fa-solid fa-bell text-lg"></i>
                    @if (Auth::user()->unreadNotifications->count() > 0)
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                            {{ Auth::user()->unreadNotifications->count() > 9 ? '9+' : Auth::user()->unreadNotifications->count() }}
                        </span>
                    @endif
                    <span class="sr-only">Notifikasi</span>
                </a>
                <x-app-button variant="secondary" :href="route('applicant.profile')">
                    <i class="fa-solid fa-id-card"></i> Biodata Saya
                </x-app-button>
                <x-app-button variant="primary" :href="route('registration.create')">
                    <i class="fa-solid fa-plus"></i> Daftar Baru
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
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pendaftaran</h3>
                            <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat pendaftaran baru.</p>
                            @if (auth()->user()->applicant)
                                <div class="mt-6">
                                    <a href="{{ route('registration.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        Buat Pendaftaran
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Pendaftaran</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenjang</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sekolah</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jurusan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jalur</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batas Waktu</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembayaran</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dokumen</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($registrations as $reg)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $reg->registration_number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $reg->registrationPeriod->schoolLevel->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $reg->school?->name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $reg->major?->name ?? $reg->finalMajor?->name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $reg->registrationPeriod->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $reg->registrationTrack->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <x-status-badge :status="$reg->status" type="registration" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if ($reg->deadline_at && $reg->status === 'pending')
                                                    @php
                                                        $hoursRemaining = $reg->getDeadlineHoursRemaining();
                                                        $isExpired = $reg->isDeadlineExpired();
                                                    @endphp
                                                    @if ($isExpired)
                                                        <span class="text-red-600 font-medium">Terlewati</span>
                                                    @elseif ($hoursRemaining !== null)
                                                        @if ($hoursRemaining <= 24)
                                                            <span class="text-yellow-600 font-medium">{{ $reg->getDeadlineLabel() }}</span>
                                                        @else
                                                            {{ $reg->getDeadlineLabel() }}
                                                        @endif
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <x-status-badge :status="$reg->payment_status" type="payment" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $requiredTypes = $reg->requiredDocumentTypes();
                                                    $uploadedTypes = $reg->documents->pluck('document_type')->unique();
                                                    $verifiedTypes = $reg->documents->whereNotNull('verified_at')->pluck('document_type')->unique();
                                                    $uploadedCount = $uploadedTypes->intersect($requiredTypes)->count();
                                                    $verifiedCount = $verifiedTypes->intersect($requiredTypes)->count();
                                                    $totalRequired = count($requiredTypes);
                                                    $docPct = $totalRequired > 0 ? round(($verifiedCount / $totalRequired) * 100) : 0;
                                                    $docColor = $verifiedCount === $totalRequired ? 'text-green-600' : ($uploadedCount > 0 ? 'text-yellow-600' : 'text-gray-500');
                                                @endphp
                                                <span class="inline-flex items-center gap-1 text-xs font-medium {{ $docColor }}">
                                                    <span class="text-gray-500">{{ $verifiedCount }}/{{ $totalRequired }}</span>
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </span>
                                                <div class="mt-1 w-16 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full {{ $verifiedCount === $totalRequired ? 'bg-green-500' : 'bg-yellow-500' }}" style="width: {{ $docPct }}%"></div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('registration.show', $reg) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

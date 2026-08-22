<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pendaftaran Saya
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('applicant.profile') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    Biodata Saya
                </a>
                <a href="{{ route('registration.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Daftar Baru
                </a>
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

            <x-help-steps title="Alur pendaftaran" icon="fa-route" :steps="[
                'Pastikan <strong>Profil/Biodata</strong> sudah lengkap (klik Biodata Saya / Lengkapi Profil jika ada peringatan kuning).',
                'Klik <strong>Daftar Baru</strong> untuk membuat pendaftaran.',
                'Setelah pendaftaran jadi, buka <strong>Detail</strong> untuk upload dokumen &amp; bayar sebelum batas waktu habis.',
                'Pantau <strong>Status</strong> dan <strong>Pembayaran</strong> di tabel — hubungi panitia jika butuh bantuan.',
            ]" />

            @if (!auth()->user()->applicant)
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                    <p class="font-medium">Profil belum lengkap!</p>
                    <p class="text-sm mt-1">Silakan lengkapi profil Anda terlebih dahulu sebelum mendaftar.</p>
                    <a href="{{ route('applicant.profile') }}" class="mt-2 inline-block bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                        Lengkapi Profil
                    </a>
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

            @if (!$registrations->isEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                        <p class="text-sm font-medium text-gray-500">Total Pendaftaran</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $summary['total'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                        <p class="text-sm font-medium text-gray-500">Menunggu Verifikasi</p>
                        <p class="mt-1 text-2xl font-bold text-yellow-600">{{ $summary['pending'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                        <p class="text-sm font-medium text-gray-500">Terverifikasi</p>
                        <p class="mt-1 text-2xl font-bold text-blue-600">{{ $summary['verified'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                        <p class="text-sm font-medium text-gray-500">Diterima / Terdaftar</p>
                        <p class="mt-1 text-2xl font-bold text-green-600">{{ $summary['accepted'] }}</p>
                    </div>
                </div>
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
                                                {{ $reg->registrationPeriod->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $reg->registrationTrack->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'verified' => 'bg-blue-100 text-blue-800',
                                                    'rejected' => 'bg-red-100 text-red-800',
                                                    'accepted' => 'bg-green-100 text-green-800',
                                                    're_registration_complete' => 'bg-purple-100 text-purple-800',
                                                    'canceled' => 'bg-gray-300 text-gray-700',
                                                ];
                                                $statusLabels = [
                                                    'pending' => 'Menunggu Verifikasi',
                                                    'verified' => 'Terverifikasi',
                                                    'rejected' => 'Ditolak',
                                                    'accepted' => 'Diterima',
                                                    're_registration_complete' => 'Terdaftar',
                                                    'canceled' => 'Dibatalkan',
                                                ];
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$reg->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $statusLabels[$reg->status] ?? ucfirst(str_replace('_', ' ', $reg->status)) }}
                                                </span>
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
                                                @php
                                                    $paymentColors = [
                                                        'unpaid' => 'bg-gray-100 text-gray-800',
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'paid' => 'bg-green-100 text-green-800',
                                                        'failed' => 'bg-red-100 text-red-800',
                                                    ];
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $paymentColors[$reg->payment_status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst($reg->payment_status) }}
                                                </span>
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

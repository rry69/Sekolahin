<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Pendaftaran
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <x-help-steps title="Selesaikan pendaftaran — 3 hal sebelum batas waktu" icon="fa-file-circle-check" :steps="[
                '<strong>Upload Dokumen</strong> di bagian Upload Dokumen (foto, KK, akta, rapor — pilih file lalu Upload Semua). Dokumen yang ditolak ada alasan, upload ulang.',
                '<strong>Pembayaran</strong> di bagian Pembayaran — pilih Bayar Online via Xendit atau transfer manual + upload bukti.',
                '<strong>Pantau Status</strong> di atas — Belum Lengkap → Menunggu Verifikasi → Diterima. Jika Diterima, tombol Daftar Ulang akan muncul.',
            ]" />

            @if (!isset($isAdmin) || !$isAdmin)
                @php
                    $rejectedDocs = $registration->documents->whereNotNull('verification_notes');
                @endphp
                @if ($registration->status === 'rejected' || $rejectedDocs->count() > 0)
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <p class="font-semibold">⚠ {{ $registration->status === 'rejected' ? 'Pendaftaran Anda ditolak' : 'Dokumen Anda ada yang ditolak' }}</p>
                        <p class="text-sm mt-1">
                            @if ($rejectedDocs->count() > 0)
                                @foreach ($rejectedDocs as $rejectedDoc)
                                    <span class="block mt-1">{{ $rejectedDoc->document_type }}: {{ $rejectedDoc->verification_notes }}</span>
                                @endforeach
                            @elseif ($registration->verified_notes)
                                <span class="block mt-1">Alasan: {{ $registration->verified_notes }}</span>
                            @endif
                        </p>
                        <p class="text-sm mt-2 font-medium">Silakan upload ulang dokumen yang ditolak di bagian <strong>Upload Dokumen</strong> di bawah.</p>
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
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <p class="font-semibold">⚠ Batas waktu telah terlewati!</p>
                            <p class="text-sm mt-1">Pendaftaran ini akan segera dibatalkan otomatis karena melebihi batas waktu penyelesaian ({{ $registration->deadline_at->format('d M Y H:i') }}).</p>
                        </div>
                    @elseif ($hoursRemaining !== null && $hoursRemaining <= 24)
                        <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                            <p class="font-semibold">⏰ Segera selesaikan pendaftaran Anda!</p>
                            <p class="text-sm mt-1">Sisa waktu: <span class="font-bold">{{ $registration->getDeadlineLabel() }}</span> (sampai {{ $registration->deadline_at->format('d M Y H:i') }}). Segera lengkapi dokumen dan lakukan pembayaran sebelum pendaftaran dibatalkan otomatis.</p>
                        </div>
                    @else
                        <div class="mb-4 bg-blue-50 border border-blue-300 text-blue-700 px-4 py-3 rounded">
                            <p class="text-sm"><span class="font-semibold">🕒 Batas waktu penyelesaian:</span> {{ $registration->deadline_at->format('d M Y H:i') }} (sisa {{ $registration->getDeadlineLabel() }})</p>
                        </div>
                    @endif
                @endif
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="border-b pb-4 mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">{{ $registration->registration_number }}</h3>
                        <p class="text-sm text-gray-500 mt-1">Dibuat: {{ $registration->created_at->format('d M Y H:i') }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Informasi Pendaftar</h4>
                            <div class="space-y-2">
                                <p><span class="font-medium">Nama:</span> {{ $registration->applicant->full_name }}</p>
                                <p><span class="font-medium">Email:</span> {{ $registration->applicant->user->email }}</p>
                                @if ($registration->applicant->phone)
                                    <p><span class="font-medium">Telepon:</span> {{ $registration->applicant->phone }}</p>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Informasi Pendaftaran</h4>
                            <div class="space-y-2">
                                <p><span class="font-medium">Jenjang:</span> {{ $registration->registrationPeriod->schoolLevel->name }}</p>
                                <p><span class="font-medium">Periode:</span> {{ $registration->registrationPeriod->name }}</p>
                                <p><span class="font-medium">Jalur:</span> {{ $registration->registrationTrack->name }}</p>
                                <p><span class="font-medium">Sekolah:</span> {{ $registration->school->name }}</p>
                                @if($registration->major)
                                <p><span class="font-medium">Jurusan Pilihan:</span> {{ $registration->major->name }}</p>
                                @endif
                                @if($registration->finalMajor)
                                    <p><span class="font-medium">Jurusan Diterima:</span> {{ $registration->finalMajor->name }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Status Pendaftaran</h4>
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

                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                    'verified' => 'bg-blue-100 text-blue-800 border-blue-300',
                                    'rejected' => 'bg-red-100 text-red-800 border-red-300',
                                    'accepted' => 'bg-green-100 text-green-800 border-green-300',
                                    're_registration_complete' => 'bg-purple-100 text-purple-800 border-purple-300',
                                    'canceled' => 'bg-gray-300 text-gray-700 border-gray-400',
                                ];

                                if ($registration->status === 'pending' && !$docsComplete) {
                                    $statusLabel = 'Belum Lengkap';
                                    $statusColor = 'bg-gray-100 text-gray-800 border-gray-300';
                                } else {
                                    $statusLabels = [
                                        'pending' => 'Menunggu Verifikasi',
                                        'verified' => 'Terverifikasi',
                                        'rejected' => 'Ditolak',
                                        'accepted' => 'Diterima',
                                        're_registration_complete' => 'Terdaftar',
                                        'canceled' => 'Dibatalkan',
                                    ];
                                    $statusLabel = $statusLabels[$registration->status] ?? ucfirst($registration->status);
                                    $statusColor = $statusColors[$registration->status] ?? 'bg-gray-100 text-gray-800 border-gray-300';
                                }
                            @endphp
                            <div class="border-2 rounded-lg p-4 {{ $statusColor }}">
                                <p class="font-bold text-lg">{{ $statusLabel }}</p>
                                @if ($registration->status === 'pending' && !$docsComplete)
                                    <p class="text-sm mt-1">Lengkapi dokumen yang masih belum diupload.</p>
                                @endif
                                @if ($registration->documents_verified_at)
                                    <p class="text-sm mt-1">Diverifikasi: {{ $registration->documents_verified_at->format('d M Y H:i') }}</p>
                                @endif
                                @if ($registration->deadline_at && $registration->status === 'pending')
                                    @php
                                        $hoursRemaining = $registration->getDeadlineHoursRemaining();
                                        $isExpired = $registration->isDeadlineExpired();
                                    @endphp
                                    @if ($isExpired)
                                        <p class="text-sm mt-1 text-red-600 font-medium">Batas waktu telah terlewati. Pendaftaran akan dibatalkan otomatis.</p>
                                    @elseif ($hoursRemaining !== null)
                                        <p class="text-sm mt-1">
                                            @if ($hoursRemaining > 24)
                                                @php $days = floor($hoursRemaining / 24); @endphp
                                                Sisa waktu: {{ $days }} hari {{ $hoursRemaining % 24 }} jam
                                            @else
                                                Sisa waktu: {{ $hoursRemaining }} jam
                                            @endif
                                        </p>
                                    @endif
                                @endif
                                @if ($registration->canceled_at)
                                    <p class="text-sm mt-1 text-red-600">Dibatalkan pada: {{ $registration->canceled_at->format('d M Y H:i') }}</p>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Status Pembayaran</h4>
                            @php
                                $paymentColors = [
                                    'unpaid' => 'bg-gray-100 text-gray-800 border-gray-300',
                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                    'paid' => 'bg-green-100 text-green-800 border-green-300',
                                    'failed' => 'bg-red-100 text-red-800 border-red-300',
                                ];
                                $paymentLabels = [
                                    'unpaid' => 'Belum Dibayar',
                                    'pending' => 'Menunggu Konfirmasi',
                                    'paid' => 'Lunas',
                                    'failed' => 'Gagal',
                                ];
                            @endphp
                            <div class="border-2 rounded-lg p-4 {{ $paymentColors[$registration->payment_status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                                <p class="font-bold text-lg">{{ $paymentLabels[$registration->payment_status] ?? ucfirst($registration->payment_status) }}</p>
                                @if ($registration->payment_amount)
                                    <p class="text-sm mt-1">Jumlah: Rp {{ number_format($registration->payment_amount, 0, ',', '.') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($registration->notes)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Catatan</h4>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <p class="text-gray-700">{{ $registration->notes }}</p>
                            </div>
                        </div>
                    @endif

                    @if($docsComplete && $registration->payment_status === 'paid' && !in_array($registration->status, ['accepted', 're_registration_complete']))
                        <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                            <p class="text-blue-900 font-semibold">✓ Dokumen dan Pembayaran Lengkap</p>
                            <p class="text-blue-800 text-sm mt-1">Menunggu verifikasi panitia — setelah diverifikasi akan muncul instruksi cetak kartu daftar ulang.</p>
                        </div>
                    @endif

                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Upload Dokumen</h4>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
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
                                $isStudentView = !isset($isAdmin) || !$isAdmin;
                                $multi = ['rapor'];
                            @endphp

                            <form action="{{ route('registration.documents.upload', $registration) }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="space-y-4">
                                @foreach($documentTypes as $type => $label)
                                    @php $docsOfType = $uploadedDocs->get($type, collect()); @endphp
                                    <div class="border border-gray-300 rounded-lg p-4">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h5 class="font-medium text-gray-900">{{ $label }}</h5>
                                                @if($docsOfType->count() > 0)
                                                    @foreach($docsOfType as $doc)
                                                        <p class="text-sm text-gray-600 mt-1">
                                                            <button type="button" onclick="showFileModal('{{ Storage::url($doc->file_path) }}', '{{ $doc->file_name }}')" class="text-blue-600 hover:underline">{{ $doc->file_name }}</button>
                                                            @if($doc->verified_at)
                                                                <span class="ml-2 px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded">Terverifikasi</span>
                                                            @elseif($doc->verification_notes)
                                                                <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded font-medium">Ditolak</span>
                                                                <div class="mt-1 bg-red-50 border border-red-200 rounded p-2">
                                                                    <p class="text-xs font-medium text-red-700">Alasan penolakan:</p>
                                                                    <p class="text-xs text-red-600 mt-0.5">{{ $doc->verification_notes }}</p>
                                                                </div>
                                                            @else
                                                                <span class="ml-2 px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded">Menunggu Verifikasi</span>
                                                            @endif
                                                        </p>
                                                        <div class="mt-1">
                                                            @if(isset($isAdmin) && $isAdmin && !$doc->verified_at)
                                                                <button type="button" onclick="submitDocAction('{{ route('admin.documents.verify', $doc) }}', 'PATCH')" class="text-sm text-green-600 hover:underline">Verifikasi</button>
                                                                <button type="button" onclick="openRejectModal({{ $doc->id }})" class="text-sm text-red-600 hover:underline ml-2">Tolak</button>
                                                            @elseif($isStudentView)
                                                                <button type="button" onclick="if(confirm('Hapus dokumen ini?')) submitDocAction('{{ route('registration.documents.delete', [$registration, $doc]) }}', 'DELETE')" class="text-sm text-red-600 hover:underline">Hapus</button>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                    @if($isStudentView)
                                                        <p class="text-xs text-gray-400 mt-2">{{ in_array($type, $multi) ? 'Tambah file lain / ganti dokumen di bawah.' : 'Pilih file jika ingin mengganti dokumen ini.' }}</p>
                                                    @endif
                                                @else
                                                    @if(isset($isAdmin) && $isAdmin)
                                                        <p class="text-sm text-gray-500 mt-2">Belum diupload</p>
                                                    @else
                                                        <label class="block mt-2 text-sm text-gray-500">Pilih file:</label>
                                                    @endif
                                                @endif
                                            </div>

                                            @if($isStudentView)
                                                <div class="ml-4">
                                                    <input type="file" name="documents[{{ $type }}]{{ in_array($type, $multi) ? '[]' : '' }}" {{ in_array($type, $multi) ? 'multiple' : '' }}
                                                        accept=".pdf,.jpg,.jpeg,.png"
                                                        class="text-sm border border-gray-300 rounded px-2 py-1">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                </div>

                                @if($isStudentView)
                                    <div class="mt-4">
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                            Upload Semua Dokumen
                                        </button>
                                        <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (max 2MB). Pilih file pada kolom isi, sekali klik akan mengunggah semua; dokumen yang sudah ada ditimpa.</p>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
    </div>

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
    @endphp
    @if($payLocked)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Pembayaran</h4>
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                <p class="text-sm font-medium text-amber-800">Pembayaran jalur {{ $trackNameForPay }} belum tersedia</p>
                <p class="text-sm text-amber-700 mt-1">Lengkapi berkas lalu tunggu panitia memverifikasi. Setelah status berkas <span class="font-semibold">Terverifikasi</span>, nominal biaya akan muncul di sini.</p>
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
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Pembayaran</h4>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <p class="text-sm text-gray-700 mb-2">Biaya Pendaftaran: <span class="font-bold text-lg">Rp {{ number_format((float)$paymentAmount, 0, ',', '.') }}</span></p>
                @if($trackNote)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                        <p class="text-sm text-gray-800"><span class="font-medium">Termasuk:</span> {{ $trackNote }}</p>
                    </div>
                @endif
                
                <form action="{{ route('payments.store') }}" method="POST" class="mb-4">
                    @csrf
                    <input type="hidden" name="registration_id" value="{{ $registration->id }}">
                    <input type="hidden" name="payment_type" value="registration_fee">
                    <input type="hidden" name="amount" value="{{ $paymentAmount }}">
                    <input type="hidden" name="payment_method" value="online">
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-medium flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Bayar Online via Xendit
                    </button>
                    <p class="text-xs text-gray-500 text-center mt-2">Transfer Bank, E-Wallet, Retail Store</p>
                </form>
                
                <div class="border-t pt-4">
                    @php
                        $bankName = App\Models\Setting::get('bank_name', '');
                        $bankNumber = App\Models\Setting::get('bank_account_number');
                        $bankAccountName = App\Models\Setting::get('bank_account_name');
                        $paymentNote = App\Models\Setting::get('payment_note');
                    @endphp
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                        <p class="text-sm text-gray-800 font-medium mb-1">{{ $paymentNote ?: 'Transfer ke rekening berikut:' }}</p>
                        @if($bankNumber)
                            <p class="text-base font-bold text-gray-900">{{ $bankName }} - {{ $bankNumber }}</p>
                            <p class="text-sm text-gray-700">a.n. {{ $bankAccountName }}</p>
                        @else
                            <p class="text-sm text-gray-600">Nomor rekening belum diatur admin.</p>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Setelah transfer, upload bukti transfer manual:</p>
                    <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="registration_id" value="{{ $registration->id }}">
                        <input type="hidden" name="payment_type" value="registration_fee">
                        <input type="hidden" name="amount" value="{{ $paymentAmount }}">
                        <input type="hidden" name="payment_method" value="bank_transfer">
                        
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Transfer</label>
                            <input type="file" name="proof_file" accept=".pdf,.jpg,.jpeg,.png" required class="w-full text-sm border border-gray-300 rounded px-3 py-2">
                            <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (max 2MB)</p>
                        </div>
                        
                        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium">
                            Upload Bukti Transfer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @php
        $payments = $registration->payments()->orderBy('created_at', 'desc')->get()
            ->reject(fn ($p) => \App\Models\Payment::isAbandonedOnline($p));
        $hiddenInvoices = $registration->payments()->get()->filter(fn ($p) => \App\Models\Payment::isAbandonedOnline($p))->count();
    @endphp
    @if($payments->count() > 0)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Riwayat Pembayaran</h4>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-3">
                @foreach($payments as $payment)
                    <div class="border border-gray-300 rounded p-3 bg-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-gray-900">
                                    @if($payment->payment_method === 'online')
                                        Pembayaran Online (Xendit)
                                    @else
                                        Transfer Manual
                                    @endif
                                </p>
                                @if($payment->payment_method === 'online' && $payment->xendit_payment_method)
                                    <p class="text-xs text-gray-600">Channel: <span class="font-medium">{{ \App\Services\XenditService::friendlyXenditMethod($payment->xendit_payment_method) }}</span> <span class="text-gray-400">({{ $payment->xendit_payment_method }})</span></p>
                                @endif
                                <p class="text-sm text-gray-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">{{ $payment->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                @php
                                    $paymentStatusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'verified' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs rounded {{ $paymentStatusColors[$payment->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                        </div>
                        
                        @if($payment->rejection_reason)
                            <p class="text-xs text-red-600 mt-2 bg-red-50 p-2 rounded">Ditolak: {{ $payment->rejection_reason }}</p>
                        @endif
                        
                        @if($payment->notes)
                            <p class="text-xs text-gray-600 mt-2 bg-gray-50 p-2 rounded">{{ $payment->notes }}</p>
                        @endif
                        
                        <a href="{{ route('payments.show', $payment) }}" class="block mt-2 text-sm text-blue-600 hover:underline">
                            Lihat Detail →
                        </a>
                        @if($payment->invoice_pdf)
                            <a href="{{ route('payments.invoice', $payment) }}" target="_blank" class="block mt-1 text-sm text-blue-600 hover:underline">
                                Invoice (PDF) →
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
            @if($hiddenInvoices > 0)
                <p class="text-xs text-gray-500 mt-2">{{ $hiddenInvoices }} pembayaran yang tidak dilanjutkan disembunyikan dari riwayat.</p>
            @endif
        </div>
    @endif

    @if(in_array($registration->status, ['accepted', 're_registration_complete']))
        @php
            $reReg = $registration->reRegistration;
        @endphp
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <p class="text-green-900 font-semibold">✓ Anda telah diterima sebagai siswa {{ $registration->school->name }}</p>
            @if($registration->finalMajor)
                <p class="text-green-800 text-sm mt-1">Jurusan: {{ $registration->finalMajor->name }}</p>
            @endif
            @if($registration->applicant->student_number)
                <p class="text-green-800 text-sm mt-1">Nomor Induk Siswa (NIS): <span class="font-bold">{{ $registration->applicant->student_number }}</span></p>
            @endif

            @if($registration->status === 're_registration_complete')
                <p class="text-green-700 text-sm mt-2">Daftar ulang selesai — silakan unduh kartu daftar ulang.</p>
                <a href="{{ route('registration.proof', $registration) }}" target="_blank" class="mt-3 inline-block px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium">Unduh Kartu Daftar Ulang</a>
            @else
                @php
                    $rrLevelId = $registration->registrationPeriod->school_level_id ?? null;
                    $rrStart = $rrLevelId ? \App\Models\Setting::reRegistrationStartForLevel((int) $rrLevelId) : null;
                    $rrEnd = $rrLevelId ? \App\Models\Setting::reRegistrationEndForLevel((int) $rrLevelId) : null;
                @endphp
                <p class="text-green-800 text-sm mt-2">Daftar ulang dilakukan <strong>offline</strong> di sekolah. Unduh kartu daftar ulang dan bawa dokumen asli sesuai jadwal yang ditentukan.</p>
                @if($rrStart || $rrEnd)
                    <p class="text-green-800 text-sm mt-1">
                        <span class="font-semibold">Jadwal daftar ulang:</span>
                        @if($rrStart && $rrEnd)
                            <span class="font-bold">{{ \Illuminate\Support\Carbon::parse($rrStart)->translatedFormat('d F Y') }} – {{ \Illuminate\Support\Carbon::parse($rrEnd)->translatedFormat('d F Y') }}</span>
                        @elseif($rrStart)
                            mulai <span class="font-bold">{{ \Illuminate\Support\Carbon::parse($rrStart)->translatedFormat('d F Y') }}</span>
                        @else
                            sampai <span class="font-bold">{{ \Illuminate\Support\Carbon::parse($rrEnd)->translatedFormat('d F Y') }}</span>
                        @endif
                    </p>
                @endif
                <a href="{{ route('registration.proof', $registration) }}" target="_blank" class="mt-3 inline-block px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium">Unduh Kartu Daftar Ulang</a>
                @if($reReg && $reReg->verification_code)
                    <p class="text-sm text-green-800 mt-2">Kode verifikasi: <span class="font-mono font-bold tracking-widest text-base text-gray-900">{{ $reReg->verification_code }}</span> — tunjukkan kepada panitia di sekolah.</p>
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
                    <div class="mt-4 border-t border-green-200 pt-4">
                        <p class="text-green-900 font-semibold">Biaya Daftar Ulang: <span class="font-bold">Rp {{ number_format($reRegFee, 0, ',', '.') }}</span></p>
                        <p class="text-sm text-green-800 mt-1">Selesaikan pembayaran biaya daftar ulang sebelum/bersamaan dengan daftar ulang di sekolah.</p>
                        @if($reRegFeePending)
                            <p class="text-sm text-amber-700 mt-2">Bukti pembayaran biaya daftar ulang Anda sedang <strong>menunggu verifikasi</strong> panitia.</p>
                        @else
                            <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                                @csrf
                                <input type="hidden" name="registration_id" value="{{ $registration->id }}">
                                <input type="hidden" name="payment_type" value="re_registration_fee">
                                <input type="hidden" name="amount" value="{{ $reRegFee }}">
                                <input type="hidden" name="payment_method" value="bank_transfer">
                                <div class="mb-2">
                                    <label class="block text-sm font-medium text-green-800 mb-1">Bukti Transfer Biaya Daftar Ulang</label>
                                    <input type="file" name="proof_file" accept=".pdf,.jpg,.jpeg,.png" required class="w-full text-sm border border-gray-300 rounded px-3 py-2">
                                </div>
                                <button type="submit" class="inline-block px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium">Upload Bukti Bayar Daftar Ulang</button>
                            </form>
                        @endif
                    </div>
                @elseif($reRegFeePaid)
                    <p class="text-sm text-green-800 mt-2">✓ Biaya daftar ulang <strong>lunas</strong>.</p>
                @endif
            @endif
        </div>
    @endif

                    <div class="flex justify-between mt-8">
                        <a href="{{ route('registration.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function submitDocAction(url, method) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = `<input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                              ${method !== 'POST' ? `<input type="hidden" name="_method" value="${method}">` : ''}`;
            document.body.appendChild(form);
            form.submit();
        }
    </script>

    @if(isset($isAdmin) && $isAdmin)
    <script>
        function openRejectModal(documentId) {
            const notes = prompt('Masukkan alasan penolakan:');
            if (notes && notes.trim() !== '') {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/documents/${documentId}/reject`;
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PATCH';
                
                const notesInput = document.createElement('input');
                notesInput.type = 'hidden';
                notesInput.name = 'verification_notes';
                notesInput.value = notes;
                
                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                form.appendChild(notesInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
    @endif

    @include('components.file-preview-modal')
</x-app-layout>

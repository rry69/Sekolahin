@extends('layouts.dashboard')
@section('title', 'Detail Pendaftaran')
@section('content')
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
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

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Detail Pendaftaran</h3>
                        <p class="text-sm text-gray-600 mt-1">No. Registrasi: {{ $registration->registration_number }}</p>
                    </div>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                            'verified' => 'bg-blue-100 text-blue-800 border-blue-300',
                            'accepted' => 'bg-green-100 text-green-800 border-green-300',
                            'rejected' => 'bg-red-100 text-red-800 border-red-300',
                            'canceled' => 'bg-gray-300 text-gray-700 border-gray-400',
                        ];
                    @endphp
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 text-sm font-semibold rounded border {{ $statusColors[$registration->status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                            {{ ucfirst($registration->status) }}
                        </span>
                        @if ($registration->deadline_at && $registration->status === 'pending')
                            @php
                                $hoursRemaining = $registration->getDeadlineHoursRemaining();
                            @endphp
                            <span class="text-xs text-gray-500">
                                Batas waktu: {{ $registration->deadline_at->format('d M Y H:i') }}
                                @if ($hoursRemaining !== null)
                                    ({{ $hoursRemaining }} jam tersisa)
                                @endif
                            </span>
                        @endif
                        @if ($registration->canceled_at)
                            <span class="text-xs text-gray-500">
                                Dibatalkan: {{ $registration->canceled_at->format('d M Y H:i') }}
                            </span>
                        @endif
                        <form action="{{ route('admin.registrations.delete-account', $registration) }}" method="POST" onsubmit="return confirm('Hapus akun siswa {{ $registration->applicant?->full_name ?? '' }}? Seluruh data pendaftaran, ujian, dan pembayarannya akan ikut terhapus permanen.')">
                            @csrf
                            <button type="submit" class="px-3 py-1 text-sm font-semibold rounded bg-red-600 text-white hover:bg-red-700">
                                Hapus Akun
                            </button>
                        </form>
                    </div>
                </div>

                <div class="border-b pb-6 mb-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Informasi Pendaftar</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Nama Lengkap</p>
                            <p class="font-medium text-gray-900">{{ $registration->applicant->full_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">NISN</p>
                            <p class="font-medium text-gray-900">{{ $registration->applicant->nisn ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Verifikasi NISN</p>
                            <p class="font-medium text-gray-900">
                                @php $vstatus = $registration->applicant->nisn_verification_status ?? null; @endphp
                                @if ($vstatus === 'verified')
                                    <span class="text-green-600">✓ Terverifikasi</span>
                                    @if ($registration->applicant->nisn_verified_at)
                                        <span class="text-xs text-gray-500">({{ $registration->applicant->nisn_verified_at->format('d M Y H:i') }})</span>
                                    @endif
                                @elseif ($vstatus === 'unavailable')
                                    <span class="text-yellow-600">Menunggu (server NISN tidak dapat diakses)</span>
                                @elseif ($vstatus === 'failed')
                                    <span class="text-red-600">Gagal</span>
                                @else
                                    <span class="text-gray-400">Belum diverifikasi</span>
                                @endif
                            </p>
                            @if ($registration->applicant->nisn_verified_name)
                                <p class="text-xs text-gray-500">Nama di Kemendikdasmen: {{ $registration->applicant->nisn_verified_name }}</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">NIK</p>
                            <p class="font-medium text-gray-900">{{ $registration->applicant->nik ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-medium text-gray-900">{{ $registration->applicant->user->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Jenis Kelamin</p>
                            <p class="font-medium text-gray-900">{{ $registration->applicant->gender ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tempat/Tanggal Lahir</p>
                            <p class="font-medium text-gray-900">{{ $registration->applicant->birth_place ?? '-' }}, {{ $registration->applicant->birth_date ? $registration->applicant->birth_date->format('d M Y') : '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="border-b pb-6 mb-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Pilihan Sekolah & Jurusan</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Sekolah</p>
                            <p class="font-medium text-gray-900">{{ $registration->school->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Jalur</p>
                            <p class="font-medium text-gray-900">{{ $registration->registrationTrack->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Jurusan Pilihan</p>
                            <p class="font-medium text-gray-900">{{ $registration->major->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Jurusan Diterima</p>
                            <p class="font-medium text-gray-900">{{ $registration->finalMajor->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="border-b pb-6 mb-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Verifikasi Dokumen</h4>
                    @forelse ($registration->documents as $doc)
                        <div class="py-3 border-b border-gray-100 last:border-0" id="doc-row-{{ $doc->id }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $doc->document_type }}</p>
                                    <p class="text-xs text-gray-500">{{ $doc->file_name }}</p>
                                    @if($doc->verification_notes)
                                        <p class="text-xs text-red-600 mt-1 bg-red-50 border border-red-200 rounded p-1.5">Alasan: {{ $doc->verification_notes }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 flex-wrap justify-end" id="doc-actions-{{ $doc->id }}">
                                    <button type="button" onclick="showFileModal('{{ Storage::url($doc->file_path) }}', '{{ $doc->document_type }}')" class="text-sm text-blue-600 hover:underline">Lihat</button>
                                    @if($doc->verified_at)
                                        <span id="doc-badge-{{ $doc->id }}" class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Terverifikasi</span>
                                        <span id="doc-verify-btns-{{ $doc->id }}" class="hidden items-center gap-2">
                                            <button type="button" onclick="openDocVerifyModal({{ $doc->id }}, '{{ addslashes($doc->document_type) }}')" class="px-3 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700">✓ Verifikasi</button>
                                            <button type="button" onclick="toggleDocReject({{ $doc->id }})" class="px-3 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700">✕ Tolak</button>
                                        </span>
                                        <span id="doc-verified-btns-{{ $doc->id }}" class="inline-flex items-center gap-2">
                                            <button type="button" onclick="openDocUnverifyModal({{ $doc->id }}, '{{ addslashes($doc->document_type) }}')" class="px-3 py-1 bg-amber-500 text-white text-xs font-medium rounded hover:bg-amber-600">↩ Batal Verifikasi</button>
                                        </span>
                                    @elseif($doc->verification_notes)
                                        <span id="doc-badge-{{ $doc->id }}" class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded font-medium">Ditolak</span>
                                        <span id="doc-verify-btns-{{ $doc->id }}" class="hidden items-center gap-2"></span>
                                        <span id="doc-verified-btns-{{ $doc->id }}" class="hidden"></span>
                                    @else
                                        <span id="doc-badge-{{ $doc->id }}" class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Menunggu</span>
                                        <span id="doc-verify-btns-{{ $doc->id }}" class="inline-flex items-center gap-2">
                                            <button type="button" onclick="openDocVerifyModal({{ $doc->id }}, '{{ addslashes($doc->document_type) }}')" class="px-3 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700">✓ Verifikasi</button>
                                            <button type="button" onclick="toggleDocReject({{ $doc->id }})" class="px-3 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700">✕ Tolak</button>
                                        </span>
                                        <span id="doc-verified-btns-{{ $doc->id }}" class="hidden items-center gap-2">
                                            <button type="button" onclick="openDocUnverifyModal({{ $doc->id }}, '{{ addslashes($doc->document_type) }}')" class="px-3 py-1 bg-amber-500 text-white text-xs font-medium rounded hover:bg-amber-600">↩ Batal Verifikasi</button>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if(!$doc->verified_at && !$doc->verification_notes)
                                <div id="doc-reject-{{ $doc->id }}" class="hidden mt-3 bg-red-50 border border-red-200 rounded p-3">
                                    <p class="text-xs font-medium text-red-800 mb-2">Tolak dokumen — beri alasan (file akan dihapus):</p>
                                    <form action="{{ route('admin.documents.reject', $doc) }}" method="POST" class="flex gap-2 items-center">
                                        @csrf
                                        @method('PATCH')
                                        <input type="text" name="verification_notes" placeholder="Alasan penolakan (wajib)" required maxlength="500" class="flex-1 border-gray-300 rounded-md shadow-sm text-sm px-3 py-2">
                                        <button type="submit" class="px-3 py-2 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700">Kirim</button>
                                        <button type="button" onclick="toggleDocReject({{ $doc->id }})" class="px-3 py-2 text-xs text-gray-600 hover:text-gray-800">Batal</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada dokumen</p>
                    @endforelse
                </div>

                <div class="border-b pb-6 mb-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Verifikasi Pendaftaran</h4>
                    @if ($registration->status === 'pending' || $registration->status === 'rejected')
                        @php
                            $docsVerified = $registration->hasAllDocumentsVerified();
                            $requiredDocs = $registration->requiredDocumentTypes();
                        @endphp
                        @if(!$docsVerified)
                            <div id="docVerifyLock" class="mb-3 bg-amber-50 border border-amber-200 rounded p-3 text-sm text-amber-800">
                                ⚠ Verifikasi pendaftaran terkunci sampai <span class="font-semibold">semua dokumen wajib</span> diverifikasi. Dokumen diverifikasi satu per satu di bagian Verifikasi Dokumen di atas.
                            </div>
                        @else
                            <div id="docVerifyLock" class="hidden mb-3 bg-amber-50 border border-amber-200 rounded p-3 text-sm text-amber-800">⚠ Verifikasi pendaftaran terkunci sampai semua dokumen wajib diverifikasi.</div>
                        @endif
                        @php
                            $isRegulerVerify = strtolower($registration->registrationTrack->name ?? '') === 'reguler';
                            $verifyFee = $registration->payment_amount;
                            if ($verifyFee === null && $isRegulerVerify) {
                                $raw = \App\Models\Setting::get('fee_' . ($registration->registrationPeriod->school_level_id ?? '') . '_' . $registration->registration_track_id);
                                $verifyFee = ($raw !== null && $raw !== '' && is_numeric($raw)) ? (float) $raw : 500000;
                            }
                        @endphp
                        <form action="{{ route('admin.registrations.verify', $registration) }}" method="POST" class="space-y-3">
                            @csrf
                            <div class="flex flex-wrap gap-4 items-center">
                                <select name="status" class="border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="verified">Verifikasi (Terima Berkas)</option>
                                    <option value="rejected">Tolak</option>
                                </select>
                                @if(!$isRegulerVerify)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Biaya Pendaftaran (Rp)</label>
                                        <input type="number" name="payment_amount" value="{{ old('payment_amount', $verifyFee) }}" min="0" step="1000" placeholder="0 = gratis" class="border-gray-300 rounded-md shadow-sm text-sm w-44">
                                    </div>
                                @else
                                    <span class="text-sm text-gray-600">Biaya: <span class="font-semibold text-gray-900">Rp {{ number_format($verifyFee, 0, ',', '.') }}</span> <span class="text-xs text-gray-500">(otomatis dari Setting)</span></span>
                                @endif
                                <input type="text" name="verified_notes" placeholder="Catatan verifikasi" class="flex-1 border-gray-300 rounded-md shadow-sm text-sm min-w-[180px]">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Simpan</button>
                            </div>
                            @if(!$isRegulerVerify)
                                <p class="text-xs text-gray-500">Isi nominal per siswa (tiap siswa bisa beda). Isi <code>0</code> untuk gratis → langsung lunas tanpa siswa bayar.</p>
                            @else
                                <p class="text-xs text-gray-500">Biaya Reguler otomatis dari menu Setting. Tidak perlu input manual.</p>
                            @endif
                        </form>
                    @else
                        <p class="text-sm text-gray-600">Diverifikasi oleh <span class="font-medium">{{ $registration->verifiedBy->name ?? '-' }}</span>
                            @if($registration->verified_notes)
                                — {{ $registration->verified_notes }}
                            @endif
                        </p>
                    @endif
                </div>

                @if ($registration->status === 'verified')
                <div class="border-b pb-6 mb-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Status Diterima (Otomatis)</h4>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-gray-800">Siswa otomatis terdaftar sebagai siswa setelah <span class="font-semibold">berkas diverifikasi</span> dan <span class="font-semibold">pembayaran lunas</span>. NIS diterbitkan otomatis saat itu.</p>
                    </div>
                </div>
                @endif

                <div class="border-b pb-6 mb-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Status Pembayaran</h4>
                    @php
                        $payColors = [
                            'unpaid' => 'bg-gray-100 text-gray-800 border-gray-300',
                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                            'paid' => 'bg-green-100 text-green-800 border-green-300',
                            'failed' => 'bg-red-100 text-red-800 border-red-300',
                        ];
                    @endphp
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 text-sm font-semibold rounded border {{ $payColors[$registration->payment_status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                            {{ ucfirst($registration->payment_status) }}
                        </span>
                        @if ($registration->payment_amount !== null)
                            <span class="text-sm text-gray-700">Rp {{ number_format($registration->payment_amount, 0, ',', '.') }}</span>
                        @else
                            <span class="text-xs px-2 py-1 bg-amber-100 text-amber-800 rounded">Belum ditentukan — akan muncul setelah Terverifikasi</span>
                        @endif
                    </div>
                    @php
                        $pendingPayment = $registration->payments->where('status', 'pending')->sortByDesc('id')->first();
                        $proofPayment = $registration->payments->filter(fn ($p) => !empty($p->proof_file))->sortByDesc('id')->first();
                        $invoicePayment = $latestVerifiedPayment ?? $registration->payments->whereNotNull('invoice_pdf')->sortByDesc('id')->first();
                    @endphp
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        @if($invoicePayment)
                            <a href="{{ route('payments.invoice.view', $invoicePayment) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700">Lihat Invoice</a>
                        @endif
                        @if($proofPayment)
                            <button type="button" onclick="showFileModal('{{ asset('storage/' . $proofPayment->proof_file) }}', 'Bukti Pembayaran')" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700">Lihat Bukti</button>
                        @endif
                        @if($pendingPayment)
                            <form action="{{ route('admin.payments.verify', $pendingPayment) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded hover:bg-emerald-700">Verifikasi Pembayaran</button>
                            </form>
                        @endif
                        @if(!$pendingPayment && !$invoicePayment && !$proofPayment)
                            <span class="text-xs text-gray-500">Belum ada pembayaran untuk diverifikasi.</span>
                        @endif
                    </div>
                </div>

                @php
                    $successPayments = $registration->payments->filter(fn ($p) => ! \App\Models\Payment::isAbandonedOnline($p))->sortByDesc('created_at');
                    $hiddenInvoicesAdmin = $registration->payments->filter(fn ($p) => \App\Models\Payment::isAbandonedOnline($p))->count();
                @endphp
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Riwayat Pembayaran</h4>
                    @if($successPayments->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada riwayat pembayaran</p>
                    @else
                    @forelse ($successPayments as $payment)
                        <div class="flex justify-between py-2 border-b border-gray-100 last:border-0">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }} · {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? '-')) }}</p>
                                @if($payment->payment_method === 'online' && $payment->xendit_payment_method)
                                    <p class="text-xs text-gray-600 mt-1">Channel: <span class="font-medium">{{ \App\Services\XenditService::friendlyXenditMethod($payment->xendit_payment_method) }}</span> <span class="text-gray-400">({{ $payment->xendit_payment_method }})</span> via Xendit</p>
                                @endif
                                @if($payment->invoice_pdf)
                                    <a href="{{ route('payments.invoice', $payment) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Invoice (PDF) →</a>
                                @endif
                                @if($payment->notes)
                                    <p class="text-xs text-gray-600 mt-1 bg-gray-50 p-1.5 rounded">{{ $payment->notes }}</p>
                                @endif
                                @if($payment->xendit_paid_at)
                                    <p class="text-xs text-gray-500 mt-0.5">Dibayar: {{ $payment->xendit_paid_at->format('d M Y H:i') }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 ml-3">
                                @if ($payment->proof_file)
                                    <button type="button"
                                        onclick="showFileModal('{{ asset('storage/' . $payment->proof_file) }}', 'Bukti Pembayaran')"
                                        class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                        Lihat Bukti
                                    </button>
                                @endif
                                @php $adminPaymentLabels = ['pending' => 'Pending', 'verified' => 'Lunas', 'rejected' => 'Ditolak']; @endphp
                                <span class="px-2 py-1 text-xs rounded {{ $payment->status === 'verified' ? 'bg-green-100 text-green-800' : ($payment->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $adminPaymentLabels[$payment->status] ?? ucfirst($payment->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada riwayat pembayaran</p>
                    @endforelse
                    @if($hiddenInvoicesAdmin > 0)
                        <p class="text-xs text-gray-500 mt-2">{{ $hiddenInvoicesAdmin }} invoice online yang tidak dilanjutkan disembunyikan.</p>
                    @endif
                    @endif
                </div>

                <div class="mt-6">
                    <a href="{{ route('admin.registrations.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal konfirmasi custom (mengganti browser confirm) -->
<div id="docConfirmModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-50 items-center justify-center p-4" style="display:none">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex items-start gap-3 mb-4">
            <div id="docConfirmIcon" class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 text-lg bg-green-100 text-green-600">✓</div>
            <div class="flex-1">
                <h3 id="docConfirmTitle" class="text-base font-semibold text-gray-900"></h3>
                <p id="docConfirmMessage" class="text-sm text-gray-600 mt-1"></p>
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeDocConfirmModal()" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Batal</button>
            <button type="button" id="docConfirmAction" class="px-4 py-2 text-sm font-medium rounded text-white bg-green-600 hover:bg-green-700">Ya</button>
        </div>
    </div>
</div>

<div id="docToast" class="hidden fixed top-6 right-6 z-50 bg-gray-900 text-white text-sm px-4 py-3 rounded-lg shadow-lg max-w-sm"></div>

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
        t.className = 'fixed top-6 right-6 z-50 text-sm px-4 py-3 rounded-lg shadow-lg max-w-sm ' + (isError ? 'bg-red-600 text-white' : 'bg-gray-900 text-white');
        t.classList.remove('hidden');
        setTimeout(function () { t.classList.add('hidden'); }, 3000);
    }

    window.docShowToast = showToast;

    window.openDocVerifyModal = function (id, docType) {
        pendingDocId = id;
        pendingAction = 'verify';
        document.getElementById('docConfirmTitle').textContent = 'Verifikasi dokumen?';
        document.getElementById('docConfirmMessage').textContent = 'Verifikasi dokumen "' + docType + '"? Dokumen akan ditandai Terverifikasi.';
        var icon = document.getElementById('docConfirmIcon');
        icon.textContent = '✓';
        icon.className = 'w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 text-lg bg-green-100 text-green-600';
        var btn = document.getElementById('docConfirmAction');
        btn.textContent = 'Ya, Verifikasi';
        btn.className = 'px-4 py-2 text-sm font-medium rounded text-white bg-green-600 hover:bg-green-700';
        var m = document.getElementById('docConfirmModal');
        m.style.display = 'flex';
        m.classList.remove('hidden');
        m.classList.add('flex');
    };

    window.openDocUnverifyModal = function (id, docType) {
        pendingDocId = id;
        pendingAction = 'unverify';
        document.getElementById('docConfirmTitle').textContent = 'Batalkan verifikasi?';
        document.getElementById('docConfirmMessage').textContent = 'Batalkan verifikasi dokumen "' + docType + '"? Status akan kembali menjadi Menunggu.';
        var icon = document.getElementById('docConfirmIcon');
        icon.textContent = '↩';
        icon.className = 'w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 text-lg bg-amber-100 text-amber-600';
        var btn = document.getElementById('docConfirmAction');
        btn.textContent = 'Ya, Batalkan';
        btn.className = 'px-4 py-2 text-sm font-medium rounded text-white bg-amber-500 hover:bg-amber-600';
        var m = document.getElementById('docConfirmModal');
        m.style.display = 'flex';
        m.classList.remove('hidden');
        m.classList.add('flex');
    };

    window.closeDocConfirmModal = function () {
        var m = document.getElementById('docConfirmModal');
        m.classList.add('hidden');
        m.classList.remove('flex');
        m.style.display = 'none';
        pendingDocId = null;
        pendingAction = null;
    };

    document.getElementById('docConfirmModal').addEventListener('click', function (e) {
        if (e.target === this) closeDocConfirmModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (!document.getElementById('docConfirmModal').classList.contains('hidden')) closeDocConfirmModal();
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
        btn.disabled = true;

        fetch(url, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': getToken(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        }).then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); })
        .then(function (res) {
            btn.disabled = false;
            if (!res.ok || !res.body.success) {
                showToast(res.body.message || 'Gagal memproses dokumen', true);
                return;
            }
            showToast(res.body.message || (action === 'verify' ? 'Dokumen diverifikasi' : 'Verifikasi dibatalkan'), false);
            applyDocState(id, action === 'verify' ? 'verified' : 'pending', res.body);
        }).catch(function () {
            btn.disabled = false;
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
            if (badge) { badge.textContent = 'Terverifikasi'; badge.className = 'px-2 py-1 text-xs bg-green-100 text-green-800 rounded'; }
            if (verifyBtns) { verifyBtns.classList.add('hidden'); verifyBtns.classList.remove('inline-flex'); }
            if (verifiedBtns) { verifiedBtns.classList.remove('hidden'); verifiedBtns.classList.add('inline-flex'); }
        } else {
            if (badge) { badge.textContent = 'Menunggu'; badge.className = 'px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded'; }
            if (verifyBtns) { verifyBtns.classList.remove('hidden'); verifyBtns.classList.add('inline-flex'); }
            if (verifiedBtns) { verifiedBtns.classList.add('hidden'); verifiedBtns.classList.remove('inline-flex'); }
        }

        // Kunci/buka verifikasi pendaftaran (warning kuning)
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
</script>
@endsection

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
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $doc->document_type }}</p>
                                <p class="text-xs text-gray-500">{{ $doc->file_name }}</p>
                                @if($doc->verification_notes)
                                    <p class="text-xs text-red-600 mt-1 bg-red-50 border border-red-200 rounded p-1.5">Alasan: {{ $doc->verification_notes }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="showFileModal('{{ Storage::url($doc->file_path) }}', '{{ $doc->document_type }}')" class="text-sm text-blue-600 hover:underline">Lihat</button>
                                @if($doc->verified_at)
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Terverifikasi</span>
                                @elseif($doc->verification_notes)
                                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded font-medium">Ditolak</span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Menunggu</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada dokumen</p>
                    @endforelse
                </div>

                <div class="border-b pb-6 mb-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Verifikasi Pendaftaran</h4>
                    @if ($registration->status === 'pending' || $registration->status === 'rejected')
                        <form action="{{ route('admin.registrations.verify', $registration) }}" method="POST" class="space-y-3">
                            @csrf
                            <div class="flex gap-4 items-center">
                                <select name="status" class="border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="verified">Verifikasi (Terima Berkas)</option>
                                    <option value="rejected">Tolak</option>
                                </select>
                                <input type="text" name="verified_notes" placeholder="Catatan verifikasi" class="flex-1 border-gray-300 rounded-md shadow-sm text-sm">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Simpan</button>
                            </div>
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
                    <span class="px-3 py-1 text-sm font-semibold rounded border {{ $payColors[$registration->payment_status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                        {{ ucfirst($registration->payment_status) }}
                    </span>
                    @if ($registration->payment_amount)
                        <span class="ml-2 text-sm text-gray-600">Rp {{ number_format($registration->payment_amount, 0, ',', '.') }}</span>
                    @endif
                    <form action="{{ route('admin.registrations.update-payment', $registration) }}" method="POST" class="mt-4 space-y-3">
                        @csrf
                        <div class="flex gap-4 items-center">
                            <select name="payment_status" class="border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="unpaid" {{ $registration->payment_status === 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
                                <option value="pending" {{ $registration->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $registration->payment_status === 'paid' ? 'selected' : '' }}>Lunas</option>
                                <option value="failed" {{ $registration->payment_status === 'failed' ? 'selected' : '' }}>Gagal</option>
                            </select>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Simpan Pembayaran</button>
                        </div>
                    </form>
                </div>

                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Riwayat Pembayaran</h4>
                    @forelse ($registration->payments as $payment)
                        <div class="flex justify-between py-2 border-b border-gray-100 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }} · {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? '-')) }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if ($payment->proof_file)
                                    <button type="button"
                                        onclick="showFileModal('{{ asset('storage/' . $payment->proof_file) }}', 'Bukti Pembayaran')"
                                        class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                        Lihat Bukti
                                    </button>
                                @endif
                                <span class="px-2 py-1 text-xs rounded {{ $payment->status === 'verified' ? 'bg-green-100 text-green-800' : ($payment->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada riwayat pembayaran</p>
                    @endforelse
                </div>

                <div class="mt-6">
                    <a href="{{ route('admin.registrations.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

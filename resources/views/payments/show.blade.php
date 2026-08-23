<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('registration.index') }}" class="inline-flex items-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100" aria-label="Kembali ke Pendaftaran">
                <i class="fa-solid fa-arrow-left text-lg"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Pembayaran
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Pembayaran #{{ $payment->id }}</h3>
                            <p class="text-sm text-gray-600 mt-1">No. Registrasi: {{ $payment->registration->registration_number }}</p>
                        </div>
                        <x-status-badge :status="$payment->status" type="payment" class="border px-3 py-1" />
                    </div>

                    <div class="border-b pb-6 mb-6">
                        <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Informasi Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($payment->invoice_number)
                                <div>
                                    <p class="text-sm text-gray-600">No. Invoice</p>
                                    <p class="font-medium text-gray-900">{{ $payment->invoice_number }}</p>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-600">Tipe Pembayaran</p>
                                <p class="font-medium text-gray-900">
                                    @if ($payment->payment_type === 'registration_fee')
                                        Biaya Pendaftaran
                                    @else
                                        Biaya Daftar Ulang
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Jumlah</p>
                                <p class="font-medium text-gray-900 text-lg">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Metode Pembayaran</p>
                                <p class="font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</p>
                                @if($payment->payment_method === 'online' && $payment->xendit_payment_method)
                                    <p class="text-xs text-gray-500 mt-1">Channel: {{ \App\Services\XenditService::friendlyXenditMethod($payment->xendit_payment_method) }} <span class="text-gray-400">({{ $payment->xendit_payment_method }})</span></p>
                                @endif
                                @if($payment->payment_method === 'online' && $payment->status === 'pending' && $payment->xendit_invoice_url)
                                    <a href="{{ route('payments.invoice.view', $payment) }}" class="text-xs text-blue-600 hover:underline">Lihat Invoice →</a>
                                @endif
                                @if($payment->invoice_pdf)
                                    <a href="{{ route('payments.invoice', $payment) }}" target="_blank" class="text-xs text-blue-600 hover:underline ml-2">Unduh Invoice (PDF)</a>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Tanggal Upload</p>
                                <p class="font-medium text-gray-900">{{ $payment->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    @if ($payment->proof_file)
                        <div class="border-b pb-6 mb-6">
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Bukti Pembayaran</h4>
                            <div class="flex items-center justify-between bg-gray-50 p-4 rounded">
                                <div class="flex items-center">
                                    <svg class="w-8 h-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium text-gray-900">Bukti Transfer</p>
                                        <p class="text-sm text-gray-500">{{ basename($payment->proof_file) }}</p>
                                    </div>
                                </div>
                                <button type="button" onclick="showFileModal('{{ asset('storage/' . $payment->proof_file) }}', 'Bukti Pembayaran')" class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                    Lihat Bukti
                                </button>
                            </div>
                        </div>
                    @endif

                    @if ($payment->status === 'verified')
                        <div class="border-b pb-6 mb-6">
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Informasi Verifikasi</h4>
                            <div class="bg-green-50 border border-green-200 rounded p-4">
                                <div class="flex items-start">
                                    <svg class="w-6 h-6 text-green-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium text-green-900">Pembayaran Terverifikasi</p>
                                        <p class="text-sm text-green-700 mt-1">Diverifikasi oleh: {{ $payment->verifier->name ?? '-' }}</p>
                                        <p class="text-sm text-green-700">Tanggal: {{ $payment->verified_at ? $payment->verified_at->format('d M Y H:i') : '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($payment->status === 'rejected')
                        <div class="border-b pb-6 mb-6">
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Informasi Penolakan</h4>
                            <div class="bg-red-50 border border-red-200 rounded p-4">
                                <div class="flex items-start">
                                    <svg class="w-6 h-6 text-red-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="flex-1">
                                        <p class="font-medium text-red-900">Pembayaran Ditolak</p>
                                        <p class="text-sm text-red-700 mt-1">Ditolak oleh: {{ $payment->verifier->name ?? '-' }}</p>
                                        <p class="text-sm text-red-700">Tanggal: {{ $payment->verified_at ? $payment->verified_at->format('d M Y H:i') : '-' }}</p>
                                        @if ($payment->rejection_reason)
                                            <div class="mt-3 p-3 bg-white border border-red-200 rounded">
                                                <p class="text-sm font-medium text-gray-900">Alasan:</p>
                                                <p class="text-sm text-gray-700 mt-1">{{ $payment->rejection_reason }}</p>
                                            </div>
                                        @endif
                                        <p class="text-sm text-red-600 mt-3">Silakan upload ulang bukti pembayaran yang sesuai.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($payment->status === 'pending')
                        <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-6">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-yellow-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="font-medium text-yellow-900">Menunggu Verifikasi</p>
                                    <p class="text-sm text-yellow-700 mt-1">Pembayaran Anda sedang dalam proses verifikasi oleh admin. Mohon tunggu konfirmasi.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($payment->notes)
                        <div class="border-b pb-6 mb-6">
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Catatan</h4>
                            <p class="text-gray-700">{{ $payment->notes }}</p>
                        </div>
                    @endif

                    <div class="flex justify-end">
                        <a href="{{ route('registration.show', $payment->registration) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Kembali ke Pendaftaran</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.file-preview-modal')
</x-app-layout>

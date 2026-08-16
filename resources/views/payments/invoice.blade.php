<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Invoice Pembayaran
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @php
                        $school = $registration->school;
                        $badgeClass = match ($payment->status) { 'verified' => 'bg-green-100 text-green-800 border-green-300', 'rejected' => 'bg-red-100 text-red-800 border-red-300', default => 'bg-yellow-100 text-yellow-800 border-yellow-300' };
                        $badgeLabel = match ($payment->status) { 'verified' => 'LUNAS', 'rejected' => 'DITOLAK', default => 'MENUNGGU PEMBAYARAN' };
                    @endphp

                    <div class="border-b border-gray-200 pb-4 mb-6 text-center">
                        <h3 class="text-xl font-bold text-gray-900">{{ $school->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $school->address }}</p>
                        <p class="text-sm font-medium text-gray-700 mt-1">INVOICE PEMBAYARAN</p>
                        <span class="inline-block mt-2 px-3 py-1 text-sm font-semibold rounded border {{ $badgeClass }}">{{ $badgeLabel }}</span>
                    </div>

                    <div class="border border-gray-200 rounded-lg overflow-hidden mb-6">
                        <table class="w-full text-sm">
                            <tbody>
                                <tr class="border-b border-gray-100">
                                    <td class="px-4 py-2.5 text-gray-500 w-2/5">No. Invoice</td>
                                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ $payment->invoice_number ?? '-' }}</td>
                                </tr>
                                <tr class="border-b border-gray-100">
                                    <td class="px-4 py-2.5 text-gray-500">No. Registrasi</td>
                                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ $registration->registration_number }}</td>
                                </tr>
                                <tr class="border-b border-gray-100">
                                    <td class="px-4 py-2.5 text-gray-500">Tanggal Terbit</td>
                                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ optional($payment->invoice_issued_at ?? $payment->created_at)->format('d M Y H:i') }}</td>
                                </tr>
                                <tr class="border-b border-gray-100">
                                    <td class="px-4 py-2.5 text-gray-500">Nama Lengkap</td>
                                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ $registration->applicant->full_name }}</td>
                                </tr>
                                <tr class="border-b border-gray-100">
                                    <td class="px-4 py-2.5 text-gray-500">NISN</td>
                                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ $registration->applicant->nisn }}</td>
                                </tr>
                                <tr class="border-b border-gray-100">
                                    <td class="px-4 py-2.5 text-gray-500">Jenjang</td>
                                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ $registration->registrationPeriod->schoolLevel->name }}</td>
                                </tr>
                                <tr class="border-b border-gray-100">
                                    <td class="px-4 py-2.5 text-gray-500">Periode</td>
                                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ $registration->registrationPeriod->name }}</td>
                                </tr>
                                <tr class="border-b border-gray-100">
                                    <td class="px-4 py-2.5 text-gray-500">Jalur</td>
                                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ $registration->registrationTrack->name }}</td>
                                </tr>
                                <tr class="border-b border-gray-100">
                                    <td class="px-4 py-2.5 text-gray-500">Jenis Biaya</td>
                                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ $payment->payment_type === 'registration_fee' ? 'Biaya Pendaftaran' : 'Biaya Daftar Ulang' }}</td>
                                </tr>
                                <tr class="border-b border-gray-100">
                                    <td class="px-4 py-2.5 text-gray-500">Metode</td>
                                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ $payment->payment_method === 'online' ? 'Online (Xendit)' : ucwords(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2.5 text-gray-500">Jumlah</td>
                                    <td class="px-4 py-2.5 font-bold text-gray-900 text-lg">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if ($canPayOnline)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <p class="text-sm text-yellow-800">Pembayaran online belum selesai. Lanjutkan pembayaran melalui Xendit (Transfer Bank, E-Wallet, Retail Store) atau gunakan transfer manual + unggah bukti.</p>
                            <div class="mt-3 flex flex-wrap gap-3">
                                <a href="{{ $payment->xendit_invoice_url }}" target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 font-medium">
                                    Lanjut Bayar Online (Xendit)
                                </a>
                                <a href="{{ route('registration.show', $registration) }}"
                                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-50 font-medium">
                                    Bayar Manual / Unggah Bukti
                                </a>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-600 mb-6">Invoice ini dibuat otomatis oleh sistem SPMB {{ $school->name }}.</p>
                    @endif

                    @if ($payment->invoice_pdf)
                        <a href="{{ route('payments.invoice', $payment) }}" target="_blank"
                           class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-900 font-medium">
                            Unduh Invoice (PDF)
                        </a>
                    @endif

                    <div class="mt-6 flex justify-between">
                        <a href="{{ route('registration.show', $registration) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 text-sm hover:bg-gray-50">
                            Kembali ke Pendaftaran
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

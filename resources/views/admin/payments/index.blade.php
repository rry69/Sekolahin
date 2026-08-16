@extends('layouts.dashboard')
@section('title', 'Kelola Pembayaran')
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Daftar Pembayaran</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 text-sm {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Semua</a>
                        <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="px-4 py-2 text-sm {{ request('status') === 'pending' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Pending</a>
                        <a href="{{ route('admin.payments.index', ['status' => 'verified']) }}" class="px-4 py-2 text-sm {{ request('status') === 'verified' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Verified</a>
                        <a href="{{ route('admin.payments.index', ['status' => 'rejected']) }}" class="px-4 py-2 text-sm {{ request('status') === 'rejected' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Rejected</a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Registrasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pendaftar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bukti</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($payments as $payment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $payment->registration->registration_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $payment->registration->applicant->full_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($payment->payment_type === 'registration_fee')
                                            Biaya Pendaftaran
                                        @else
                                            Biaya Daftar Ulang
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                                'verified' => 'bg-green-100 text-green-800 border-green-300',
                                                'rejected' => 'bg-red-100 text-red-800 border-red-300',
                                            ];
                                            $statusLabels = ['pending' => 'Pending', 'verified' => 'Lunas', 'rejected' => 'Ditolak'];
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-semibold rounded border {{ $statusColors[$payment->status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                                            {{ $statusLabels[$payment->status] ?? ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($payment->proof_file)
                                            <button type="button"
                                                onclick="showFileModal('{{ asset('storage/' . $payment->proof_file) }}', 'Bukti Pembayaran · {{ $payment->registration->applicant->full_name }}')"
                                                class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                                Lihat Bukti
                                            </button>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($payment->status === 'pending')
                                            <div class="flex gap-2">
                                                <form action="{{ route('admin.payments.verify', $payment) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">Verifikasi</button>
                                                </form>
                                                <button onclick="showRejectModal({{ $payment->id }})" class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">Tolak</button>
                                            </div>
                                        @else
                                            <form action="{{ route('admin.payments.reset', $payment) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Kembalikan status pembayaran ini ke pending?')">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-yellow-500 text-white text-xs rounded hover:bg-yellow-600">Reset</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">Tidak ada data pembayaran</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div id="rejectModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.4);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:#fff;padding:24px;border-radius:10px;width:400px;max-width:90%;">
        <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">Tolak Pembayaran</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;">Alasan Penolakan</label>
                <textarea name="rejection_reason" rows="4" style="width:100%;padding:8px 12px;border:1px solid #e0e0e0;border-radius:6px;font-size:13px;font-family:inherit;" required></textarea>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" onclick="hideRejectModal()" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-danger">Tolak</button>
            </div>
        </form>
    </div>
</div>
@endsection

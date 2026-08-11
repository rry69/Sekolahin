@extends('layouts.dashboard')
@section('title', 'Kelola Pendaftaran')
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
                    <h3 class="text-2xl font-bold text-gray-900">Daftar Pendaftaran</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.registrations.index') }}" class="px-4 py-2 text-sm {{ !request('status') && !request('payment_status') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Semua</a>
                        <a href="{{ route('admin.registrations.index', ['status' => 'pending']) }}" class="px-4 py-2 text-sm {{ request('status') === 'pending' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Pending</a>
                        <a href="{{ route('admin.registrations.index', ['status' => 'verified']) }}" class="px-4 py-2 text-sm {{ request('status') === 'verified' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Terverifikasi</a>
                        <a href="{{ route('admin.registrations.index', ['status' => 'accepted']) }}" class="px-4 py-2 text-sm {{ request('status') === 'accepted' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Diterima</a>
                        <a href="{{ route('admin.registrations.index', ['status' => 'rejected']) }}" class="px-4 py-2 text-sm {{ request('status') === 'rejected' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Ditolak</a>
                        <a href="{{ route('admin.registrations.index', ['status' => 'canceled']) }}" class="px-4 py-2 text-sm {{ request('status') === 'canceled' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Dibatalkan</a>
                        <a href="{{ route('admin.registrations.index', ['payment_status' => 'pending']) }}" class="px-4 py-2 text-sm {{ request('payment_status') === 'pending' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Pembayaran Pending</a>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.registrations.index') }}" class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cari</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NIK / NISN / No. Reg" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Periode</label>
                            <select name="period_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">Semua</option>
                                @foreach($periods as $period)
                                    <option value="{{ $period->id }}" {{ request('period_id') == $period->id ? 'selected' : '' }}>{{ $period->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Jalur</label>
                            <select name="track_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">Semua</option>
                                @foreach($tracks as $track)
                                    <option value="{{ $track->id }}" {{ request('track_id') == $track->id ? 'selected' : '' }}>{{ $track->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Sekolah</label>
                            <select name="school_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">Semua</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Filter</button>
                        </div>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Registrasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Verif. NISN</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Daftar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jalur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jurusan Pilihan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembayaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($registrations as $registration)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $registration->registration_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $registration->applicant->full_name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $vstatus = $registration->applicant->nisn_verification_status ?? null;
                                            $vbadge = ['verified' => 'bg-green-100 text-green-800 border-green-300', 'unavailable' => 'bg-yellow-100 text-yellow-800 border-yellow-300', 'failed' => 'bg-red-100 text-red-800 border-red-300'];
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-semibold rounded border {{ $vbadge[$vstatus] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                                            {{ \App\Services\NisnVerificationService::statusLabel($vstatus ?? '') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $registration->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $registration->registrationTrack->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $registration->major->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                                    'verified' => 'bg-blue-100 text-blue-800 border-blue-300',
                                                    'accepted' => 'bg-green-100 text-green-800 border-green-300',
                                                    'rejected' => 'bg-red-100 text-red-800 border-red-300',
                                                    'canceled' => 'bg-gray-300 text-gray-700 border-gray-400',
                                                ];
                                            @endphp
                                        <span class="px-2 py-1 text-xs font-semibold rounded border {{ $statusColors[$registration->status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                                            {{ ucfirst($registration->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $payColors = [
                                                'paid' => 'bg-green-100 text-green-800 border-green-300',
                                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                                'unpaid' => 'bg-gray-100 text-gray-800 border-gray-300',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-semibold rounded border {{ $payColors[$registration->payment_status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                                            {{ ucfirst($registration->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex gap-2">
                                            <a href="{{ route('admin.registrations.show', $registration) }}" class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">Detail</a>
                                            <form action="{{ route('admin.registrations.delete-account', $registration) }}" method="POST"
                                                  onsubmit="return confirm('Hapus akun siswa {{ $registration->applicant->full_name ?? '' }}? Seluruh data pendaftaran, ujian, dan pembayarannya akan ikut terhapus permanen.')">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">Tidak ada data pendaftaran</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $registrations->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

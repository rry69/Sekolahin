@extends('layouts.dashboard')
@section('title', 'Kelola Daftar Ulang')
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
                    <h3 class="text-2xl font-bold text-gray-900">Daftar Ulang Pendaftar</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.re-registrations.index') }}" class="px-4 py-2 text-sm {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Semua</a>
                        <a href="{{ route('admin.re-registrations.index', ['status' => 'pending']) }}" class="px-4 py-2 text-sm {{ request('status') === 'pending' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Pending</a>
                        <a href="{{ route('admin.re-registrations.index', ['status' => 'completed']) }}" class="px-4 py-2 text-sm {{ request('status') === 'completed' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Completed</a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Registrasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Siswa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uk. Seragam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Submit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($reRegistrations as $reRegistration)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reRegistration->registration->registration_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reRegistration->registration->applicant->full_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ trim(($reRegistration->uniform_shirt_size ?? '-') . ' / ' . ($reRegistration->uniform_pants_size ?? '-'), ' /') ?: '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $reRegistration->submitted_at ? $reRegistration->submitted_at->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                                'completed' => 'bg-green-100 text-green-800 border-green-300',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-semibold rounded border {{ $statusColors[$reRegistration->status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                                            {{ ucfirst($reRegistration->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex gap-2">
                                            <a href="{{ route('admin.re-registrations.show', $reRegistration) }}" class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">Detail</a>
                                            @if ($reRegistration->status === 'pending')
                                                <form action="{{ route('admin.re-registrations.verify', $reRegistration) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">Verifikasi</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data daftar ulang</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $reRegistrations->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

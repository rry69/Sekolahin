@extends('layouts.dashboard')
@section('title', 'Detail Jurusan')
@section('content')
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $major->name }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $major->school->name ?? '-' }} ({{ $major->code }})</p>
                    </div>
                    <a href="{{ route('admin.majors.edit', $major) }}" class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm rounded hover:bg-yellow-200">Edit</a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Sisa Kuota (total)</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['available_quota'] }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Total Pendaftar</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_applicants'] }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Diterima</p>
                        <p class="text-2xl font-bold text-green-600">{{ $statistics['accepted'] }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $statistics['pending'] }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Terverifikasi</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $statistics['verified'] }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Ditolak</p>
                        <p class="text-2xl font-bold text-red-600">{{ $statistics['rejected'] }}</p>
                    </div>
                </div>

                @if(isset($statistics['by_track']))
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Kuota per Jalur</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        @foreach($statistics['by_track'] as $trackName => $row)
                            <div class="border rounded p-3 bg-white">
                                <p class="text-sm font-medium text-gray-700">{{ $trackName }}</p>
                                <p class="text-sm text-gray-600">Kuota {{ $row['quota'] }} · Terisi {{ $row['accepted'] }} · Sisa <span class="font-semibold {{ $row['sisa']===0 ? 'text-red-600' : 'text-green-600' }}">{{ $row['sisa'] }}</span></p>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($major->description)
                <div class="border-b pb-6 mb-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Deskripsi</h4>
                    <p class="text-sm text-gray-700">{{ $major->description }}</p>
                </div>
                @endif

                <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Daftar Pendaftar</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jalur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($registrations as $registration)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $registration->applicant->full_name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $registration->registrationTrack->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ ucfirst($registration->status) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">Belum ada pendaftar</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $registrations->links() }}
                </div>

                <div class="mt-6">
                    <a href="{{ route('admin.majors.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
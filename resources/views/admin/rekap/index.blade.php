@extends('layouts.dashboard')
@section('title', 'Rekap Siswa Diterima')
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
                    <h3 class="text-2xl font-bold text-gray-900">Rekap Siswa Diterima</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.rekap.index', ['major_id' => '']) }}" class="px-4 py-2 text-sm {{ !request('major_id') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Semua</a>
                        @foreach ($majors as $major)
                            <a href="{{ route('admin.rekap.index', ['major_id' => $major->id] + request()->only(['period_id'])) }}" class="px-4 py-2 text-sm {{ request('major_id') == $major->id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">
                                {{ $major->name }} ({{ $statsPerMajor[$major->id] ?? 0 }})
                            </a>
                        @endforeach
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.rekap.index') }}" class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
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
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cari</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NIS / NISN / No. Reg" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jurusan Diterima</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($registrations as $reg)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reg->registration_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reg->applicant->student_number ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $reg->applicant->full_name ?? '-' }}
                                        <div class="text-xs text-gray-500">{{ $reg->applicant->user->email ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reg->finalMajor->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $reg->registrationPeriod->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded border border-green-300 bg-green-100 text-green-800">
                                            {{ ucfirst(str_replace('_', ' ', $reg->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada siswa yang diterima</td>
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

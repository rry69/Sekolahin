@extends('layouts.dashboard')
@section('title', 'Kelola Akun Siswa')
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
                    <h3 class="text-2xl font-bold text-gray-900">Daftar Akun Siswa</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.accounts.index') }}" class="px-4 py-2 text-sm {{ !request('registration_status') && !request('major_id') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Semua</a>
                        <a href="{{ route('admin.accounts.index', ['registration_status' => 'pending']) }}" class="px-4 py-2 text-sm {{ request('registration_status') === 'pending' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Pending</a>
                        <a href="{{ route('admin.accounts.index', ['registration_status' => 'verified']) }}" class="px-4 py-2 text-sm {{ request('registration_status') === 'verified' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Terverifikasi</a>
                        <a href="{{ route('admin.accounts.index', ['registration_status' => 'accepted']) }}" class="px-4 py-2 text-sm {{ request('registration_status') === 'accepted' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Diterima</a>
                        <a href="{{ route('admin.accounts.index', ['registration_status' => 'rejected']) }}" class="px-4 py-2 text-sm {{ request('registration_status') === 'rejected' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded">Ditolak</a>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.accounts.index') }}" class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cari</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / Email / NIK / NISN" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Status Pendaftaran</label>
                            <select name="registration_status" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">Semua</option>
                                <option value="pending" {{ request('registration_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="verified" {{ request('registration_status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                                <option value="accepted" {{ request('registration_status') == 'accepted' ? 'selected' : '' }}>Diterima</option>
                                <option value="rejected" {{ request('registration_status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Jurusan</label>
                            <select name="major_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">Semua</option>
                                @foreach($majors as $major)
                                    <option value="{{ $major->id }}" {{ request('major_id') == $major->id ? 'selected' : '' }}>{{ $major->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Filter</button>
                            <a href="{{ route('admin.accounts.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIK / NISN</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Pendaftaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terdaftar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($accounts as $account)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $account->applicant->full_name ?? $account->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $account->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div>{{ $account->applicant->nik ?? '-' }}</div>
                                        <div class="text-xs text-gray-400">NISN: {{ $account->applicant->nisn ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">{{ $account->applicant->registrations_count ?? 0 }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $account->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <form action="{{ route('admin.accounts.destroy', $account) }}" method="POST"
                                              onsubmit="return confirm('Hapus akun siswa {{ $account->applicant->full_name ?? $account->name }}? Seluruh data pendaftaran, ujian, dan pembayarannya akan ikut terhapus permanen.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">Hapus Akun</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada akun siswa</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $accounts->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

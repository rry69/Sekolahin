@extends('layouts.dashboard')
@section('title', 'Kelola Sekolah')
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

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-900">Data Sekolah</h3>
            <a href="{{ route('admin.schools.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Tambah Sekolah
            </a>
        </div>

        @forelse ($levels as $level)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-bold text-gray-900">Jenjang {{ $level->name }}
                            <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded {{ $level->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500' }}">
                                {{ $level->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </h4>
                    </div>

                    @php $levelSchools = $grouped->get($level->id, collect()); @endphp
                    @if ($levelSchools->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Sekolah</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kepala Sekolah</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenjang</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jurusan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($levelSchools as $entry)
                                        @php $school = $entry['school']; @endphp
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $school->name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ $school->address ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $school->principal_name ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $school->levelsName() }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-blue-100 text-blue-800">{{ $school->majors_count }} jurusan</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <div class="flex gap-2">
                                                    <a href="{{ route('admin.schools.edit', $school) }}" class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs rounded hover:bg-yellow-200">Edit</a>
                                                    <form method="POST" action="{{ route('admin.schools.destroy', $school) }}" onsubmit="return confirm('Hapus sekolah ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-3 py-1 bg-red-100 text-red-800 text-xs rounded hover:bg-red-200">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Belum ada sekolah untuk jenjang ini.</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center text-gray-500">Tidak ada data jenjang</div>
            </div>
        @endforelse

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Status Pendaftaran per Jenjang</h3>
                <p class="text-sm text-gray-500 mb-4">Matikan jenjang yang tidak menerima pendaftaran. Jenjang yang nonaktif tidak akan muncul di form pendaftaran siswa.</p>

                <form action="{{ route('admin.schools.levels.update') }}" method="POST">
                    @csrf
                    <div class="space-y-3">
                        @foreach ($levels as $level)
                            <div class="flex items-center justify-between border rounded-lg px-4 py-3">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $level->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $level->description }}</p>
                                </div>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_active[{{ $level->id }}]" value="1"
                                        {{ $level->is_active ? 'checked' : '' }}
                                        class="h-4 w-4 text-green-600 focus:ring-green-500 rounded">
                                    <span class="text-sm font-medium {{ $level->is_active ? 'text-green-600' : 'text-red-500' }}">
                                        {{ $level->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 mt-6">Simpan Status Pendaftaran</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

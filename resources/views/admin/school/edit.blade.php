@extends('layouts.dashboard')
@section('title', 'Kelola Sekolah')
@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Data Sekolah</h3>
                <p class="text-sm text-gray-500 mb-6">Kelola profil sekolah yang ditampilkan pada form pendaftaran siswa.</p>

                <form action="{{ route('admin.school.update') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Sekolah</label>
                            <input type="text" name="name" value="{{ old('name', $school->name ?? '') }}" required class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                            <textarea name="address" rows="2" class="w-full border-gray-300 rounded-md shadow-sm">{{ old('address', $school->address ?? '') }}</textarea>
                            @error('address')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $school->phone ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('phone')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', $school->email ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kepala Sekolah</label>
                            <input type="text" name="principal_name" value="{{ old('principal_name', $school->principal_name ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('principal_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="border-t pt-6 mt-6">
                        <h4 class="text-lg font-bold text-gray-900 mb-2">Jenjang yang Dilayani</h4>
                        <p class="text-sm text-gray-500 mb-4">Centang jenjang pendidikan yang menerima pendaftaran di sekolah ini.</p>

                        <div class="flex flex-wrap gap-4">
                            @foreach ($levels as $level)
                                <label class="flex items-center gap-2 border rounded-lg px-4 py-2 cursor-pointer hover:bg-gray-50">
                                    <input type="checkbox" name="school_level_ids[]" value="{{ $level->id }}"
                                        {{ in_array($level->id, $school?->schoolLevels?->pluck('id')->all() ?? []) ? 'checked' : '' }}
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 rounded">
                                    <span class="text-sm font-medium text-gray-800">{{ $level->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 mt-6">Simpan Data Sekolah</button>
                </form>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
            <div class="p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Status Pendaftaran per Jenjang</h3>
                <p class="text-sm text-gray-500 mb-6">Matikan jenjang yang tidak menerima pendaftaran. Jenjang yang nonaktif tidak akan muncul di form pendaftaran siswa.</p>

                <form action="{{ route('admin.school.levels.update') }}" method="POST">
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

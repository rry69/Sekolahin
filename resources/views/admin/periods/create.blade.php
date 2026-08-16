@extends('layouts.dashboard')
@section('title', 'Tambah Periode Pendaftaran')
@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Tambah Periode Pendaftaran</h3>

                <form action="{{ route('admin.periods.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenjang</label>
                        <select name="school_level_id" required class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Pilih Jenjang</option>
                            @foreach ($schoolLevels as $level)
                                <option value="{{ $level->id }}" {{ old('school_level_id') == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                            @endforeach
                        </select>
                        @error('school_level_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Periode</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: SPMB 2026/2027 Gelombang 1" class="w-full border-gray-300 rounded-md shadow-sm">
                        @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                            <x-date-picker name="start_date" :required="true" label="Tanggal Mulai" />
                            @error('start_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                            <x-date-picker name="end_date" :required="true" label="Tanggal Selesai" />
                            @error('end_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Maksimal Pendaftar (opsional)</label>
                        <input type="number" name="max_applicants" value="{{ old('max_applicants') }}" min="1" class="w-full border-gray-300 rounded-md shadow-sm">
                        @error('max_applicants')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-6">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                            <span class="text-sm text-gray-700">Jadikan periode aktif</span>
                        </label>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Simpan</button>
                        <a href="{{ route('admin.periods.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

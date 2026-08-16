@extends('layouts.dashboard')
@section('title', 'Tambah Jurusan')
@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Tambah Jurusan</h3>

                <form action="{{ route('admin.majors.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sekolah</label>
                        <select name="school_id" required class="w-full border-gray-300 rounded-md shadow-sm">
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                            @endforeach
                        </select>
                        @error('school_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Jurusan <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Rekayasa Perangkat Lunak" class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kode <span class="text-red-500">*</span></label>
                            <input type="text" name="code" value="{{ old('code') }}" required placeholder="e.g. RPL" class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('code')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kuota (opsional, untuk kompatibilitas)</label>
                        <input type="number" name="quota" value="{{ old('quota') }}" min="0" placeholder="Kosongkan jika tidak ada kuota" class="w-full border-gray-300 rounded-md shadow-sm">
                        <p class="text-xs text-gray-500 mt-1">Jika kosong, kuota diambil dari rincian per jalur di bawah.</p>
                        @error('quota')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4 border rounded p-4 bg-gray-50">
                        <p class="text-sm font-medium text-gray-700 mb-3">Kuota per Jalur <span class="text-gray-400 font-normal">(revisi.md: jalur tidak saling mempengaruhi)</span></p>
                        <p class="text-xs text-gray-500 mb-3">TKJ/RPL: Reguler 40, Prestasi 20, Beasiswa 12 · MM/TEI: 20, 10, 6 — kosongkan untuk auto-isi sesuai kode jurusan.</p>
                        <div class="grid grid-cols-3 gap-4">
                            @foreach($tracks as $t)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t->name }}</label>
                                    <input type="number" name="quota_track_{{ $t->id }}" value="{{ old('quota_track_'.$t->id) }}" min="0" placeholder="mis. {{ $t->name==='Reguler' ? '40' : ($t->name==='Prestasi' ? '20' : '12') }}" class="w-full border-gray-300 rounded-md shadow-sm">
                                    @error('quota_track_'.$t->id)<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="description" rows="3" class="w-full border-gray-300 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="requires_health_test" value="1" class="rounded border-gray-300"> Tes Kesehatan
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="requires_interview" value="1" class="rounded border-gray-300"> Tes Wawancara
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="requires_skill_test" value="1" class="rounded border-gray-300"> Tes Keterampilan
                        </label>
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('admin.majors.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Tambah Jurusan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
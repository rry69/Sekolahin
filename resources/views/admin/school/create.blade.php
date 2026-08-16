@extends('layouts.dashboard')
@section('title', 'Tambah Sekolah')
@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Tambah Sekolah</h3>

                <form action="{{ route('admin.schools.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Sekolah <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                            <textarea name="address" rows="2" class="w-full border-gray-300 rounded-md shadow-sm">{{ old('address') }}</textarea>
                            @error('address')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('phone')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kepala Sekolah</label>
                            <input type="text" name="principal_name" value="{{ old('principal_name') }}" class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('principal_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="border-t pt-6 mt-6">
                        <h4 class="text-lg font-bold text-gray-900 mb-2">Jenjang yang Dilayani <span class="text-red-500">*</span></h4>
                        <p class="text-sm text-gray-500 mb-4">Centang jenjang pendidikan yang menerima pendaftaran di sekolah ini.</p>

                        <div class="flex flex-wrap gap-4">
                            @foreach ($levels as $level)
                                <label class="flex items-center gap-2 border rounded-lg px-4 py-2 cursor-pointer hover:bg-gray-50">
                                    <input type="checkbox" name="school_level_ids[]" value="{{ $level->id }}"
                                        {{ in_array($level->id, old('school_level_ids', [])) ? 'checked' : '' }}
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 rounded">
                                    <span class="text-sm font-medium text-gray-800">{{ $level->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('school_level_ids')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex justify-between mt-6">
                        <a href="{{ route('admin.schools.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Batal</a>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Simpan Sekolah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

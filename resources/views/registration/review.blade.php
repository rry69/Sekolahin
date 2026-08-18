<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Review Pendaftaran
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-help-steps title="Cek sebelum daftar" icon="fa-eye" :steps="[
                        'Pastikan jenjang, periode, jalur, dan jurusan sudah sesuai.',
                        'Jika ada yang salah, klik <strong>Kembali</strong>.',
                        'Klik <strong>Konfirmasi &amp; Daftar</strong> untuk menerbitkan nomor pendaftaran — batas waktu akan mulai berjalan.',
                    ]" />
                    <h3 class="text-lg font-semibold mb-4">Periksa kembali data pendaftaran Anda sebelum mengonfirmasi.</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Data Diri</h4>
                            <div class="space-y-2">
                                <p><span class="font-medium">Nama:</span> {{ $applicant->full_name }}</p>
                                <p><span class="font-medium">NIK:</span> {{ $applicant->nik }}</p>
                                <p><span class="font-medium">NISN:</span> {{ $applicant->nisn ?? '-' }}</p>
                                <p><span class="font-medium">Tempat, Tanggal Lahir:</span> {{ $applicant->birth_place }}, {{ $applicant->birth_date?->format('d M Y') }}</p>
                                <p><span class="font-medium">Jenis Kelamin:</span> {{ $applicant->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                                <p><span class="font-medium">Agama:</span> {{ $applicant->religion }}</p>
                                <p><span class="font-medium">Telepon:</span> {{ $applicant->phone }}</p>
                                <p><span class="font-medium">Sekolah Asal:</span> {{ $applicant->previous_school }}</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Pilihan Pendaftaran</h4>
                            <div class="space-y-2">
                                <p><span class="font-medium">Jenjang:</span> {{ $period->schoolLevel->name }}</p>
                                <p><span class="font-medium">Periode:</span> {{ $period->name }}</p>
                                <p><span class="font-medium">Jalur:</span> {{ $track->name }}</p>
                                <p><span class="font-medium">Sekolah:</span> {{ $school->name }}</p>
                                @if($major)
                                <p><span class="font-medium">Jurusan Pilihan:</span> {{ $major->name }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <p class="text-sm text-gray-500 mb-4">Pastikan semua data di atas sudah benar.</p>
                        <div class="flex justify-between">
                            <a href="{{ route('registration.create') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Kembali
                            </a>
                            <form method="POST" action="{{ route('registration.confirm') }}">
                                @csrf
                                <input type="hidden" name="registration_period_id" value="{{ $validated['registration_period_id'] }}">
                                <input type="hidden" name="registration_track_id" value="{{ $validated['registration_track_id'] }}">
                                <input type="hidden" name="major_id" value="{{ $validated['major_id'] ?? '' }}">
                                <input type="hidden" name="school_id" value="{{ $validated['school_id'] }}">
                                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                                    Konfirmasi & Daftar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

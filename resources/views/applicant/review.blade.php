<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Review Data Diri
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
                    <h3 class="text-lg font-semibold mb-4">Periksa kembali data diri Anda sebelum menyimpan.</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Data Pribadi</h4>
                            <div class="space-y-2">
                                <p><span class="font-medium">Nama Lengkap:</span> {{ $data['full_name'] }}</p>
                                <p><span class="font-medium">NISN:</span> {{ $data['nisn'] ?? '-' }}</p>
                                <p><span class="font-medium">Verifikasi NISN:</span>
                                    @if (($data['nisn_verification_status'] ?? null) === 'verified')
                                        <span class="text-green-600 font-medium">✓ Terverifikasi</span>
                                    @elseif (($data['nisn_verification_status'] ?? null) === 'unavailable')
                                        <span class="text-yellow-600 font-medium">Menunggu verifikasi (server NISN tidak dapat diakses)</span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </p>
                                <p><span class="font-medium">NIK:</span> {{ $data['nik'] }}</p>
                                <p><span class="font-medium">Tempat Lahir:</span> {{ $data['birth_place'] }}</p>
                                <p><span class="font-medium">Tanggal Lahir:</span> {{ \Carbon\Carbon::parse($data['birth_date'])->format('d M Y') }}</p>
                                <p><span class="font-medium">Jenis Kelamin:</span> {{ $data['gender'] === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                                <p><span class="font-medium">Agama:</span> {{ $data['religion'] }}</p>
                                <p><span class="font-medium">Nomor Telepon:</span> {{ $data['phone'] }}</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Alamat</h4>
                            <div class="space-y-2">
                                <p><span class="font-medium">Alamat:</span> {{ $data['address'] }}</p>
                                <p><span class="font-medium">RT/RW:</span> {{ $data['rt'] ?? '-' }} / {{ $data['rw'] ?? '-' }}</p>
                                <p><span class="font-medium">Kelurahan:</span> {{ $data['village'] ?? '-' }}</p>
                                <p><span class="font-medium">Kecamatan:</span> {{ $data['district'] ?? '-' }}</p>
                                <p><span class="font-medium">Kab/Kota:</span> {{ $data['city'] ?? '-' }}</p>
                                <p><span class="font-medium">Provinsi:</span> {{ $data['province'] ?? '-' }}</p>
                                <p><span class="font-medium">Kode Pos:</span> {{ $data['postal_code'] ?? '-' }}</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Orang Tua / Wali</h4>
                            <div class="space-y-2">
                                <p><span class="font-medium">Nama Ayah:</span> {{ $data['father_name'] }}</p>
                                <p><span class="font-medium">Pekerjaan Ayah:</span> {{ $data['father_occupation'] ?? '-' }}</p>
                                <p><span class="font-medium">Nama Ibu:</span> {{ $data['mother_name'] }}</p>
                                <p><span class="font-medium">Pekerjaan Ibu:</span> {{ $data['mother_occupation'] ?? '-' }}</p>
                                <p><span class="font-medium">Nama Wali:</span> {{ $data['parent_name'] ?? '-' }}</p>
                                <p><span class="font-medium">HP Orang Tua/Wali:</span> {{ $data['parent_phone'] ?? '-' }}</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Pendidikan</h4>
                            <div class="space-y-2">
                                <p><span class="font-medium">Sekolah Asal:</span> {{ $data['previous_school'] }}</p>
                                <p><span class="font-medium">Tahun Lulus:</span> {{ $data['graduation_year'] ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <p class="text-sm text-gray-500 mb-4">Pastikan semua data di atas sudah benar.</p>
                        <div class="flex justify-between">
                            <a href="{{ route('applicant.profile') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Kembali
                            </a>
                            <form method="POST" action="{{ route('applicant.profile.confirm') }}">
                                @csrf
                                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                                    Konfirmasi & Simpan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

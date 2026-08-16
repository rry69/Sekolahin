@extends('layouts.dashboard')
@section('title', 'Detail Daftar Ulang')
@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Detail Daftar Ulang</h3>
                        <p class="text-sm text-gray-600 mt-1">No. Registrasi: {{ $reRegistration->registration->registration_number }}</p>
                    </div>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                            'completed' => 'bg-green-100 text-green-800 border-green-300',
                        ];
                    @endphp
                    <span class="px-3 py-1 text-sm font-semibold rounded border {{ $statusColors[$reRegistration->status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                        {{ ucfirst($reRegistration->status) }}
                    </span>
                </div>

                <div class="border-b pb-6 mb-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Informasi Pendaftar</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Nama Lengkap</p>
                            <p class="font-medium text-gray-900">{{ $reRegistration->registration->applicant->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-medium text-gray-900">{{ $reRegistration->registration->applicant->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Jenjang</p>
                            <p class="font-medium text-gray-900">{{ $reRegistration->registration->registrationPeriod->schoolLevel->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Jalur Pendaftaran</p>
                            <p class="font-medium text-gray-900">{{ $reRegistration->registration->registrationTrack->name }}</p>
                        </div>
                    </div>
                </div>

                <div class="border-b pb-6 mb-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Data Seragam &amp; Fisik</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Ukuran Baju</p>
                            <p class="font-medium text-gray-900">{{ $reRegistration->uniform_shirt_size ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Ukuran Celana</p>
                            <p class="font-medium text-gray-900">{{ $reRegistration->uniform_pants_size ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Golongan Darah</p>
                            <p class="font-medium text-gray-900">{{ $reRegistration->blood_type ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tinggi Badan</p>
                            <p class="font-medium text-gray-900">{{ $reRegistration->height_cm ? $reRegistration->height_cm . ' cm' : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Berat Badan</p>
                            <p class="font-medium text-gray-900">{{ $reRegistration->weight_kg ? $reRegistration->weight_kg . ' kg' : '-' }}</p>
                        </div>
                    </div>
                </div>

                @if($reRegistration->verification_code)
                <div class="border-b pb-6 mb-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Kode Verifikasi</h4>
                    <p class="font-mono font-bold tracking-widest text-lg text-gray-900">{{ $reRegistration->verification_code }}</p>
                    <p class="text-xs text-gray-500 mt-1">Kode pada kartu daftar ulang — verifikasi di dashboard admin.</p>
                </div>
                @endif

                <div class="border-b pb-6 mb-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Status Verifikasi</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Tanggal Submit</p>
                            <p class="font-medium text-gray-900">{{ $reRegistration->submitted_at ? $reRegistration->submitted_at->format('d M Y H:i') : '-' }}</p>
                        </div>
                        @if ($reRegistration->verified_at)
                            <div>
                                <p class="text-sm text-gray-600">Tanggal Verifikasi</p>
                                <p class="font-medium text-gray-900">{{ $reRegistration->verified_at->format('d M Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Diverifikasi Oleh</p>
                                <p class="font-medium text-gray-900">{{ $reRegistration->verifier->name ?? '-' }}</p>
                            </div>
                        @endif
                        @if ($reRegistration->notes)
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-600">Catatan</p>
                                <p class="font-medium text-gray-900">{{ $reRegistration->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <a href="{{ route('admin.re-registrations.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Kembali</a>
                    @if ($reRegistration->status === 'pending')
                        <form action="{{ route('admin.re-registrations.verify', $reRegistration) }}" method="POST" onsubmit="return confirm('Yakin ingin verifikasi daftar ulang ini?')">
                            @csrf
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">Verifikasi Daftar Ulang</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

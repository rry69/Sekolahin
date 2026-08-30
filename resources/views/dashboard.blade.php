<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-center py-8">
                        <div class="mx-auto w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                            <x-hi icon="fa-graduation-cap" class="text-2xl" />
                        </div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Selamat datang, {{ Auth::user()->name }}!</h3>
                        <p class="mt-2 text-sm text-gray-500 max-w-xl mx-auto">
                            Ini adalah Sistem Penerimaan Mahasiswa Baru (SPMB). Silakan lengkapi biodata dan daftar
                            untuk memulai pendaftaran Anda.
                        </p>
                        <div class="mt-6 flex flex-wrap justify-center gap-3">
                            <x-app-button variant="primary" :href="route('registration.index')">
                                <x-hi icon="fa-folder-open" /> Lihat Pendaftaran
                            </x-app-button>
                            <x-app-button variant="secondary" :href="route('applicant.profile')">
                                <x-hi icon="fa-id-card" /> Biodata Saya
                            </x-app-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

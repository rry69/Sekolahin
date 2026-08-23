<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('registration.index') }}" class="inline-flex items-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100" aria-label="Kembali ke Pendaftaran">
                <i class="fa-solid fa-arrow-left text-lg"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Profile') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('profile.partials.profile-content')
        </div>
    </div>
</x-app-layout>

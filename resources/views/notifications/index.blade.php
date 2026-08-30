<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('registration.index') }}" class="inline-flex items-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100" aria-label="Kembali ke Pendaftaran">
                    <x-hi icon="fa-arrow-left" class="text-lg" />
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Notifikasi
                </h2>
            </div>
            @if (auth()->user()->unreadNotifications->count() > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <x-app-button variant="primary" size="sm" type="submit">
                        <x-hi icon="fa-check-double" /> Tandai Semua Dibaca
                    </x-app-button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($notifications->isEmpty())
                        <div class="text-center py-12">
                            <div class="mx-auto w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                <x-hi icon="fa-bell" class="text-2xl" />
                            </div>
                            <p class="mt-4 text-gray-500">Belum ada notifikasi.</p>
                            <p class="text-sm text-gray-400 mt-1">Pemberitahuan perubahan status pendaftaran akan muncul di sini.</p>
                        </div>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach ($notifications as $notif)
                                @php
                                    $data = $notif->data;
                                    $url = $data['url'] ?? null;
                                    $message = $data['message'] ?? 'Perubahan status pendaftaran';
                                    $regNumber = $data['registration_number'] ?? null;
                                @endphp
                                <li class="py-4 {{ $notif->read_at ? 'opacity-60' : '' }}">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ route('notifications.open', $notif->id) }}" class="block hover:text-indigo-600">
                                                <p class="text-sm text-gray-800 {{ $notif->read_at ? '' : 'font-medium' }}">
                                                    @if (!$notif->read_at)
                                                        <span class="inline-block w-2 h-2 rounded-full bg-indigo-500 mr-1.5 align-middle"></span>
                                                    @endif
                                                    {{ $message }}
                                                </p>
                                                @if ($regNumber)
                                                    <p class="text-xs text-gray-500 mt-1 font-mono">{{ $regNumber }}</p>
                                                @endif
                                                <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                            </a>
                                        </div>
                                        @if (!$notif->read_at)
                                            <form method="POST" action="{{ route('notifications.read', $notif->id) }}" class="shrink-0">
                                                @csrf
                                                <button type="submit" class="text-xs text-indigo-600 hover:underline">Tandai dibaca</button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-6">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

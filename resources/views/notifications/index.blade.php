<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Notifikasi
            </h2>
            @if (auth()->user()->unreadNotifications->count() > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm">
                        Tandai Semua Dibaca
                    </button>
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
                        <p class="text-gray-500 text-center py-8">Belum ada notifikasi.</p>
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
                                        <div class="min-w-0">
                                            @if ($url)
                                                <a href="{{ $url }}" class="block hover:text-indigo-600">
                                                    <p class="text-sm text-gray-800 {{ $notif->read_at ? '' : 'font-medium' }}">{{ $message }}</p>
                                                    @if ($regNumber)
                                                        <p class="text-xs text-gray-500 mt-1">{{ $regNumber }}</p>
                                                    @endif
                                                </a>
                                            @else
                                                <p class="text-sm text-gray-800 {{ $notif->read_at ? '' : 'font-medium' }}">{{ $message }}</p>
                                                @if ($regNumber)
                                                    <p class="text-xs text-gray-500 mt-1">{{ $regNumber }}</p>
                                                @endif
                                            @endif
                                            <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
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

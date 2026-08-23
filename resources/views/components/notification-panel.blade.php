{{--
    Panel notifikasi ala Facebook: klik lonceng → dropdown muncul, tetap di halaman.
    Tidak membuat route/halaman baru — memakai rute notifikasi yang sudah ada:
      notifications.read, notifications.read-all, notifications.open.
    Butuh resources/js/notification-panel.js (global window.notificationPanel).
--}}
@php
    $unreadCount = auth()->user()->unreadNotifications->count();
    $recentNotifications = auth()->user()->notifications()->latest()->take(8)->get()->map(fn ($n) => [
        'id' => $n->id,
        'message' => \Illuminate\Support\Str::limit(data_get($n->data, 'message', 'Perubahan status pendaftaran'), 90),
        'url' => data_get($n->data, 'url'),
        'registration_number' => data_get($n->data, 'registration_number'),
        'read_at' => $n->read_at,
        'time' => $n->created_at->diffForHumans(),
    ])->values();
@endphp

<div
    x-data="notificationPanel({
        open: false,
        unread: {{ $unreadCount }},
        notifications: @js($recentNotifications),
        readUrl: @js(route('notifications.read', ':id')),
        markAllUrl: @js(route('notifications.read-all')),
        openUrl: @js(route('notifications.open', ':id')),
    })"
    @keydown.escape.window="close()"
    class="relative"
>
    <!-- Tombol lonceng -->
    <button type="button" @click="toggle()" aria-haspopup="true" :aria-expanded="open"
        class="relative inline-flex items-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition ease-in-out duration-150"
        aria-label="Notifikasi">
        <i class="fa-solid fa-bell text-lg"></i>
        <span x-show="unread > 0" x-cloak
            class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full min-w-[1.25rem] text-center">
            <span x-text="unread > 9 ? '9+' : unread"></span>
        </span>
    </button>

    <!-- Panel dropdown -->
    <div x-show="open" x-cloak
        x-transition:enter="transition ease-out duration-100 origin-top-right"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.outside="close()"
        role="menu" aria-label="Notifikasi"
        class="absolute right-0 top-full mt-2 w-80 sm:w-96 max-w-[92vw] bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden">
        <!-- Header panel -->
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-gray-50">
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-gray-900">Notifikasi</span>
                <span x-show="unread > 0" x-cloak
                    class="inline-flex items-center justify-center px-1.5 py-0.5 text-[11px] font-bold text-white bg-indigo-600 rounded-full min-w-[1.25rem] text-center">
                    <span x-text="unread > 9 ? '9+' : unread"></span>
                </span>
            </div>
            <div class="flex items-center gap-1">
                <button type="button" @click="markAllRead()" x-show="unread > 0" x-cloak
                    class="text-xs text-indigo-600 hover:text-indigo-800 hover:underline px-2 py-1 rounded">
                    Tandai semua dibaca
                </button>
                <button type="button" @click="close()" aria-label="Tutup"
                    class="inline-flex items-center justify-center w-7 h-7 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-200 transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- Daftar notifikasi -->
        <div class="max-h-96 overflow-y-auto divide-y divide-gray-100">
            <template x-if="notifications.length === 0">
                <div class="px-4 py-10 text-center">
                    <div class="mx-auto w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                        <i class="fa-regular fa-bell text-xl"></i>
                    </div>
                    <p class="mt-3 text-sm text-gray-500">Belum ada notifikasi.</p>
                </div>
            </template>

            <template x-for="notif in notifications" :key="notif.id">
                <div class="relative group hover:bg-gray-50 transition">
                    <a :href="notif.url ? (openUrl.replace(':id', notif.id)) : '#'"
                        class="flex items-start gap-3 px-4 py-3"
                        :class="notif.read_at ? 'opacity-70' : 'bg-indigo-50/60'">
                        <span class="mt-1.5 shrink-0 w-2 h-2 rounded-full"
                            :class="notif.read_at ? 'bg-transparent' : 'bg-indigo-500'"></span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm text-gray-800 leading-snug"
                                :class="notif.read_at ? 'font-normal' : 'font-medium'"
                                x-text="notif.message"></span>
                            <span class="block text-xs text-gray-400 mt-1" x-text="notif.time"></span>
                        </span>
                    </a>
                    <!-- Aksi tandai dibaca (muncul saat hover) -->
                    <button type="button"
                        @click="markRead(notif.id)"
                        x-show="!notif.read_at"
                        x-cloak
                        class="absolute right-2 top-1/2 -translate-y-1/2 inline-flex items-center gap-1 text-[11px] text-indigo-600 hover:text-indigo-800 bg-white border border-gray-200 rounded-md px-1.5 py-0.5 shadow-sm opacity-0 group-hover:opacity-100 focus:opacity-100 transition"
                        title="Tandai dibaca">
                        <i class="fa-solid fa-check"></i> Dibaca
                    </button>
                </div>
            </template>
        </div>

        <!-- Footer: lihat semua -->
        <div class="border-t border-gray-100">
            <a href="{{ route('notifications.index') }}"
                class="flex items-center justify-center gap-2 px-4 py-2.5 text-sm text-indigo-600 hover:bg-indigo-50 transition">
                Lihat Semua Notifikasi
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</div>

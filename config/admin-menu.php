<?php

/*
|--------------------------------------------------------------------------
| Menu Sidebar Admin
|--------------------------------------------------------------------------
|
| Struktur menu sidebar Dashboard Admin, dikelompokkan secara fungsional.
| Setiap grup collapsible berisi daftar item menu. Tambahkan menu baru
| cukup dengan menambahkan item ke dalam array di bawah.
|
| item:  label    -> teks yang ditampilkan
|        icon     -> class Font Awesome (fallback)
|        svg      -> path SVG (stroke-based, 24x24, viewBox=0 0 24 24)
|        route    -> nama route (route())
|        routeIs  -> pola request()->routeIs() untuk state aktif
|
*/

return [

    'top' => [
        [
            'label'   => 'Dashboard',
            'icon'    => 'fa-solid fa-grip',
            'svg'     => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>',
            'route'   => 'admin.dashboard',
            'routeIs' => 'admin.dashboard',
        ],
    ],

    'groups' => [

        'PENDAFTARAN' => [
            'icon'   => 'fa-solid fa-folder-open',
            'svg'    => '<path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/>',
            'items'  => [
                [
                    'label'   => 'Data Pendaftar',
                    'icon'    => 'fa-solid fa-users',
                    'svg'     => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
                    'route'   => 'admin.registrations.index',
                    'routeIs' => 'admin.registrations.*',
                ],
                [
                    'label'   => 'Daftar Ulang',
                    'icon'    => 'fa-solid fa-user-check',
                    'svg'     => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/>',
                    'route'   => 'admin.re-registrations.index',
                    'routeIs' => 'admin.re-registrations.*',
                ],
                [
                    'label'   => 'Pembayaran',
                    'icon'    => 'fa-solid fa-money-check-dollar',
                    'svg'     => '<rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>',
                    'route'   => 'admin.payments.index',
                    'routeIs' => 'admin.payments.*',
                ],
                [
                    'label'   => 'Rekap Diterima',
                    'icon'    => 'fa-solid fa-clipboard-check',
                    'svg'     => '<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/>',
                    'route'   => 'admin.rekap.index',
                    'routeIs' => 'admin.rekap.*',
                ],
                [
                    'label'   => 'Pengaturan Jalur',
                    'icon'    => 'fa-solid fa-route',
                    'svg'     => '<circle cx="6" cy="19" r="3"/><path d="M9 19h8.5a3.5 3.5 0 0 0 0-7h-11a3.5 3.5 0 0 1 0-7H15"/><circle cx="18" cy="5" r="3"/>',
                    'route'   => 'admin.tracks.index',
                    'routeIs' => 'admin.tracks.*',
                ],
            ],
        ],

        'DATA MASTER' => [
            'icon'   => 'fa-solid fa-database',
            'svg'    => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/>',
            'items'  => [
                [
                    'label'   => 'Sekolah',
                    'icon'    => 'fa-solid fa-school',
                    'svg'     => '<path d="m22 9-10-5L2 9"/><path d="M6 11v6a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-6"/><path d="M12 7v.01"/><path d="M12 11v4"/>',
                    'route'   => 'admin.schools.index',
                    'routeIs' => 'admin.schools.*',
                ],
                [
                    'label'   => 'Jurusan',
                    'icon'    => 'fa-solid fa-graduation-cap',
                    'svg'     => '<path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12.5V16c0 1.1 2.7 2.5 6 2.5s6-1.4 6-2.5v-3.5"/>',
                    'route'   => 'admin.majors.index',
                    'routeIs' => 'admin.majors.*',
                ],
                [
                    'label'   => 'Periode Pendaftaran',
                    'icon'    => 'fa-solid fa-calendar-day',
                    'svg'     => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
                    'route'   => 'admin.periods.index',
                    'routeIs' => 'admin.periods.*',
                ],
            ],
        ],

        'PENGGUNA & SISTEM' => [
            'icon'   => 'fa-solid fa-user-gear',
            'svg'    => '<path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/>',
            'items'  => [
                [
                    'label'   => 'Akun Siswa',
                    'icon'    => 'fa-solid fa-user-graduate',
                    'svg'     => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>',
                    'route'   => 'admin.accounts.index',
                    'routeIs' => 'admin.accounts.*',
                ],
                [
                    'label'   => 'Log Aktivitas',
                    'icon'    => 'fa-solid fa-clock-rotate-left',
                    'svg'     => '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/>',
                    'route'   => 'admin.activity-logs.index',
                    'routeIs' => 'admin.activity-logs.*',
                ],
                [
                    'label'   => 'Pengaturan',
                    'icon'    => 'fa-solid fa-gear',
                    'svg'     => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/>',
                    'route'   => 'admin.settings.edit',
                    'routeIs' => 'admin.settings.*',
                ],
            ],
        ],
    ],
];

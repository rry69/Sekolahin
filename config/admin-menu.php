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
|        icon     -> class Font Awesome
|        route    -> nama route (route())
|        routeIs  -> pola request()->routeIs() untuk state aktif
|
*/

return [

    'top' => [
        [
            'label'   => 'Dashboard',
            'icon'    => 'fa-solid fa-grip',
            'route'   => 'admin.dashboard',
            'routeIs' => 'admin.dashboard',
        ],
    ],

    'groups' => [

        'PENDAFTARAN' => [
            'icon'   => 'fa-solid fa-folder-open',
            'items'  => [
                [
                    'label'   => 'Data Pendaftar',
                    'icon'    => 'fa-solid fa-users',
                    'route'   => 'admin.registrations.index',
                    'routeIs' => 'admin.registrations.*',
                ],
                [
                    'label'   => 'Daftar Ulang',
                    'icon'    => 'fa-solid fa-user-check',
                    'route'   => 'admin.re-registrations.index',
                    'routeIs' => 'admin.re-registrations.*',
                ],
                [
                    'label'   => 'Pembayaran',
                    'icon'    => 'fa-solid fa-money-check-dollar',
                    'route'   => 'admin.payments.index',
                    'routeIs' => 'admin.payments.*',
                ],
                [
                    'label'   => 'Rekap Diterima',
                    'icon'    => 'fa-solid fa-clipboard-check',
                    'route'   => 'admin.rekap.index',
                    'routeIs' => 'admin.rekap.*',
                ],
                [
                    'label'   => 'Pengaturan Jalur',
                    'icon'    => 'fa-solid fa-route',
                    'route'   => 'admin.tracks.index',
                    'routeIs' => 'admin.tracks.*',
                ],
            ],
        ],

        'DATA MASTER' => [
            'icon'   => 'fa-solid fa-database',
            'items'  => [
                [
                    'label'   => 'Sekolah',
                    'icon'    => 'fa-solid fa-school',
                    'route'   => 'admin.schools.index',
                    'routeIs' => 'admin.schools.*',
                ],
                [
                    'label'   => 'Jurusan',
                    'icon'    => 'fa-solid fa-graduation-cap',
                    'route'   => 'admin.majors.index',
                    'routeIs' => 'admin.majors.*',
                ],
                [
                    'label'   => 'Periode Pendaftaran',
                    'icon'    => 'fa-solid fa-calendar-day',
                    'route'   => 'admin.periods.index',
                    'routeIs' => 'admin.periods.*',
                ],
            ],
        ],

        'PENGGUNA & SISTEM' => [
            'icon'   => 'fa-solid fa-user-gear',
            'items'  => [
                [
                    'label'   => 'Akun Siswa',
                    'icon'    => 'fa-solid fa-user-graduate',
                    'route'   => 'admin.accounts.index',
                    'routeIs' => 'admin.accounts.*',
                ],
                [
                    'label'   => 'Log Aktivitas',
                    'icon'    => 'fa-solid fa-clock-rotate-left',
                    'route'   => 'admin.activity-logs.index',
                    'routeIs' => 'admin.activity-logs.*',
                ],
                [
                    'label'   => 'Pengaturan',
                    'icon'    => 'fa-solid fa-gear',
                    'route'   => 'admin.settings.edit',
                    'routeIs' => 'admin.settings.*',
                ],
            ],
        ],
    ],
];

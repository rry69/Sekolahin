<!DOCTYPE html>
@props(['title' => null])
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SPMB') }} — {{ $title ?? 'Penerimaan Murid Baru' }}</title>

        <!-- Fonts: Inter (design system EGGPLORE) -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" style="font-family: 'Inter', system-ui, sans-serif; background: #F5F6FA;">
        @php
            // Pola titik halus untuk panel brand (SVG inline, tanpa request tambahan)
            $dotPattern = "background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='28' height='28'%3E%3Ccircle cx='2' cy='2' r='1.4' fill='rgba(255,255,255,0.14)'/%3E%3C/svg%3E\");";
        @endphp
        <div class="min-h-screen flex flex-col lg:flex-row">

            {{-- ===== Panel kiri: brand (Split Screen) ===== --}}
            <aside class="relative hidden lg:flex lg:w-1/2 flex-col justify-between px-14 py-12 text-white overflow-hidden"
                   style="background: linear-gradient(150deg, #4A54C9 0%, #6C78F5 55%, #8B96F5 100%);">
                {{-- pola titik --}}
                <div class="pointer-events-none absolute inset-0" style="{{ $dotPattern }}"></div>
                {{-- blob dekoratif --}}
                <div class="pointer-events-none absolute -top-32 -right-32 h-[420px] w-[420px] rounded-full" style="background: rgba(255,255,255,.08);"></div>
                <div class="pointer-events-none absolute -bottom-24 -left-16 h-[300px] w-[300px] rounded-full" style="background: rgba(255,255,255,.05);"></div>

                <div class="relative z-10 flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl text-lg" style="background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.25);">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <div class="text-lg font-extrabold leading-tight tracking-tight">SPMB</div>
                        <div class="text-[11px] font-semibold uppercase tracking-[0.04em] opacity-80">Penerimaan Murid Baru</div>
                    </div>
                </div>

                <div class="relative z-10 max-w-[480px]">
                    <span class="inline-flex items-center gap-2.5 rounded-full px-4 py-1.5 text-xs font-semibold" style="background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.25);">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full opacity-60" style="background: #7CF5CB;"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full" style="background: #7CF5CB;"></span>
                        </span>
                        Pendaftaran dibuka
                    </span>
                    <h1 class="mt-5 text-[34px] font-extrabold leading-[1.2] tracking-tight">Mulai perjalanan pendidikan putra-putri Anda</h1>
                    <p class="mt-4 max-w-[420px] text-[15px] leading-relaxed opacity-90">Kelola pendaftaran, pantau status berkas, dan lakukan pembayaran dalam satu tempat. Mudah, transparan, dan terpercaya.</p>

                    <div class="mt-8 flex flex-col gap-4">
                        <div class="flex items-center gap-3.5 text-sm font-medium">
                            <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-[10px] text-[15px]" style="background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.2);"><i class="fa-solid fa-file-lines"></i></span>
                            Pendaftaran online tanpa antre
                        </div>
                        <div class="flex items-center gap-3.5 text-sm font-medium">
                            <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-[10px] text-[15px]" style="background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.2);"><i class="fa-solid fa-magnifying-glass-chart"></i></span>
                            Pantau status berkas real-time
                        </div>
                        <div class="flex items-center gap-3.5 text-sm font-medium">
                            <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-[10px] text-[15px]" style="background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.2);"><i class="fa-solid fa-shield-halved"></i></span>
                            Data aman &amp; terenkripsi
                        </div>
                    </div>

                    {{-- Mockup kartu status berkas: preview mini dashboard bergaya glassmorphism --}}
                    <div aria-hidden="true" class="pointer-events-none absolute right-[-40px] top-24 hidden xl:block w-[300px] rotate-2 rounded-2xl p-5"
                         style="background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.25); backdrop-filter: blur(10px); box-shadow: 0 24px 48px -20px rgba(30,27,75,.45);">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-[0.08em] opacity-85">Status Berkas</span>
                            <i class="fa-solid fa-magnifying-glass-chart text-sm opacity-85"></i>
                        </div>
                        <div class="mt-4 flex items-center gap-3 rounded-xl px-3 py-2.5" style="background: rgba(255,255,255,.14);">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-extrabold" style="background: rgba(255,255,255,.22);">AR</span>
                            <div class="flex-1">
                                <div class="text-[13px] font-semibold leading-tight">Aulia Rahman</div>
                                <div class="text-[11px] opacity-80">No. Pendaftaran SPMB-2026-0042</div>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-bold" style="background: #DFF9EE; color: #157A57;">Terverifikasi</span>
                        </div>
                        <div class="mt-3 space-y-2.5">
                            <div class="flex items-center gap-2.5 text-[12px] font-medium opacity-95">
                                <i class="fa-solid fa-circle-check" style="color:#7CF5CB;"></i> Berkas diterima panitia
                            </div>
                            <div class="flex items-center gap-2.5 text-[12px] font-medium opacity-95">
                                <i class="fa-solid fa-circle-check" style="color:#7CF5CB;"></i> Verifikasi dokumen selesai
                            </div>
                            <div class="flex items-center gap-2.5 text-[12px] font-medium opacity-80">
                                <i class="fa-regular fa-circle"></i> Pengumuman hasil seleksi
                            </div>
                        </div>
                        <div class="mt-4 h-1.5 w-full overflow-hidden rounded-full" style="background: rgba(255,255,255,.18);">
                            <div class="h-full w-2/3 rounded-full" style="background: #7CF5CB;"></div>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 flex items-center gap-4 text-xs opacity-75">
                    <span>&copy; {{ date('Y') }} SPMB</span>
                    <span class="h-3.5 w-px" style="background: rgba(255,255,255,.35);"></span>
                    <span>Dukungan: halo@spmb.sch.id</span>
                    <span class="h-3.5 w-px" style="background: rgba(255,255,255,.35);"></span>
                    <a href="#" class="hover:underline">Bantuan</a>
                </div>
            </aside>

            {{-- ===== Panel kanan: konten ===== --}}
            <main class="flex flex-1 items-center justify-center px-6 py-12" style="background: #F5F6FA;">
                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </main>
        </div>

        @stack('scripts')
    </body>
</html>

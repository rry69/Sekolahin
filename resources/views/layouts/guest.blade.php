<!DOCTYPE html>
@props(['title' => null])
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SPMB') }} — {{ $title ?? 'Penerimaan Murid Baru' }}</title>

        <!-- Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            // === Ikon HugeIcons (JS) — konsisten dengan dashboard admin ===
            window.__HI = @json(\App\Support\Hi::all());
            window.__HI_MAP = @json(config('hugeicons'));
            function hiSvg(name, attr) {
                var key = name || '';
                if (!(key in (window.__HI || {}))) {
                    key = (window.__HI_MAP || {})[key] || '';
                }
                var body = (window.__HI || {})[key] || '';
                if (!body) return '';
                var a = attr ? ' ' + attr : '';
                return '<svg class="hi" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"' + a + '>' + body + '</svg>';
            }
            function hiHtml(name, attr) { return hiSvg(name, attr); }
        </script>
    </head>
    <body class="font-sans antialiased" style="font-family: 'Inter', system-ui, sans-serif; background: #F4F5FB;">
        <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10 sm:px-6">
            {{-- ===== Soft gradient blob background (gaya Bringova) ===== --}}
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div class="absolute -top-24 -left-24 h-[420px] w-[420px] rounded-full"
                     style="background: radial-gradient(circle at 30% 30%, rgba(255,107,107,.35), transparent 70%); filter: blur(60px);"></div>
                <div class="absolute top-1/3 -right-32 h-[460px] w-[460px] rounded-full"
                     style="background: radial-gradient(circle at 60% 40%, rgba(255,165,120,.38), transparent 70%); filter: blur(60px);"></div>
                <div class="absolute -bottom-24 left-1/4 h-[420px] w-[420px] rounded-full"
                     style="background: radial-gradient(circle at 50% 50%, rgba(130,150,255,.30), transparent 70%); filter: blur(60px);"></div>
                <div class="absolute top-8 left-1/2 h-[260px] w-[260px] -translate-x-1/2 rounded-full"
                     style="background: radial-gradient(circle at 50% 50%, rgba(255,210,140,.28), transparent 70%); filter: blur(50px);"></div>
            </div>

            {{-- ===== Konten (centered, kartu hanya outline tipis) ===== --}}
            <div class="relative z-10 w-full max-w-md">
                {{-- Branding: logo + judul --}}
                <div class="mb-7 flex flex-col items-center text-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl text-white shadow-lg"
                         style="background: linear-gradient(135deg, #FF6B6B, #FF8E6E); box-shadow: 0 10px 24px -8px rgba(255,107,107,.55);">
                        <x-hi icon="fa-graduation-cap" class="text-xl" />
                    </div>
                    <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-gray-900">Sekolahin</h1>
                    <p class="text-xs font-semibold uppercase tracking-[0.05em] text-gray-400">Penerimaan Murid Baru</p>
                </div>

                {{-- Kartu outline tipis (tanpa isian putih) --}}
                <div class="rounded-3xl border border-gray-200/70 bg-white/10 p-8 sm:p-10 backdrop-blur-sm">
                    <div class="auth-shell w-full">
                        {{ $slot }}
                    </div>
                </div>

                <p class="mt-6 text-center text-xs text-gray-400">
                    &copy; {{ date('Y') }} Sekolahin. Semua hak dilindungi.
                </p>
            </div>
        </div>

        {{-- Override terarah untuk halaman auth lain yang memakai komponen global berwarna gelap,
             agar konsisten terbaca di atas background terang. --}}
        <style>
            .auth-shell input[type="email"],
            .auth-shell input[type="password"],
            .auth-shell input[type="text"] {
                border-color: #e5e7eb !important;
                background-color: #ffffff !important;
                color: #111827 !important;
                border-radius: .75rem !important;
            }
            .auth-shell input[type="email"]:focus,
            .auth-shell input[type="password"]:focus,
            .auth-shell input[type="text"]:focus {
                border-color: #FF6B6B !important;
                background-color: #ffffff !important;
                --tw-ring-color: rgba(255,107,107,.15) !important;
            }
            .auth-shell button.bg-gray-800,
            .auth-shell button.bg-gray-900 {
                background: linear-gradient(135deg, #FF6B6B, #FF8E6E) !important;
                color: #ffffff !important;
                border-radius: .75rem !important;
                box-shadow: 0 10px 20px -8px rgba(255,107,107,.6) !important;
            }
            .auth-shell button.bg-gray-800:hover,
            .auth-shell button.bg-gray-900:hover { background: linear-gradient(135deg, #FF5B5B, #FF7E5E) !important; }
        </style>

        @stack('scripts')
    </body>
</html>

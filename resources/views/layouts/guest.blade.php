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
                   style="background: #4A54C9;">
                {{-- Layered aura gradient (teknik auragradients.com, re-tint palet EGGPLORE) --}}
                <div class="pointer-events-none absolute -inset-10" style="background: linear-gradient(160deg, rgba(139,150,245,.55) 0%, rgba(255,255,255,.22) 22%, rgba(90,100,232,.45) 60%, rgba(58,66,168,.65) 100%); mix-blend-mode: hard-light; filter: blur(28px);"></div>
                <div class="pointer-events-none absolute -inset-10" style="background: radial-gradient(circle at 78% 18%, rgba(255,255,255,.5), transparent 46%), radial-gradient(circle at 12% 85%, rgba(108,120,245,.7), transparent 55%); mix-blend-mode: soft-light; filter: blur(20px);"></div>
                {{-- pola titik --}}
                <div class="pointer-events-none absolute inset-0" style="{{ $dotPattern }}"></div>

                <div class="relative z-10 flex items-center gap-3">
                    <div>
                        <div class="text-lg font-extrabold leading-tight tracking-tight">Sekolahin</div>
                        <div class="text-[11px] font-semibold uppercase tracking-[0.04em] opacity-80">Penerimaan Murid Baru</div>
                    </div>
                </div>

                <div class="relative z-10 max-w-[480px]">
                    @php
                    $periodSma = \App\Models\RegistrationPeriod::where('school_level_id', 4)->where('is_active', true)->orderByDesc('updated_at')->first();
                    $periodeTeks = $periodSma
                        ? 'PPDB '.$periodSma->name.' · '.\Carbon\Carbon::parse($periodSma->start_date)->translatedFormat('d M').' – '.\Carbon\Carbon::parse($periodSma->end_date)->translatedFormat('d M Y')
                        : 'PPDB 2026/2027';
                @endphp
                <p class="text-[13px] font-semibold uppercase tracking-[0.14em] opacity-75">{{ $periodeTeks }}</p>
                    <h1 class="mt-4 text-[36px] font-extrabold leading-[1.15] tracking-tight">Satu pintu untuk<br>pendaftaran <span class="font-light italic">anak Anda</span></h1>
                    <p class="mt-4 max-w-[420px] text-[15px] leading-relaxed opacity-90">Daftar, unggah berkas, dan pantau setiap tahapan sampai pengumuman &mdash; tanpa perlu datang ke loket.</p>

                    {{-- Angka fakta dari database: bukti, bukan klaim --}}
                    @php
                        $jumlahPendaftar = $periodSma ? \App\Models\Registration::whereHas('registrationPeriod', fn ($q) => $q->whereKey($periodSma->id))->count() : 0;
                        $dayaTampung = (int) \App\Models\Major::where('school_level_id', 4)->sum('quota');
                        $jumlahJurusan = \App\Models\Major::where('school_level_id', 4)->count();
                    @endphp
                    <dl class="mt-9 grid grid-cols-3 gap-6 border-t pt-7" style="border-color: rgba(255,255,255,.22);">
                        <div>
                            <dt class="text-[11px] font-semibold uppercase tracking-[0.08em] opacity-70">Kuota tersedia</dt>
                            <dd class="mt-1.5 text-2xl font-bold tabular-nums">{{ number_format(max($dayaTampung - $jumlahPendaftar, 0), 0, ',', '.') }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-semibold uppercase tracking-[0.08em] opacity-70">Total kursi</dt>
                            <dd class="mt-1.5 text-2xl font-bold tabular-nums">{{ number_format($dayaTampung, 0, ',', '.') }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-semibold uppercase tracking-[0.08em] opacity-70">Pilihan jurusan</dt>
                            <dd class="mt-1.5 text-2xl font-bold tabular-nums">{{ $jumlahJurusan }}</dd>
                        </div>
                    </dl>

                                        {{-- Cara daftar: 3 langkah bernomor --}}
                    <ol class="mt-9 space-y-3">
                        <li class="flex items-baseline gap-3.5 text-sm">
                            <span class="text-xs font-bold tabular-nums opacity-60">01</span>
                            <span><span class="font-semibold">Buat akun dan isi formulir</span> &mdash; selesai dalam 10 menit</span>
                        </li>
                        <li class="flex items-baseline gap-3.5 text-sm">
                            <span class="text-xs font-bold tabular-nums opacity-60">02</span>
                            <span><span class="font-semibold">Panitia memverifikasi berkas</span> &mdash; status dapat dipantau kapan pun</span>
                        </li>
                        <li class="flex items-baseline gap-3.5 text-sm">
                            <span class="text-xs font-bold tabular-nums opacity-60">03</span>
                            <span><span class="font-semibold">Pengumuman hasil seleksi</span> &mdash; diberitahukan lewat akun Anda</span>
                        </li>
                    </ol>

                </div>

                <div class="relative z-10 flex items-center gap-4 text-xs opacity-75">
                    <span>&copy; {{ date('Y') }} Sekolahin</span>
                    <span class="h-3.5 w-px" style="background: rgba(255,255,255,.35);"></span>
                    <a href="https://github.com/rry69" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 hover:underline">
                        <i class="fa-brands fa-github"></i> Profil GitHub
                    </a>
                    <span class="h-3.5 w-px" style="background: rgba(255,255,255,.35);"></span>
                    <a href="https://github.com/rry69/Sekolahin" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 hover:underline">
                        <i class="fa-solid fa-code-fork"></i> Sekolahin
                    </a>
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

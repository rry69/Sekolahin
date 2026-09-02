@php
  $code = $code ?? ($exception->getStatusCode() ?? 500);
  $map = [
    400 => ['title'=>'Permintaan Tidak Valid','desc'=>'Data yang kamu kirim tidak bisa diproses. Coba periksa kembali isian form.','pill'=>'Bad Request','ic'=>'alert-triangle','color'=>'amber'],
    401 => ['title'=>'Belum Masuk','desc'=>'Kamu harus login dulu untuk mengakses halaman ini.','pill'=>'Unauthorized','ic'=>'lock-key','color'=>'amber'],
    403 => ['title'=>'Akses Ditolak','desc'=>'Kamu tidak punya izin untuk membuka halaman ini. Hubungi admin jika ini keliru.','pill'=>'Forbidden','ic'=>'shield-cross','color'=>'red'],
    404 => ['title'=>'Halaman Tidak Ditemukan','desc'=>'Sepertinya halaman yang kamu cari sudah dipindah atau tidak pernah ada.','pill'=>'Not Found','ic'=>'search-x','color'=>'coral'],
    419 => ['title'=>'Sesi Berakhir','desc'=>'Sesi kamu sudah kadaluarsa. Silakan muat ulang dan coba lagi.','pill'=>'Expired','ic'=>'clock-refresh','color'=>'amber'],
    422 => ['title'=>'Data Tidak Valid','desc'=>'Ada isian yang belum sesuai. Periksa kembali form kamu.','pill'=>'Unprocessable','ic'=>'file-x','color'=>'amber'],
    429 => ['title'=>'Terlalu Banyak Permintaan','desc'=>'Kamu mengirim terlalu banyak permintaan. Tunggu sebentar lalu coba lagi.','pill'=>'Too Many Requests','ic'=>'hourglass','color'=>'purple'],
    500 => ['title'=>'Gangguan Server','desc'=>'Ada yang tidak beres di sisi kami. Tim sudah diberitahu — coba lagi beberapa saat.','pill'=>'Server Error','ic'=>'server-error','color'=>'red'],
    503 => ['title'=>'Sedang Pemeliharaan','desc'=>'Sistem sedang dalam pemeliharaan singkat. Kami segera kembali.','pill'=>'Maintenance','ic'=>'wrench','color'=>'blue'],
  ];
  $info = $map[$code] ?? $map[500];
  $title = $title ?? $info['title'];
  $desc  = $desc ?? $info['desc'];
  $pill  = $pill ?? $info['pill'];
  $ic    = $ic ?? $info['ic'];
  $color = $color ?? $info['color'];
  $home = auth()->check() ? route('dashboard') : route('login');
  $prev = url()->previous() !== url()->current() ? url()->previous() : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $code }} — {{ $title }} · {{ config('app.name','Sekolahin') }}</title>
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="icon" type="image/png" href="{{ asset('images/web_logo.png') }}">
@vite(['resources/css/app.css','resources/js/app.js'])
<style>
  :root{--coral:#FF6B6B;--coral-2:#FF8E6E;--coral-soft:#FFE5E3;--amber:#F59E0B;--amber-soft:#FEF3C7;--green:#10B981;--green-soft:#D1FAE5;--blue:#3B82F6;--blue-soft:#DBEAFE;--purple:#8B5CF6;--purple-soft:#EDE9FE;--red:#EF4444;--red-soft:#FEE2E2;--gray:#6b7280;--gray-soft:#F3F4F6;--ink:#1a1a2e;--muted:#8a8f9d;--divider:rgba(26,26,46,.10)}
  *{font-family:'Inter',system-ui,sans-serif}
  .err-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:32px 16px;position:relative;overflow:hidden;background:#F4F5FB}
  .err-card{position:relative;z-index:1;width:100%;max-width:520px;text-align:center}
  .err-logo{display:flex;flex-direction:column;align-items:center;gap:8px;margin-bottom:22px}
  .err-code{font-size:76px;font-weight:800;letter-spacing:-0.04em;line-height:1;color:var(--ink)}
  .err-code span{background:linear-gradient(135deg,var(--coral),var(--coral-2));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
  .err-pill{display:inline-flex;align-items:center;gap:7px;padding:5px 12px;border-radius:20px;font:700 11px var(--ink);letter-spacing:.04em;text-transform:uppercase}
  .err-pill.coral{background:var(--coral-soft);color:var(--coral)}
  .err-pill.red{background:var(--red-soft);color:var(--red)}
  .err-pill.amber{background:var(--amber-soft);color:#b45309}
  .err-pill.blue{background:var(--blue-soft);color:var(--blue)}
  .err-pill.purple{background:var(--purple-soft);color:var(--purple)}
  .err-pill.green{background:var(--green-soft);color:var(--green)}
  .err-title{margin-top:14px;font:800 22px/1.2 var(--ink);letter-spacing:-.02em}
  .err-desc{margin-top:8px;font:400 13.5px/1.6 var(--muted);max-width:420px;margin-left:auto;margin-right:auto}
  .err-actions{margin-top:22px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
  .err-btn{display:inline-flex;align-items:center;gap:8px;border:none;cursor:pointer;border-radius:11px;padding:11px 18px;font:700 13px;text-decoration:none;transition:transform .15s,filter .15s,background .15s}
  .err-btn:hover{transform:translateY(-1px)}
  .err-btn.coral{background:linear-gradient(135deg,var(--coral),var(--coral-2));color:#fff;box-shadow:0 10px 20px -10px rgba(255,107,107,.7)}
  .err-btn.ghost{background:rgba(255,255,255,.75);color:var(--ink);border:1px solid rgba(26,26,46,.08)}
  .err-btn.ghost:hover{background:#fff;color:var(--coral)}
  .err-meta{margin-top:18px;font:500 11.5px var(--muted)}
  .err-meta code{font:600 11.5px ui-monospace,monospace;background:rgba(255,255,255,.7);border:1px solid rgba(26,26,46,.08);padding:2px 7px;border-radius:6px;color:var(--ink)}
  .err-ic{width:64px;height:64px;border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:22px}
  .err-ic.coral{background:var(--coral-soft);color:var(--coral)}
  .err-ic.red{background:var(--red-soft);color:var(--red)}
  .err-ic.amber{background:var(--amber-soft);color:#b45309}
  .err-ic.blue{background:var(--blue-soft);color:var(--blue)}
  .err-ic.purple{background:var(--purple-soft);color:var(--purple)}
  .err-ic.green{background:var(--green-soft);color:var(--green)}
  .err-foot{margin-top:22px;font:400 11.5px var(--muted)}
</style>
</head>
<body>
<div class="err-wrap">
  {{-- blobs Bringova --}}
  <div class="pointer-events-none absolute inset-0" aria-hidden="true">
    <div class="absolute -top-24 -left-24 h-[420px] w-[420px] rounded-full" style="background:radial-gradient(circle at 30% 30%, rgba(255,107,107,.30), transparent 70%);filter:blur(60px)"></div>
    <div class="absolute top-1/3 -right-32 h-[460px] w-[460px] rounded-full" style="background:radial-gradient(circle at 60% 40%, rgba(255,165,120,.32), transparent 70%);filter:blur(60px)"></div>
    <div class="absolute -bottom-24 left-1/4 h-[420px] w-[420px] rounded-full" style="background:radial-gradient(circle at 50% 50%, rgba(130,150,255,.26), transparent 70%);filter:blur(60px)"></div>
  </div>

  <div class="err-card">
    <div class="err-logo">
      <img src="{{ asset('images/web_logo.png') }}" alt="Sekolahin" class="h-12 w-12 object-contain">
      <img src="{{ asset('images/logo_text.png') }}" alt="Sekolahin" class="h-6 w-auto max-w-[180px] object-contain">
    </div>

    <div class="err-ic {{ $color }}"><i class="fa-solid fa-{{ $code==404?'map-location-dot':($code==403?'shield-halved':($code==500||$code==503?'triangle-exclamation':($code==401?'right-to-bracket':'circle-exclamation'))) }}"></i></div>
    <div class="err-code"><span>{{ $code }}</span></div>
    <div style="margin-top:10px"><span class="err-pill {{ $color }}"><i class="fa-solid fa-circle" style="font-size:6px"></i> {{ $code }} · {{ $pill }}</span></div>
    <h1 class="err-title">{{ $title }}</h1>
    <p class="err-desc">{{ $desc }}</p>

    @if(app()->hasDebugModeEnabled() && isset($exception) && $exception->getMessage())
      <p class="err-meta"><code>{{ \Illuminate\Support\Str::limit($exception->getMessage(), 160) }}</code></p>
    @endif

    <div class="err-actions">
      @if($prev)
        <a href="{{ $prev }}" class="err-btn ghost"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
      @endif
      <a href="{{ $home }}" class="err-btn coral"><i class="fa-solid fa-house"></i> Ke Beranda</a>
      <button onclick="location.reload()" class="err-btn ghost"><i class="fa-solid fa-rotate"></i> Muat Ulang</button>
    </div>

    <p class="err-foot">&copy; {{ date('Y') }} Sekolahin — Penerimaan Murid Baru &middot; <span style="color:var(--muted)">Kode rujukan: <code style="font:600 11px ui-monospace,monospace">{{ request()->path() }}</code></span></p>
  </div>
</div>
</body>
</html>

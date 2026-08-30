@props([
    'icon' => null,      // nama HugeIcons (mis. 'search-01') ATAU kelas Font Awesome ('fa-solid fa-search')
    'fa' => null,        // pintasan: kelas Font Awesome
    'name' => null,      // pintasan: nama HugeIcons
    'class' => null,     // class tambahan pada <svg>
    'size' => null,      // ukuran px / em (opsional, override)
])

@php
    $key = $name ?? $icon ?? $fa;
    $iconName = \App\Support\Hi::name($key);
    $body = \App\Support\Hi::body($iconName);
    $classes = trim('hi ' . ($class ?? ''));
    $sizeStyle = $size ? 'width:'.$size.';height:'.$size.';' : '';
    $attrs = $attributes->merge(['class' => $classes]);
    if ($sizeStyle) {
        $attrs = $attrs->merge(['style' => trim($attrs->get('style') . ';' . $sizeStyle, ';')]);
    }
@endphp

@if ($body)
<svg {{ $attrs }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $body !!}</svg>
@else
{{-- fallback: teks (jangan diam-diam hilang) --}}
<span class="hi-fallback {{ $classes }}" {{ $attributes->except('class') }}>{{ $key }}</span>
@endif

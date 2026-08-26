@props(['label', 'value' => null, 'mono' => false, 'wide' => false])

<div {{ $attributes->merge(['class' => ($wide ? 'sm:col-span-2 ' : '') . 'min-w-0']) }}>
    <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-eggplore-neutral-400">{{ $label }}</p>
    @if ($slot->isNotEmpty())
        <div class="mt-1 text-sm leading-relaxed">{{ $slot }}</div>
    @else
        <p class="mt-1 text-sm {{ $mono ? 'font-mono' : 'font-medium' }} {{ $value ? 'text-eggplore-neutral-900' : 'text-eggplore-neutral-300' }}">
            {{ $value ?: '—' }}
        </p>
    @endif
</div>

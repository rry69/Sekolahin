@props(['type' => 'registration', 'status' => null, 'label' => null])

@php
    $badge = $type === 'payment'
        ? \App\Support\StatusBadge::paymentStatusCard($status)
        : \App\Support\StatusBadge::registrationStatusCard($status);
    $text = $label ?? $badge['label'];
    $cls  = $attributes->get('class', '');
@endphp

<span {{ $attributes->merge(['class' => 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . trim($badge['cls'] . ' ' . $cls)]) }}>
    @if (!empty($badge['icon']))
        <x-hi :name="$badge['icon']" class="mr-1.5 self-center text-[11px]" />
    @endif
    {{ $text }}
</span>
